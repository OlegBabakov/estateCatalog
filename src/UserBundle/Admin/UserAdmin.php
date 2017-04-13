<?php

namespace UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use UserBundle\Entity\User;

class UserAdmin extends Admin
{
    const DEFAULT_ROLE = 'ROLE_SUPER_ADMIN';

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('username')
            ->add('enabled')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->add('id')
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject();

        $passwordLabel = 'Пароль';
        if ($user->getPassword()) {
            $passwordLabel = 'Пароль (оставьте пустым, чтобы не менять существующий)';
        }

        $formMapper
            ->add('enabled')
            ->add('username')

//            ->add('email')
            ->add('newPassword', 'text', array(
                'label' => $passwordLabel,
                'required' => false
            ))
            ->add('email', null, ['required' => false])

        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
//            ->add('id')
            ->add('username')
            ->add('email')
            ->add('enabled')
//            ->add('roles')
            ->add('avatar')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    public function prePersist($object) {
        $this->fillDefaultData($object);
        parent::prePersist($object);
        $this->updateUser($object);
    }

    public function preUpdate($object) {
        parent::preUpdate($object);
        $this->updateUser($object);
    }

    public function updateUser(User $u) {
        if ($u->getNewPassword()) {
            $u->setPlainPassword($u->getNewPassword());
        }

        $um = $this->getConfigurationPool()->getContainer()->get('fos_user.user_manager');
        $um->updateUser($u, false);
    }

    private function fillDefaultData(User $user) {
        if (!$user->hasRole($this::DEFAULT_ROLE)) {
            $user->addRole($this::DEFAULT_ROLE);
        }
    }
}
