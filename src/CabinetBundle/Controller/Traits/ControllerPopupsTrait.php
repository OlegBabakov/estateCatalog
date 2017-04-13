<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 14.12.16
 * Time: 15:12
 */

namespace CabinetBundle\Controller\Traits;


trait ControllerPopupsTrait
{
    private function popup($type, $message) {
        /**@var \Symfony\Bundle\FrameworkBundle\Controller\Controller $this*/
        $message = $this->get('translator')->trans($message);

        $this->get('session')
            ->getFlashBag()
            ->add($type, $message);
    }

    private function popupSuccess($message) {
        $this->popup('success', $message);
    }

    private function popupDanger($message) {
        $this->popup('danger', $message);
    }

    private function popupWarning($message) {
        $this->popup('warning', $message);
    }

    private function popupInfo($message) {
        $this->popup('info', $message);
    }
}