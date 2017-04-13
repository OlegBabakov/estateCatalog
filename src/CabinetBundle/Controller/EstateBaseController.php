<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 17.12.16
 * Time: 15:04
 */

namespace CabinetBundle\Controller;


use CabinetBundle\Form\estate\EstateMediaType;
use CatalogBundle\Entity\Estate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EstateBaseController extends Controller
{
    protected function  buildGalleryForms(Estate $estate) {
        $formList = [];
        $router = $this->get('router');
        foreach ($estate->getGallery() as $estateMedia) {
            $form = $this
                ->createForm(EstateMediaType::class, $estateMedia, [
                    'router' => $router
                ])
                ->createView();
            $formList[] = $form;
        }
        return $formList;
    }

    /**
     * Возвращает сущность по id
     * @param $id
     * @return Estate
     */
    protected function getEstate($id) {
        $rep = $this->getDoctrine()->getManager()->getRepository('CatalogBundle:Estate');
        $estate = $rep->find($id);
        if (!$estate) throw $this->createNotFoundException();
        return $estate;
    }


    protected function checkPermission(Estate $estate, $action = 'edit') {
        if ($this->getUser() !== $estate->getCreator()) throw $this->createAccessDeniedException();
    }

}