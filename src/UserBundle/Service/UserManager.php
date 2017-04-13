<?php

namespace UserBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use UserBundle\Entity\User;

class UserManager
{
    /**@var \FOS\UserBundle\Util\UserManipulator*/
    protected $userManipulator;
    /**@var \FOS\UserBundle\Util\Canonicalizer*/
    protected $canonicalizer;
    /**@var ContainerInterface*/
    protected $conatiner;


    /**
     * UserManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->conatiner = $container;
        $this->userManipulator = $container->get('fos_user.util.user_manipulator');
        //$this->canonicalizer   = $container->get('fos_user.util.username_canonicalizer');
    }

    /**
     * Добавляет пользователя в БД из формы
     * @param Form $form
     * @param null $password
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function createFromAddForm(Form $form, $password = null) {
        $email = $form->get('email')->getData();
        $username = substr($email, 0, strpos($email, '@'));
        $password = (!is_null($password)) ? $password : $this->generateRandomString(6);

        $user =  $this->userManipulator->create(
            $username,
            $password,
            $email,
            true,
            false
        );

        return $user;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function deleteAvatar(User $user) {
        $avatar = $user->getAvatar();
        if (!is_array($avatar)) return false;

        $webDir = $this->conatiner->getParameter('kernel.root_dir'). '/../web';

        $field = 'url';
        if (isset($avatar[$field]) && file_exists($webDir.$avatar[$field])) unlink($webDir.$avatar[$field]);

        $field = 'thumbUrl';
        if (isset($avatar[$field]) && file_exists($webDir.$avatar[$field])) unlink($webDir.$avatar[$field]);

        return true;
    }

}