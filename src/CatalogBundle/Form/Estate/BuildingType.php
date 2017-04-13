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

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('floors', NumberType::class, [
                'label' => 'building_floors',
            ])
            ->add('construction', ConstructionInfoType::class, [
                'label' => 'building_info',
            ])
        ;
    }
}