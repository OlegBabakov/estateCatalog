<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 23.11.16
 * Time: 9:40
 */

namespace CatalogBundle\Form;

use CatalogBundle\Entity\Estate;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SendMessageType extends AbstractType
{
    private $container;
    /**@var Estate */
    private $estate;

    /**
     * SendMessageType constructor.
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'estate' => null
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->estate = $options['estate'] ?? null;
        if (!$this->estate instanceof Estate) throw new \Exception('Не передан estate');

        $builder->setAction(
            $this->container->get('router')->generate('estate_action_send_message', ['id' => $this->estate->getId()])
        );

        $builder
            ->add('user', HiddenType::class, [
                'attr' => [
                    'class' => 'recipient'
                ]
            ])
            ->add('name', TextType::class, [
                'label' => ' ',
                'attr' => [
                    'placeholder' => 'your_name'
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => ' ',
                'attr' => [
                    'placeholder' => 'email'
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('sendCopy', CheckboxType::class, [
                'required' => false,
                'label' => 'send_me_letter_copy',
            ])
            ->add('text', TextareaType::class, [
                'data' => $this->container->get('translator')->trans('send_message_default_text', [
                    '%id%' => $this->estate->getId()
                ]),
                'label' => ' ',
                'attr' => [
                    'placeholder' => 'message_text',
                    'style' => 'min-height: 100px;'
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'send_message',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

}