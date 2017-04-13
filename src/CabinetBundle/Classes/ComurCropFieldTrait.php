<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 19.12.16
 * Time: 19:14
 */

namespace CabinetBundle\Classes;


trait ComurCropFieldTrait
{

    private $comurCropField;

    public function setComurCropField($value) {
        $field = &$this->{$this::COMUR_CROP_TARGET_FIELD};
        $field['comurCropField'] = $value;
        return $this;
    }

    public function getComurCropField() {
        $field = &$this->{$this::COMUR_CROP_TARGET_FIELD};

        return $field['comurCropField'] ?? null;

    }

}