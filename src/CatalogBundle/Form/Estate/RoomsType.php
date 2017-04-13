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

class RoomsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bedroom', NumberType::class, [ //Qty of bedrooms
                'label' => 'estate_rooms_bedroom'
            ])
            ->add('bathroom', NumberType::class, [ //Qty of baths
                'label' => 'estate_rooms_bath'
            ])
            ->add('parking', NumberType::class, [ //Qty of parking spots
                'label' => 'estate_rooms_parking'
            ])
        ;
    }

}