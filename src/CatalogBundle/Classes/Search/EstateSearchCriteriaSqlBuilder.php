<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 26.12.16
 * Time: 10:45
 */

namespace CatalogBundle\Classes\Search;


use CatalogBundle\Classes\Enum\EstateEnum;

class EstateSearchCriteriaSqlBuilder
{
    /**@var \CatalogBundle\Classes\Search\QueryBuilder*/
    private $qb;

    public function setSqlConditions(QueryBuilder $qb, EstateSearchCriteria $searchCriteria) {
        $this->qb = $qb;

        $qb->addOpenBracket();
        if ($searchCriteria->contract == 'buy' || is_null($searchCriteria->contract)) {
            $qb->addOpenBracket();
            if ($searchCriteria->price['min'])
                $qb->addCondition('es.price_sell >= :priceMin');
            if ($searchCriteria->price['max'])
                $qb->addCondition('es.price_sell <= :priceMax');
            if ($searchCriteria->contract && is_null($searchCriteria->price['max']) && is_null($searchCriteria->price['min']))
                $qb->addCondition('es.price_sell IS NOT NULL', null);
            $qb->addCloseBracket();
        }

        if ($searchCriteria->contract == 'rent' || is_null($searchCriteria->contract)) {
            if ($searchCriteria->price['min'] || $searchCriteria->price['max'] || $searchCriteria->contract)
                $qb->addCondition(null, 'OR');

            $qb->addOpenBracket();
            if ($searchCriteria->price['min'])
                $qb->addCondition('es.price_rent >= :priceMin');
            if ($searchCriteria->price['max'])
                $qb->addCondition('es.price_rent <= :priceMax');

            if ($searchCriteria->contract  && is_null($searchCriteria->price['max']) && is_null($searchCriteria->price['min']))
                $qb->addCondition('es.price_rent IS NOT NULL', null);
            $qb->addCloseBracket();
        }
        $qb->addCloseBracket();

        if ($searchCriteria->estateType) {
            $arr = array_map (
                function($item) {
                    return "'{$item}'";
                },
                $searchCriteria->estateType
            );
            $arr = implode(',', $arr);
            $qb->addCondition("es.estate_type IN ({$arr})");
        }

        $this->buildRangeCondition(
            "(es.data->'building'->'construction'->>'year')::integer",
            $searchCriteria->building['year']
        );

        $this->buildRangeCondition(
            "(es.data->'area'->>'total')::float",
            $searchCriteria->area['total']
        );

        $this->buildRangeCondition(
            "(es.data->'area'->>'living')::float",
            $searchCriteria->area['living']
        );

        $this->buildRangeCondition(
            "(es.data->'area'->>'kitchen')::float",
            $searchCriteria->area['kitchen']
        );

        $this->buildRangeCondition(
            "(es.data->>'floor')::integer",
            $searchCriteria->floor
        );

        if ($searchCriteria->bedrooms) {
            $qb->addCondition(null, 'AND');
            $qb->addOpenBracket();
            foreach ($searchCriteria->bedrooms as $quantity) {
                if (false !== strpos($quantity, '+')) {
                    $quantity = str_replace('+', '', $quantity);
                    if (is_numeric($quantity)) $qb->addCondition("es.data->'rooms'->>'bedroom' >= '{$quantity}'", 'OR');
                } else {
                    if (is_numeric($quantity)) $qb->addCondition("es.data->'rooms'->>'bedroom' = '{$quantity}'", 'OR');
                }
            }
            $qb->addCloseBracket();
        }

        if ($searchCriteria->facilities) {
            $allowedValues = array_values(EstateEnum::getFacilities());
            $arr = [];
            foreach ($searchCriteria->facilities as $facility) {
                if (in_array($facility, $allowedValues, true)) $arr[] = $facility;
            }
            $arr = array_map (
                function($item) {
                    return "\"{$item}\"";
                },
                $arr
            );
            $arr = implode(',', $arr);
            $qb->addCondition("es.data->'facilities' @> '[{$arr}]'");
        }

        if ($searchCriteria->creators) {
            $qb->addCondition();
            $qb->addOpenBracket();
            foreach ($searchCriteria->creators as $creator) {
                $qb->addCondition("es.creator_id = $creator", 'OR');
            }
            $qb->addCondition("es.estate_type = 'building'", 'OR');
            $qb->addCloseBracket();
        }

        $qb->addCondition('es.tree_level > 0');

        if ($searchCriteria->withPhotos) {
            $qb->addJoin('LEFT JOIN estate_media em ON em.estate = es.id');
            $qb->setAfterWhere('
                GROUP BY es.id
                HAVING COUNT(es.id) > 0
            ');
        }
    }

    /**
     * Составляет условие для проверки вхождения значения в диапазон (обе границы включены)
     * @param $field
     * @param $range , example ['min'=> 0 , 'max' => 10]
     */
    private function buildRangeCondition($field, array $range) {
        if (isset($range['min']) && $range['min'])
            $this->qb->addCondition("{$field} >= {$range['min']}");

        if (isset($range['max']) && $range['max'])
            $this->qb->addCondition("{$field} <= {$range['max']}");
    }

}