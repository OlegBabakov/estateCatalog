<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 06.12.16
 * Time: 12:54
 */

namespace CatalogBundle\Form\Estate;


use CatalogBundle\Classes\Enum\EstateEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class EstateDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rooms', RoomsType::class, [
                'label' => 'estate_rooms',
                'required' => false,
            ])
            ->add('area', AreaType::class, [
                'label' => 'estate_area',
                'required' => false,
            ])
            ->add('facilities', ChoiceType::class, [
                'label' => 'estate_facilities',
                'choices' => EstateEnum::getFacilities(),
                'multiple' => true,
                'attr' => [
                    'class' => 'multiselect'
                ]
            ])
            ->add('floor', NumberType::class, [
                'label' => 'estate_floor',
            ])
            ->add('building', BuildingType::class, [
                'label' => 'building_info',
            ])

        ;
    }
}