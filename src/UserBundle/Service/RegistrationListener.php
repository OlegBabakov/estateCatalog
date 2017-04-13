<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.09.16
 * Time: 16:57
 */

namespace UserBundle\Service;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
        ];
    }

    public function onRegistrationInitialize(GetResponseUserEvent $event)
    {
        $url = $this->router->generate('fos_user_security_login');
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }
}