<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 14.12.16
 * Time: 9:02
 */

namespace CabinetBundle\Controller;


use CabinetBundle\Controller\Traits\ControllerPopupsTrait;
use CabinetBundle\Form\estate\EstateMediaType;
use CatalogBundle\Entity\Estate;
use CatalogBundle\Form\Estate\EstateGalleryType;
use CatalogBundle\Form\Estate\EstateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EstateController extends EstateBaseController
{
    use ControllerPopupsTrait;

    public function addAction() {
        $estate = new Estate();
        return $this->estateFormAction($estate, 'add');
    }

    public function editAction($id) {
        return $this->estateFormAction(
            $this->getEstate($id),
            'edit'
        );
    }

    public function deleteAction($id) {
        $estate = $this->getEstate($id);
        if (!$estate) throw $this->createNotFoundException();
        $this->checkPermission($estate);

        $em = $this->getDoctrine()->getManager();
        $em->remove($estate);
        $em->flush();

        return $this->listAction();
    }

    public function listAction() {
        $rep = $this->getDoctrine()->getRepository('CatalogBundle:Estate');
        $collection = $rep->getByUser($this->getUser());

        return $this->render('@Cabinet/page/estate/list.html.twig', [
            'estateList' => $collection
        ]);
    }

    public function galleryUpdateEventAction($id) {
        $estate = $this->getEstate($id);
        return new JsonResponse([
            'success' => 1,
            'form_view' => $this->renderView('@Cabinet/widget/form/estate_media_gallery_form.html.twig', [
                'forms' => $this->buildGalleryForms($estate)
            ])
        ]);
    }

    private function estateFormAction(Estate $estate, $action) {
        $request = $this->get('request_stack')->getCurrentRequest();
        $form = $this->createForm(EstateType::class, $estate);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /**@var \CatalogBundle\Entity\Estate $estate*/
            $estate = $form->getData();
            if (!$estate->getCreator()) $estate->setCreator($this->getUser());
            if (!$this->isGranted('ROLE_ADMIN') && empty($estate->getContactProfiles()))
                $estate->addContactProfile($this->getUser());

            try {
                $this->get('catalog.estate_manager')->save($estate);

                if ($action === 'add') $this->popupSuccess('message_estate_added');
                if ($action === 'edit') $this->popupSuccess('message_estate_saved');

                if ($estate->getId()) return $this->redirectToRoute('cabinet_estate_edit', ['id' => $estate->getId()]);
            } catch (\Exception $e) {
                $this->popupDanger('message_save_error');
            }
        }

        $renderParams = [
            'form' => $form->createView()
        ];
        if ($estate->getId()) {
            $renderParams['estate'] = $estate;
            $renderParams['galleryFormList'] = $this->buildGalleryForms($estate);
        }
        return $this->render('@Cabinet/page/estate/add_edit.html.twig', $renderParams);
    }

}