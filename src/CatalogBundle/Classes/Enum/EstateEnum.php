<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 06.12.16
 * Time: 13:09
 */

namespace CatalogBundle\Classes\Enum;


class EstateEnum
{
    public static function getFacilities() {
        $facilities = [
            'security24',
            'aircond',
            'fridge',
            'fan',
            'bed',
            'balcony',
            'bathtub',
            'bar',
            'sofa',
            'tv',
            'cable_internet',
            'wifi',
            'dinning_table',
            'washing_machine',
            'dishwashing_machine',
            'microwave',
            'kitchen_utensil',
            'seaview',
            'pool',
            'gym'
        ];
        $output = [];
        foreach ($facilities as $facility) {
            $output['facilities_'.$facility] = $facility;
        }
        return $output;
    }

    public static function getEstateTypes() {
        $typeList = [
            'apartment',
            'house',
            'hotel',
            'land',
            'office',
            'restaurant',
            'building',
//                    'Villas'      => 'villa'
        ];
        $output = [];
        foreach ($typeList as $type) {
            $output['estate_type_'.$type] = $type;
        }
        return $output;
    }
}