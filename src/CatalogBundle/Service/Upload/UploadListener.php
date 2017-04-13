<?php

/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.10.16
 * Time: 23:18
 */
namespace CatalogBundle\Service\Upload;

use CatalogBundle\Classes\Upload\Uploadable;
use CatalogBundle\Entity\EstateMedia;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;


class UploadListener
{
    private $uploader;
    private $uploadWebPathList;

    public function __construct(FileUploader $uploader, $uploadWebPathList)
    {
        $this->uploader = $uploader;
        $this->uploadWebPathList = $uploadWebPathList;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity, $args);
    }

    private function uploadFile($entity, $preUpdateEventArgs = null)
    {
        if ($entity instanceof Uploadable) {
            /**upload path depends from $entity class */
//            if ($entity instanceof EstateMedia) {
//                $this->uploader->upload($entity, $this->uploadWebPathList['estateMedia'], $preUpdateEventArgs);
//            }
        }

        return;
    }
}