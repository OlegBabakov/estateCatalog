<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 29.10.16
 * Time: 19:35
 */

namespace CabinetBundle\Service\manager;

use CabinetBundle\Classes\VideoResolver;
use CatalogBundle\Entity\Estate;
use CatalogBundle\Entity\EstateMedia;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EstateMediaManager
{
    private $container;
    /**@var Registry */
    private $doctrine;
    /**@var CacheManager*/
    private $cacheManager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->doctrine = $container->get('doctrine');
        $this->cacheManager = $container->get('liip_imagine.cache.manager');
    }

    /**
     * Сделать медия как основное для объекта недвижимости
     * @param EstateMedia $estateMedia
     * @throws \Exception
     */
    public function setAsMainThumb(EstateMedia $estateMedia) {
        if (!$estateMedia->getEstate()) throw new \Exception("медиа {$estateMedia->getId()} не привязан к объекту недвижимости");
        $em = $this->doctrine->getManager();

        $estate = $estateMedia->getEstate();
        foreach ($estate->getGallery() as $galleryItem) {
            /**@var EstateMedia $galleryItem*/
            if ($galleryItem->getIsMainThumb()) {
                $galleryItem->setIsMainThumb(false);
                $em->persist($galleryItem);
            }
        }
        $estateMedia->setIsMainThumb(true);
        $estateMedia->setPosition(0);
        $estateMedia->getEstate()->setImageEntity($estateMedia);

        $em->persist($estateMedia->getEstate());
        $em->persist($estateMedia);
        $em->flush();
    }

    /**
     * Удаление сущности и удаление всех файлов, связанных с сущностью
     * @param EstateMedia $estateMedia
     * @param bool $removeEntity, true - удалять сущноссть, false- удаление только связанных файлов (для использование в preRemove listener'e)
     */
    public function remove(EstateMedia $estateMedia, bool $removeEntity) {
        $em = $this->doctrine->getManager();

        if ($estateMedia->getEstate() && $estateMedia->getIsMainThumb()) {
            $estate = $estateMedia->getEstate();
            $gallery = $estate->getGallery();
            if (count($gallery)>1) {
                $estate->removeGallery($estateMedia);
                $mainThumbMedia = $gallery->first();
                $this->setAsMainThumb($mainThumbMedia);
            }
            elseif (count($gallery)==1) {
                $estate->setImage([]);
                $em->persist($estate);
                $em->flush();
            }
        }

        $file = $estateMedia->getFile();
        $this->cacheManager->remove($file['url']); //Удаляем файлы кэша
        $filename = $this->container->getParameter('kernel.root_dir'). '/../web'. $file['url'];
        if (file_exists($filename)) unlink($filename);

        if ($removeEntity) {
            $em->remove($estateMedia);
            $em->flush();
        }
    }

    /**
     * @param Estate $estate
     * @return EstateMedia
     * @throws \Exception
     */
    public function addVideoToEstate(Estate $estate) {
        $YOUTUBE_URL_PARSE_ERROR_MESSAGE = "It's not YouTube video, please add youtube video URL";

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $videoURL = $request->get('video');
        if (!$videoURL) throw new \Exception($YOUTUBE_URL_PARSE_ERROR_MESSAGE);

        $videoResolver = new VideoResolver();
        $fileInfo = $videoResolver->getVideoData($videoURL);
        if (!$fileInfo) throw new \Exception($YOUTUBE_URL_PARSE_ERROR_MESSAGE);

        $estateMedia = new EstateMedia();
        $estateMedia->setFile($fileInfo);
        $estateMedia->setEstate($estate);

        $em = $this->doctrine->getManager();
        $em->persist($estateMedia);
        $em->flush();

        return $estateMedia;
    }


}