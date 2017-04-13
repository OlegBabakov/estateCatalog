<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 23.11.16
 * Time: 9:40
 */

namespace CatalogBundle\Form\Estate;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class AreaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('total', NumberType::class, [ //Total estate area
                'label' => 'estate_area_total'
            ])
            ->add('living', NumberType::class, [ //Living area
                'label' => 'estate_area_living'
            ])
            ->add('kitchen', NumberType::class, [ //Kitchen area
                'label' => 'estate_area_kitchen'
            ])
        ;
    }

}