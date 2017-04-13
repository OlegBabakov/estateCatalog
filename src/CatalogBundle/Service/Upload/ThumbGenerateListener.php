<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 25.11.16
 * Time: 11:19
 */

namespace CatalogBundle\Service\Upload;

use CatalogBundle\Classes\Upload\Uploadable;
use CatalogBundle\Entity\EstateMedia;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Смотрит изменения сущностей реализующих интерфейс Uploadable и в случае загрузки картинок прописывает URL миниатюр в поле file
 * Дополнительный функционал: при изменении EstateMedia и установленной опции setAsMainImage переносит данные поля file в сущность Estate->image
 * Class ThumbGenerateListener
 * @package CatalogBundle\Service\Upload
 */
class ThumbGenerateListener
{
    const MEDIA_LIIP_DEFAULT_THUMB_FILTER_NAME = 'estate_thumb_default';
    const MEDIA_LIIP_SMALL_THUMB_FILTER_NAME   = 'estate_thumb_small';

    private $cacheManager;
    private $doctrine;
    private $requestStack;

    public function __construct(CacheManager $cacheManager, Registry $doctrine, RequestStack $requestStack)
    {
        $this->cacheManager = $cacheManager;
        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->makeCache($entity);
        //if ($entity instanceof EstateMedia) $this->setMediaAsEstateImage($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->makeCache($entity);
        //if ($entity instanceof EstateMedia) $this->setMediaAsEstateImage($entity);
    }

    private function makeCache($entity) {
        static $clearPrefix = null;

        if (!$clearPrefix) {
            $clearPrefix = function ($str, Request $request) {
                $result = str_replace('http://localhost', '', $str);
                $result = str_replace($request->getSchemeAndHttpHost(), '', $result);
                $result = str_replace($request->getBaseUrl(), '', $result);
                return $result;
            };
        }

        if ($entity instanceof Uploadable) {
            $fileInfo = $entity->getFile();

            if (isset($fileInfo['mimeType']) && stripos($fileInfo['mimeType'], 'image')!==false && !isset($fileInfo['thumbUrl']) && !isset($fileInfo['thumbSmallUrl'])) {
                $request = $this->requestStack->getCurrentRequest();

                $fileInfo['thumbUrl'] = $clearPrefix(
                    $this->cacheManager->getBrowserPath(
                        $fileInfo['url'],
                        $this::MEDIA_LIIP_DEFAULT_THUMB_FILTER_NAME
                    ),
                    $request
                );

                $fileInfo['thumbSmallUrl'] = $clearPrefix(
                    $this->cacheManager->getBrowserPath(
                        $fileInfo['url'],
                        $this::MEDIA_LIIP_SMALL_THUMB_FILTER_NAME
                    ),
                    $request
                );

                $entity->setFile($fileInfo);
            }
        }
    }

    private function setMediaAsEstateImage(EstateMedia $media) {
        if (!$media->getEstate() || !$media->getAsEstateImage() || !$media->getFile()) return false;

        $fileInfo = $media->getFile();
        if (isset($fileInfo['mimeType']) && stripos($fileInfo['mimeType'], 'image')!==false) {
            $estate = $media->getEstate();
            $estate->setImage($media->getFile());

            $em = $this->doctrine->getManager();
            $em->detach($estate);
            $em->persist($estate);
            $em->flush();

            return true;
        }
        return false;
    }
}