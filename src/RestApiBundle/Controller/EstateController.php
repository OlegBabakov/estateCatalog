<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.11.16
 * Time: 16:48
 */

namespace RestApiBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;

class EstateController extends FOSRestController
{
    const REQUEST_DATA_FORMAT = 'json';

    public function getEstatesAction()
    {
        return $this->createResponse(
            $this->get('catalog.estate_manager')->search()
        );
    }

    private function createResponse($data = null) {
        $serializer = $this->get('rest_api.serializer');

        $data = $serializer
            ->getSerializer()
            ->normalize(
                $data,
                $this::REQUEST_DATA_FORMAT,
                ['groups' => ['api']]
            );

        return $this->handleView(
            $this->view($data, 200)
        );
    }
}