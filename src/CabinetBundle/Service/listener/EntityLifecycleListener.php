<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 18.12.16
 * Time: 9:42
 */

namespace CabinetBundle\Service\listener;

use CabinetBundle\Service\manager\EstateMediaManager;
use CatalogBundle\Entity\EstateMedia;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EntityLifecycleListener
{
    private $estateMediaManager;

    /**
     * EstateMediaListener constructor.
     * @param $estateMediaManager
     */
    public function __construct(EstateMediaManager $estateMediaManager)
    {
        $this->estateMediaManager = $estateMediaManager;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof EstateMedia) $this->estateMediaManager->remove($entity, false);
    }

}