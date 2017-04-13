<?php

namespace RestApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;

class MediaController extends FOSRestController
{
    /**
     * Returs media collection on $page of $album
     * @Get("/albums/{album}/page/{page}")
     */
    public function getMediasAction($album, $page) {
        return $this->createResponse(
            $this->get('rest_api.media_manager')->getMedias()
        );
    }

    private function createResponse($data = null) {
        return $this->handleView(
            $this->view($data, 200)
        );
    }
}