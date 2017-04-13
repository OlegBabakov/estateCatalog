<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 14.12.16
 * Time: 21:01
 */

namespace CabinetBundle\Form\estate;

use CatalogBundle\Entity\EstateMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstateMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**@var EstateMedia $estateMedia*/
        $estateMedia = ($builder->getData() instanceof EstateMedia && $builder->getData()->getId()) ? $builder->getData() : null;

        $builder
            ->add('favorite', SubmitType::class, [
                'label' => '<i class="fa '. ($estateMedia->getIsMainThumb() ? 'fa-star' : 'fa-star-o') .'" aria-hidden="true"></i>',
                'attr' => [
                    'class' => $estateMedia->getIsMainThumb() ? 'btn-warning' : 'btn-default'
                ]
            ])
                ->add('delete', SubmitType::class, [
                'label' => '<i class="fa fa-times" aria-hidden="true"></i>',
                'attr' => [
                    'class' => 'btn-danger'
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => '<i class="fa fa-floppy-o" aria-hidden="true"></i>',
                'attr' => [
                    'class' => 'btn-primary'
                ]
            ])
        ;

        $router = $options['router'] ?? null;

        if ($router && $estateMedia) {
            $builder->setAction(
                $router->generate('cabinet_estate_media_edit', [
                    'id' => $estateMedia->getId()
                ])
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CatalogBundle\Entity\EstateMedia',
            'router' => null
        ));
    }
}