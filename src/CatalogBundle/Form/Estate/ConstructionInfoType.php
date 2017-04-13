<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 14.12.16
 * Time: 13:44
 */

namespace CatalogBundle\Form\Estate;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class ConstructionInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $yearChoices = [];
        for ($year = date('Y')+5; $year > date('Y')-16; $year-- ) {
            $yearLabel = date('Y') == $year ? $yearLabel = "<{$year}>" : $year;
            $yearChoices[$yearLabel] = $year;
        }

        $builder
            ->add('year', ChoiceType::class, [
                'label' => 'construction_year',
                'required' => false,
                'choices' => $yearChoices
            ])
            ->add('quarter', ChoiceType::class, [
                'label' => 'construction_quarter',
                'required' => false,
                'choices' => [
                    'Q1' => 1,
                    'Q2' => 2,
                    'Q3' => 3,
                    'Q4' => 4,
                ]
            ])
            ->add('completion_percent', NumberType::class, [
                'label' => 'completion_percent',
            ])
        ;
    }
}
