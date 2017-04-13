<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 23.11.16
 * Time: 9:40
 */

namespace CatalogBundle\Form;


use CatalogBundle\Classes\Enum\EstateEnum;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapFilterType extends AbstractType
{
    private $container;

    private $translations = [];  // ['id' => 'translated']

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'container' => null
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->container = $options['container'] ?? null;
        if (!$this->container instanceof ContainerInterface) throw new \Exception('Не передан контейнер');

        $this->loadTranslations();

        $facilitiesEnum = EstateEnum::getFacilities();
        unset($facilitiesEnum['facilities_seaview']);

        $this->buildYearFields($builder);
        $builder
            ->add('contract', ChoiceType::class, [
                'label' => 'filter_form_contract',
                'required' => false,
                'choices' => [
                    'filter_form_choice_buy'         => 'buy',
                    'filter_form_choice_rent'        => 'rent',
                ]
            ])
            ->add('estateType', ChoiceType::class, [
                'label' => 'filter_form_property_type',
                'required' => false,
                'multiple' => true,
                'choices' => EstateEnum::getEstateTypes(),
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('priceMin', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_from'
                ]
            ])
            ->add('priceMax', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_to'
                ]
            ])
            ->add('bedrooms', ChoiceType::class, [
                'label' => 'filter_form_bedrooms',
                'attr' => [
                    'class' => 'room-count-filter'
                ],
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4+' => '4+',
                ]
            ])
            ->add('withPhotos', CheckboxType::class, [
                'label' => 'with_photos_only',
                'required' => false
            ])
            ->add('seaView', CheckboxType::class, [
                'label' => 'facilities_seaview',
                'required' => false
            ])
            ->add('areaTotalMin', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_from'
                ]
            ])
            ->add('areaTotalMax', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_to'
                ]
            ])
            ->add('areaLivingMin', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_from'
                ]
            ])
            ->add('areaLivingMax', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_to'
                ]
            ])
            ->add('areaKitchenMin', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_from'
                ]
            ])
            ->add('areaKitchenMax', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_to'
                ]
            ])
            ->add('floorMin', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_from'
                ]
            ])
            ->add('floorMax', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'range_to'
                ]
            ])
            ->add('facilities', ChoiceType::class, [
                'label' => 'estate_facilities',
                'choices' => $facilitiesEnum,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'facilities-multiselect'
                ]
            ])
//            ->add('search', SubmitType::class, [
//                'label' => 'filter_form_search',
//                'attr' => [
//                    'class' => 'btn-primary btn-search',
//                ]
//            ])
        ;
    }

    private function buildYearFields(FormBuilderInterface $builder) {

        $yearChoices = [];
        for ($year = date('Y'); $year >= date('Y')-9; $year--) {
            $yearChoices[$year] = $year;
        }

        $notLaterChoices = [];
        for ($year = date('Y'); $year <= date('Y')+5; $year++) {
            $notLaterChoices[$year] = "before{$year}";
        }

        $afterChoices = [];
        for ($counter = 7; $counter >=3; $counter-=2) {
            $afterChoices["{$counter} {$this->translations['years']}"] = "after".(date('Y')-$counter);
        }

        $builder
            ->add('buildYear', ChoiceType::class, [
                'required' => false,
                'label' => 'build_or_deadline_year',
                'attr' => [
                    'class' => 'build-year-choice'
                ],
                'choices' => [
                    'will_be_completed_this_year'   => date('Y'),
                    'not_older'   => $afterChoices,
                    'not_later'   => $notLaterChoices,
                    'set_interval' => 'interval'
                ]
            ])
            ->add('buildYearMin', ChoiceType::class, [
                'required' => false,
                'choices' => $yearChoices,
                'placeholder' => 'range_from',
                'attr' => [
                    'class' => 'build-year-min'
                ]
            ])
            ->add('buildYearMax', ChoiceType::class, [
                'required' => false,
                'choices' => $yearChoices,
                'placeholder' => 'range_to',
                'attr' => [
                    'class' => 'build-year-max'
                ]
            ])
        ;
    }

    private function loadTranslations() {
        $translator = $this->container->get('translator');
        $this->translations['years'] = $translator->trans('years');
    }

}