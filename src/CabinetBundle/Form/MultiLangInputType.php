<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 08.01.17
 * Time: 19:52
 */

namespace CabinetBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiLangInputType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'field_type_class' => TextType::class,
            'field_options' => []
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = ['en','ru', 'vn'];
        foreach ($languages as $language) {
            $builder
                ->add(
                    $language,
                    $options['field_type_class'],
                    $options['field_options']
                );
        }

    }

}