<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 16.12.16
 * Time: 12:29
 */

namespace CatalogBundle\Form\Estate;


use CabinetBundle\Form\estate\EstateMediaType;
use CatalogBundle\Entity\Estate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstateGalleryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'CatalogBundle\Entity\Estate',
            'router' => null
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gallery', CollectionType::class, array(
                'entry_type' => EstateMediaType::class,
            ))
            ->add('submit', SubmitType::class, [
                'label' => 'save',
                'attr' => [
                    'class' => 'btn-primary'
                ]
            ])
        ;

        $estate = (($builder->getData() instanceof Estate) && $builder->getData()->getId()) ? $builder->getData() : null;
        $router = $options['router'] ?? null;

        if ($estate && $router) {
            $builder->setAction(
                $router->generate('cabinet_estate_media_action', [
                    'id' => $estate->getId()
                ])
            );
        }
    }
}