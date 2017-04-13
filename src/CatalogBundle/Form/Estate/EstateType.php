<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 14.12.16
 * Time: 8:57
 */

namespace CatalogBundle\Form\Estate;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use CabinetBundle\Form\MultiLangInputType;
use CatalogBundle\Classes\Enum\EstateEnum;
use CatalogBundle\Form\AddressMapType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'CatalogBundle\Entity\Estate',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, [
                'label' => 'estate_type_building',
                'required' => false,
                'class' => 'CatalogBundle\Entity\Estate',
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('address', AddressMapType::class)
            ->add('estateType', ChoiceType::class, [
                'label' => 'filter_form_property_type',
                'choices' => EstateEnum::getEstateTypes()
            ])
            ->add('priceSell', NumberType::class, [
                'label' => 'price_sell',
                'required' => false,
                'attr' => [
                    'placeholder' => '$, USD'
                ]
            ])
            ->add('priceRent', NumberType::class, [
                'label' => 'price_rent',
                'required' => false,
                'attr' => [
                    'placeholder' => '$, USD'
                ]
            ])
            ->add('title', null, [
                'label' => 'title',
                'required' => false
            ])
            ->add('description', MultiLangInputType::class, [
                'label' => 'description',
                'field_type_class' => TextareaType::class,
                'field_options' => [
                    'required' => false,
                    'attr' => [
                        'class' => 'wysiwyg'
                    ],
                ]
            ])
            //->add('description', TranslationsType::class)
            ->add('data', EstateDataType::class, [
                'label' => 'estate_data',
                'required' => false,
                'attr' => [
                    'class' => 'estate-data-form'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save',
                'attr' => [
                    'class' => 'btn-primary'
                ]
            ])
        ;
    }
}