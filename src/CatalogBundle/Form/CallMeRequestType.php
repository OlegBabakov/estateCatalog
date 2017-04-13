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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\User;

class CallMeRequestType extends AbstractType
{
    private $container;
    private $translator;
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
        $this->translator = $this->container->get('translator');

        $this->estate = $options['estate'] ?? null;
        if (!$this->estate instanceof Estate) throw new \Exception('Не передан estate');

        $builder->setAction(
            $this->container->get('router')->generate('estate_action_call_me_request', ['id' => $this->estate->getId()])
        );

        $timeChoices = [];
        for ($hour = 0; $hour <= 24; $hour++) {
            $timeChoices[str_pad($hour, 2, '0', STR_PAD_LEFT).":00"] = $hour;
        }

        $builder
            ->add('user', HiddenType::class, [
                'attr' => [
                    'class' => 'recipient'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => ' ',
                'attr' => [
                    'placeholder' => 'phone'
                ],
                'constraints' => [
                    new NotBlank()
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
            ->add('timeFrom', ChoiceType::class, [
                'required'    => false,
                'data' => 10,
                'choices' => $timeChoices
            ])
            ->add('timeTo', ChoiceType::class, [
                'required'    => false,
                'data' => 19,
                'choices' => $timeChoices
            ])
            ->add('timezone', TimezoneType::class, [
                'label' => ' ',
                'required' => false,
                'attr' => [
                    'class' => 'select2',
                    'data-placeholder' => $this->translator->trans('timezone')
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