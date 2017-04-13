<?php

namespace RestApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

class AlbumController extends FOSRestController
{
    /**
     * Return single album data with list of attached medias
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAlbumAction($id)
    {
        return $this->createResponse(
            $this->get('rest_api.album_manager')->getAlbum($id)
        );
    }

    /**
     * Returns list of albums with count of medias for each entity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAlbumsAction()
    {
        return $this->createResponse(
            $this->get('rest_api.album_manager')->getAlbumList()
        );
    }

    /**
     * Creates new album
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAlbumsAction()
    {
        $album = $this->get('rest_api.album_manager')->addAlbum();
        return $this->createResponse($album);
    }

    private function createResponse($data = null) {
        return $this->handleView(
            $this->view($data, 200)
        );
    }
}