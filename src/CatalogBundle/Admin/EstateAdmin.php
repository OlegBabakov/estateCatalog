<?php

namespace CatalogBundle\Admin;

use CatalogBundle\Classes\Enum\EstateEnum;
use CatalogBundle\Entity\Estate;
use CatalogBundle\Entity\EstateMedia;
use CatalogBundle\Form\AddressMapType;
use CatalogBundle\Form\Estate\AreaType;
use CatalogBundle\Form\Estate\EstateDataType;
use CatalogBundle\Form\Estate\RoomsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EstateAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('lat')
            ->add('lng')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('treeLevel')
            ->add('parent')
            ->add('_action', null, array(
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
        $formMapper
            ->tab('Main')
                ->add('creator')
                ->add('contactProfiles')
                ->add('estateType', ChoiceType::class, [
                    'label' => 'filter_form_property_type',
                    'choices' => EstateEnum::getEstateTypes()
                ])
                ->add('priceSell', 'number', ['required' => false])
                ->add('priceRent', 'number', ['required' => false])
                ->add('title')
            ->end()
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('lat')
            ->add('lng')
        ;
    }

    private function getImageFieldOptions() {
        $container = $this->getConfigurationPool()->getContainer();
        $mediaRep = $container->get('doctrine')->getRepository('CatalogBundle:EstateMedia');
        $imageChoiceList = $mediaRep->findByEstate($this->getSubject());

        $fileFieldOptions = [
            'label' => 'Main image',
            'choices' => $imageChoiceList,
            'class' => EstateMedia::class,
            'required'   => false,
            'data_class' => null
        ];

        $object = $this->getSubject();
        if ($object instanceof Estate) {
            $fileInfo = $object->getImage();

            if (isset($fileInfo['url'])) {
                $fileFieldOptions['help'] = 'Current image:<br><img src="'.$fileInfo['url'].'" class="admin-preview" style="width:50%; margin-top:10px;" />';
            }
        }

        return $fileFieldOptions;
    }
}
