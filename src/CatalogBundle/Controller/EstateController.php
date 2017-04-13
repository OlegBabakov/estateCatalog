<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 29.11.16
 * Time: 9:23
 */

namespace CatalogBundle\Controller;

use CatalogBundle\Form\CallMeRequestType;
use CatalogBundle\Form\SendMessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EstateController extends Controller
{
    public function showAction($id) {
        /**@var \CatalogBundle\Entity\Estate $estate*/
        $estate = $this->getEstate($id);
        $estate = $this->get('currency_converter')->convert($estate);

        $formSendMessage = $this->createForm(SendMessageType::class, null, ['estate' => $estate]);
        $formCallMeRequest = $this->createForm(CallMeRequestType::class, null, ['estate' => $estate]);

        $offers = $this->get('currency_converter')->convert(
            $estate->getChildren()
        );

        return $this->render('@Catalog/page/estate/show/show.html.twig', [
            'estate' => $estate,
            'formSendMessage'   => $formSendMessage->createView(),
            'formCallMeRequest' => $formCallMeRequest->createView(),
            'offers' => $offers
        ]);
    }

    public function sendMessageAction($id, Request $request) {
        $responseParameters = [
            'success' => 1
        ];

        try {
            $estate = $this->getEstate($id);
            $formSendMessage = $this->createForm(SendMessageType::class, null, [
                'estate' => $estate
            ]);
            $formSendMessage->handleRequest($request);
            if ($formSendMessage->isValid())
                $this->get('catalog.email_helper')->estateSendMessageActionEmail(
                    $formSendMessage->getData(),
                    $estate
                );

            $template = $this->get('twig')->loadTemplate('@Catalog/page/estate/show/modal.html.twig');
            $responseParameters['html'] = $template->renderBlock('sendMessageContent', [
                'formSendMessage' => $formSendMessage->createView(),
                'success' => $formSendMessage->isValid()
            ]);
        } catch (\Exception $e) {
            $responseParameters['success'] = 0;
            $responseParameters['error'] = $e->getMessage();
        }

        return new JsonResponse($responseParameters);
    }

    public function callMeRequestAction($id, Request $request) {
        $responseParameters = [
            'success' => 1
        ];

        try {
            $estate = $this->getEstate($id);

            $formCallMeRequest = $this->createForm(CallMeRequestType::class, null, ['estate' => $estate]);
            $formCallMeRequest->handleRequest($request);

            if ($formCallMeRequest->isValid())
                $this->get('catalog.email_helper')->estateCallMeRequestActionEmail(
                    $formCallMeRequest->getData(),
                    $estate
                );

            $template = $this->get('twig')->loadTemplate('@Catalog/page/estate/show/modal.html.twig');
            $responseParameters['html'] = $template->renderBlock('callMeRequestContent', [
                'formCallMeRequest' => $formCallMeRequest->createView(),
                'success' => $formCallMeRequest->isValid()
            ]);
        } catch (\Exception $e) {
            $responseParameters['success'] = 0;
            $responseParameters['error'] = $e->getMessage();
        }

        return new JsonResponse($responseParameters);
    }

    private function getEstate($id) {
        $rep = $this->getDoctrine()->getRepository('CatalogBundle:Estate');
        $estate = $rep->find($id);
        if (!$estate) throw $this->createNotFoundException();

        return $estate;
    }

}