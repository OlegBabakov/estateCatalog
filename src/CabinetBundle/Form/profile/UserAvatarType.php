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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAvatarType extends AbstractType
{
    const THUMB_MIN_WIDTH = 300;
    const THUMB_MIN_HEIGHT = 300;

    /**@var \Symfony\Component\DependencyInjection\ContainerInterface*/
    private $container;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['container']) || !$options['container'] instanceof ContainerInterface) throw new \Exception('В форму не передан контейнер');
        $this->container = $options['container'];

        /**@var \UserBundle\Entity\User $user*/
        $user = $builder->getForm()->getData();

        $builder
            ->add('comurCropField', CroppableImageType::class, array(
                'label' => 'avatar',
                'uploadConfig' => array(
                    'uploadRoute' => 'comur_api_upload',        //optional
                    'uploadUrl' => $this->getUploadRootDir(),       // required - see explanation below (you can also put just a dir path)
                    'webDir' => $this->getUploadDir(),              // required - see explanation below (you can also put just a dir path)
                    'fileExt' => '*.jpg;*.gif;*.png;*.jpeg',    //optional
    //                'libraryDir' => null,                       //optional
    //                'libraryRoute' => 'comur_api_image_library', //optional
                    'showLibrary' => false,                      //optional
    //                'saveOriginal' => 'originalImage'           //optional
                ),
                'cropConfig' => array(
                    'minWidth' => $this::THUMB_MIN_WIDTH,
                    'minHeight' => $this::THUMB_MIN_HEIGHT,
                    'aspectRatio' => true,              //optional
                    'cropRoute' => 'comur_api_crop',    //optional
                    'forceResize' => false,             //optional
                    'thumbs' => array(                  //optional
                        array(
                            'maxWidth' => $this::THUMB_MIN_WIDTH,
                            'maxHeight' => $this::THUMB_MIN_HEIGHT,
                            'useAsFieldImage' => true  //optional
                        )
                    )
                )
            ))
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn green'
                ]
            ])
        ;

        $builder->setAction(
            $this->container->get('router')->generate('cabinet_profile_avatar_change')
        );

        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'handleData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'deleteOldAvatar'));
    }

    public function deleteOldAvatar(FormEvent $event) {
        $token = $this->container->get('security.token_storage')->getToken();
        if (!$token || !is_object($token->getUser())) return false;
        $user = $token->getUser();
        $this->container->get('user.manager')->deleteAvatar($user);
    }


    public function handleData(FormEvent $event) {
        /**@var \UserBundle\Entity\User $user*/
        $user = $event->getData();

        $avatarInfo = $user->getAvatar();
        $avatarInfo['url'] = '/'. $this->getUploadDir(). '/'. $avatarInfo['comurCropField'];

        $avatarInfo['thumbUrl'] = $avatarInfo['url'];
        $avatarInfo['thumbUrl'] = str_replace(
            'cropped/',
            'cropped/'.$this->container->getParameter('comur_image.thumbs_dir').'/'.$this::THUMB_MIN_WIDTH.'x'.$this::THUMB_MIN_HEIGHT.'-',
            $avatarInfo['thumbUrl']
        );

       $user->setAvatar($avatarInfo);
    }

    public function getUploadDir() {
        return 'uploads/user/avatar';
    }

    private function getUploadRootDir() {
        return dirname($this->container->getParameter('kernel.root_dir')). '/web/'. $this->getUploadDir();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'container' => null
        ));
    }

}