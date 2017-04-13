<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 28.11.16
 * Time: 9:54
 */

namespace CatalogBundle\Classes\Search;


use Symfony\Component\HttpFoundation\Request;

class EstateSearchCriteria
{
    /**
     * @var array , example ['min' => 0 , 'max' => 500]
     */
    public $price = [
        'min' => null, //int
        'max' => null  //int
    ];

    public $area = [
        'total' => [
            'min' => null,
            'max' => null
        ],
        'living' => [
            'min' => null,
            'max' => null
        ],
        'kitchen' => [
            'min' => null,
            'max' => null
        ]
    ];

    public $floor = [
        'min' => null,
        'max' => null
    ];

    public $building = [
        'year' => [
            'min' => null,
            'max' => null
        ]
    ];

    /**
     * @var array , example ['apartment', 'house']
     */
    public $estateType = [];
    /**
     * @var string, values: 'buy'/'rent'
     */
    public $contract;

    /**
     * @var array , example [2,3,'>3']
     */
    public $bedrooms = [];

    /**
     * @var array , example ['24security', 'fridge', ...] see EstateEnum
     */
    public $facilities = [];

    /**
     * @var boolean
     */
    public $withPhotos = false;

    /**
     * @var array , array of UserBundle\Entity\User->id
     */
    public $creators = null;

    /**
     * Прослойка для того чтобы обрабатывать приходящие данные с разных форм (параметры с разными префиксамИ)
     * @param Request $request
     * @return array
     */
    private static function getRequestParameters(Request $request) {
        return $request->query->all();
    }

    /**
     * @param Request $request
     * @return EstateSearchCriteria
     */
    public static function buildFromRequest(Request $request) {
        $instance = new EstateSearchCriteria();

        self::handleSubdomain($instance, $request);

        $requestParams = self::getRequestParameters($request);
        self::fillYearFields($instance, $requestParams);

        foreach ($requestParams as $key => $value) {
            switch ($key) {
                case 'priceMin': $instance->price['min'] = is_numeric($value) ? (int)$value : null; break;
                case 'priceMax': $instance->price['max'] = is_numeric($value) ? (int)$value : null; break;
                case 'contract':
                    $instance->{$key} = $value ? : null;
                    break;
                case 'bedrooms':
                case 'estateType':
                case 'facilities':
                    $instance->{$key}   = array_merge(
                        $instance->{$key},
                        explode(',', $requestParams[$key])
                    );
                    break;
                case 'withPhotos': $instance->withPhotos = $value == '1'; break;
                case 'seaView': if ($value == '1') $instance->facilities[] = 'seaview'; break;
                case 'areaTotalMin'  : $instance->area['total']['min']      = is_numeric($value) ? (int)$value : null; break;
                case 'areaTotalMax'  : $instance->area['total']['max']      = is_numeric($value) ? (int)$value : null; break;
                case 'areaLivingMin' : $instance->area['living']['min']     = is_numeric($value) ? (int)$value : null; break;
                case 'areaLivingMax' : $instance->area['living']['max']     = is_numeric($value) ? (int)$value : null; break;
                case 'areaKitchenMin': $instance->area['kitchen']['min']    = is_numeric($value) ? (int)$value : null; break;
                case 'areaKitchenMax': $instance->area['kitchen']['max']    = is_numeric($value) ? (int)$value : null; break;
                case 'floorMin'      : $instance->floor['min'] = is_numeric($value) ? (int)$value : null; break;
                case 'floorMax'      : $instance->floor['max'] = is_numeric($value) ? (int)$value : null; break;
            }
        }

        return $instance;
    }

    private static function fillYearFields(EstateSearchCriteria $searchCriteria, $requestParams) {
        if (!isset($requestParams['buildYear'])) return false;

        if (is_numeric($requestParams['buildYear'])) {
            $searchCriteria->building['year']['min'] = $requestParams['buildYear'];
            $searchCriteria->building['year']['max'] = $requestParams['buildYear'];
        }

        elseif ('interval' === $requestParams['buildYear']) {
            $searchCriteria->building['year']['min'] = $requestParams['buildYearMin'] ?? null;
            $searchCriteria->building['year']['max'] = $requestParams['buildYearMax'] ?? null;
        }

        elseif (false !== strpos($requestParams['buildYear'], 'after')) {
            $requestParams['buildYear'] = str_replace('after','', $requestParams['buildYear']);
            $searchCriteria->building['year']['min'] = $requestParams['buildYear'];
        }

        elseif (false !== strpos($requestParams['buildYear'], 'before')) {
            $requestParams['buildYear'] = str_replace('before','', $requestParams['buildYear']);
            $searchCriteria->building['year']['max'] = $requestParams['buildYear'];
        }

        $searchCriteria->building['year']['min'] = is_numeric($searchCriteria->building['year']['min']) ? (int)$searchCriteria->building['year']['min'] : null;
        $searchCriteria->building['year']['max'] = is_numeric($searchCriteria->building['year']['max']) ? (int)$searchCriteria->building['year']['max'] : null;

        return true;
    }

    private static function handleSubdomain(EstateSearchCriteria $instance, Request $request) {
        $hostParts = explode('.', $request->getHost());
        $subdomain = (isset($hostParts[0]) && count($hostParts) == 3) ? $hostParts[0] : null;

        if ($subdomain === 'zimavteple')
            $instance->creators[] = 6; /*TODO: Продумать более гибкий механизм (установка субдомена в настройках организации или профиля)**/
    }

}