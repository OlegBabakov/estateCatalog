<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 11.10.16
 * Time: 12:46
 */

namespace CabinetBundle\Service\upload;
use CatalogBundle\Classes\Upload\Uploadable;
use CatalogBundle\Entity\Estate;
use CatalogBundle\Entity\EstateMedia;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OneupUploaderListener
{
    /**@var ContainerInterface $container */
    private $container;

    /**
     * UploadListener constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * OneUp Uploader handler
     * @param PostPersistEvent $event
     */
    public function onUpload(PostPersistEvent $event)
    {
        if ($event->getType() === 'estateMedia') {
            $estateMedia = new EstateMedia();
            $this->fetchSpecialRequestParameters($estateMedia, $event);
            $this->upload($estateMedia, $event);
            $this->postUpload($estateMedia, $event);
        }
        //You can add handlers for another file mapping types
    }

    private function fetchSpecialRequestParameters(Uploadable $entity, PostPersistEvent $event) {
        try {
            /**@var \Symfony\Component\HttpFoundation\File\File $file*/
            $file = $event->getFile();
            if (!$file) throw new \Exception('В запросе отсутствует файл');

            if ($entity instanceof EstateMedia) {
                //Привязка EstateMedia к Estate
                $rep = $this->container->get('doctrine')->getRepository('CatalogBundle:Estate');
                $estateId = $event->getRequest()->get('estate');
                if (!$estateId) throw new \Exception('Отсутствует параметр estate');
                /**@var Estate $estate*/
                $estate = $rep->find($estateId);
                if (!$estate) throw new \Exception("Отсутствует объект с id={$estateId}");
                $entity->setEstate($estate);
            }

        } catch (\Exception $e) {
            if (isset($file)) unlink($file->getPathname()); //Удаление файла
            throw new \Exception($e->getMessage());
        }
    }

    private function upload(Uploadable $entity, PostPersistEvent $event) {
        /**@var \Symfony\Component\HttpFoundation\File\File $file*/
        $file = $event->getFile();
        /**@var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile*/
        $uploadedFile = $event->getRequest()->files->get('file');
        //Choose upload behavior based on content type, mapping is set by 'oneup_uploader' in app/config.yml
        $fileMappingType = $event->getType();


        $url = "/uploads/{$fileMappingType}/{$file->getFilename()}";
        $fileInfo['url']          = $url;
        $fileInfo['absolutePath'] = $file->getPathname();
        $fileInfo['originalName'] = $uploadedFile->getClientOriginalName();
        $fileInfo['mimeType']     = $uploadedFile->getClientMimeType();
        $fileInfo['size']         = $uploadedFile->getClientSize();

        $entity->setFile($fileInfo);

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->persist($entity);
        $em->flush();
    }

    private function postUpload(Uploadable $entity, PostPersistEvent $event) {
        if ($entity instanceof EstateMedia) {
            //Установка главного изображения, если оно единственное
            if (1 === $entity->getEstate()->getGallery()->count())
                $this->container->get('cabinet.estate_media_manager')->setAsMainThumb($entity);
        }
    }
}