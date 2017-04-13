<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 14.12.16
 * Time: 9:02
 */

namespace CabinetBundle\Controller;

use CabinetBundle\Form\estate\EstateMediaType;
use CatalogBundle\Entity\Estate;
use CatalogBundle\Entity\EstateMedia;
use CatalogBundle\Form\Estate\EstateGalleryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EstateMediaController extends EstateBaseController
{

    public function estateMediaEditAction(Request $request, $id) {
        $rep = $this->get('doctrine')->getRepository('CatalogBundle:EstateMedia');
        $estateMedia = $rep->find($id);
        if (!$estateMedia) throw $this->createNotFoundException('Объект недвижимости не найден');
        $this->checkAccess($estateMedia);

        $form = $this->createForm(EstateMediaType::class, $estateMedia);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /**@var EstateMedia $estateMedia*/
            $estateMedia = $form->getData();
            $em = $this->getDoctrine()->getManager();

            if ($form->get('favorite')->isClicked()) {
                $this->get('cabinet.estate_media_manager')->setAsMainThumb($estateMedia);
            }
            elseif ($form->get('save')->isClicked()) {
                $em->persist($estateMedia);
                $em->flush();
            }
            elseif ($form->get('delete')) {
                $em->remove($estateMedia);
                $em->flush();
            }
        }

        return new JsonResponse([
            'success' => 1,
            'form_view' => $this->renderView('@Cabinet/widget/form/estate_media_gallery_form.html.twig', [
                'forms' => $this->buildGalleryForms($estateMedia->getEstate())
            ])
        ]);
    }

    /**
     * Обрабатывает AJAX запрос добавления видео
     * @param Request $request
     * @param $id, Estate id
     * @return JsonResponse
     */
    public function videoAddAction(Request $request, $id) {
        $estate = $this->getEstate($id);
        $responseParameters = [
            'success' => 1
        ];

        try {
            $this->checkAccess($estate);
            $this->get('cabinet.estate_media_manager')->addVideoToEstate($estate);
            $responseParameters['form_view'] = $this->renderView('@Cabinet/widget/form/estate_media_gallery_form.html.twig', [
                'forms' => $this->buildGalleryForms($estate)
            ]);
        } catch (\Exception $e) {
            $responseParameters['success'] = 0;
            $responseParameters['error'] = $e->getMessage();
        }

        return new JsonResponse($responseParameters);
    }

    public function setGalleryPositionsAction(Request $request, $id) {
        $responseParameters = [
            'success' => 1
        ];

        try {
            $estate = $this->getEstate($id);
            $this->checkAccess($estate);
            $positions = $request->get('positions');
            if (!is_array($positions)) throw new \Exception('Не передан массив positions');
            $positions = array_flip($positions);

            $em = $this->getDoctrine()->getManager();
            foreach ($estate->getGallery() as $estateMedia) {
                /**@var EstateMedia $estateMedia*/
                if (isset($positions[$estateMedia->getId()])) {
                    $estateMedia->setPosition($positions[$estateMedia->getId()]);
                    $em->persist($estateMedia);
                }
            }
            $em->flush();
        } catch (\Exception $e) {
            $responseParameters['success'] = 0;
            $responseParameters['error'] = $e->getMessage();
        }

        return new JsonResponse($responseParameters);
    }

    /**
     *
     * @param $entity
     */
    private function checkAccess($entity) {
        $user = $this->getUser();

        if ($entity instanceof EstateMedia)
            if(!$entity->getEstate() || $user !== $entity->getEstate()->getCreator())
                throw $this->createAccessDeniedException();

        if ($entity instanceof Estate)
            if($entity->getCreator() !== $user)
                throw $this->createAccessDeniedException();
    }



}