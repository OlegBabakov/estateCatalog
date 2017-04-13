<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 29.10.16
 * Time: 19:35
 */

namespace RestApiBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use GalleryBundle\Entity\Album;

class AlbumManager
{
    const REQUEST_DATA_FORMAT = 'json';

    /**@var Registry */
    private $doctrine;
    /**@var Request */
    private $request;
    /**@var SerializerService */
    private $serializer;

    /**
     * AlbumManager constructor.
     * @param $doctrine
     * @param $request
     * @param $serializer
     */
    public function __construct($doctrine, $request, $serializer) {
        $this->doctrine = $doctrine;
        $this->request = $request;
        $this->serializer = $serializer;
    }

    /**
     * Add new album into database
     * @return Album|null
     */
    public function addAlbum() {
        $content = $this->request->getContent();
        if ($content) {
            /**@var Album $album*/
            $album = $this
                ->serializer
                ->getSerializer()
                ->deserialize(
                    $content,
                    Album::class,
                    $this::REQUEST_DATA_FORMAT
                );
            $manager = $this->doctrine->getManager();
            $manager->persist($album);
            $manager->flush();

            return $album;
        }
        return null;
    }

    public function getAlbumList() {
        $collection =  $this->doctrine->getRepository('GalleryBundle:Album')->findAll();
        $collection = $this
            ->serializer
            ->getSerializer()
            ->normalize(
                $collection,
                $this::REQUEST_DATA_FORMAT,
                ['groups' => ['api']]
            );
        return $collection;
    }

    public function getAlbum($id) {
        $album =  $this->doctrine->getRepository('GalleryBundle:Album')->find($id);
        $album = $this
            ->serializer
            ->getSerializer()
            ->normalize(
                $album,
                $this::REQUEST_DATA_FORMAT,
                ['groups' => ['api','common']]
            );
        return $album;
    }

}