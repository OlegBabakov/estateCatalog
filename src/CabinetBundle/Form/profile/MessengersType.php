<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 19.12.16
 * Time: 9:09
 */

namespace CabinetBundle\Form\profile;


use Comur\ImageBundle\Form\Type\CroppableImageType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessengersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('skype', TextType::class)
            ->add('viber', TextType::class)
            ->add('whatsapp', TextType::class)
        ;

    }
}