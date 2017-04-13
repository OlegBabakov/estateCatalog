<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 29.10.16
 * Time: 19:35
 */

namespace RestApiBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;

class MediaManager
{
    const REQUEST_DATA_FORMAT = 'json';
    const IMAGES_LIMIT_PER_PAGE = 10;

    /**@var Registry */
    private $doctrine;
    /**@var Request */
    private $request;
    /**@var SerializerService */
    private $serializer;
    /**@var Paginator*/
    private $paginator;
    /**@var TwigEngine*/
    private $templating;

    /**
     * MediaManager constructor.
     * @param $doctrine
     * @param $request
     * @param $serializer
     * @param $paginator
     * @param $templating
     */
    public function __construct($doctrine, $request, $serializer, $paginator, $templating)
    {
        $this->doctrine = $doctrine;
        $this->request = $request;
        $this->serializer = $serializer;
        $this->paginator = $paginator;
        $this->templating = $templating;
    }

    /**
     * Return media collection by selected album and page
     * @return array
     */
    public function getMedias() {
        //Default result
        $result = [
            'medias' => [],    //Medias collection
            'paginator' => ''  //Rendered paginator
        ];

        //Fetch request params
        $album =$this->request->get('album');
        $page = $this->request->get('page') ? : 1;
        if (!$album) return $result;

        //Getting medias on current page
        /**@var \Doctrine\ORM\Query $query*/
        $dql   = "SELECT m FROM GalleryBundle:Media m WHERE m.album = :album";
        $query = $this->doctrine->getManager()->createQuery($dql);
        $query->setParameter('album', $album);
        $pagination = $this->paginator->paginate(
            $query,
            $page,
            $this::IMAGES_LIMIT_PER_PAGE
        );
        $result['medias'] = $pagination->getItems();

        //Getting medias amount in album
        $dql   = "SELECT count(m.id) as qty FROM GalleryBundle:Media m WHERE m.album = :album";
        $query = $this->doctrine->getManager()->createQuery($dql);
        $query->setParameter('album', $album);
        $mediasAmout = $query->getSingleScalarResult();
        $result['paginator'] = $this->templating->render('@Gallery/widget/pagination.html.twig', [
            'showAlwaysFirstAndLast' => false,
            'currentPage'            => $page,
            'album'                  => $album,
            'lastPage'               => ceil($mediasAmout/$this::IMAGES_LIMIT_PER_PAGE)
        ]);

        $result = $this
            ->serializer
            ->getSerializer()
            ->normalize(
                $result,
                $this::REQUEST_DATA_FORMAT,
                ['groups' => ['api']]
            );
        return $result;
    }

}