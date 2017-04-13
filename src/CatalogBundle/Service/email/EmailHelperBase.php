<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 18.01.17
 * Time: 10:16
 */

namespace CatalogBundle\Service\email;

use CatalogBundle\Entity\Estate;
use \Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;

class EmailHelperBase
{
    /**@var ContainerInterface*/
    protected $container;

    /**
     * EmailHelper constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Отправка Email
     * @param $to
     * @param string $subject
     * @param string $body
     * @param string $replyTo
     * @return bool
     */
    protected function sendEmail($to, $subject = "", $body = "", $replyTo = ""){
        $domain = $this->container->getParameter('domain');
        $emailFrom = $this->container->getParameter('email_from');
        $from = [
            $emailFrom => ucfirst($domain)
        ];

        $replyTo = $replyTo ? $replyTo : $emailFrom;

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setReplyTo($replyTo)
            ->setTo($to)
            ->setBody($body,'text/html');

        $mailer = $this->container->get('swiftmailer.mailer');

        try {
            $result = $mailer->send($message);
            return true;
        } catch (\Exception $e) {
            trigger_error("by_trigger: ".$e->getMessage(), E_USER_WARNING);
            return false;
        }
    }

    /**
     * Отправляет письмо по установленному шаблону. (Вспомогательный метод требующий подготовленные шаблоны)
     * @param       $to
     * @param       $template
     * @param array $arg
     * @return bool
     */
    protected function sendEmailFromTemplate($to, $template, Array $arg){
        return self::sendEmail(
            $to,
            ServiceFactory::get('templating')->render(
                $template,
                array_merge(
                    $arg,
                    ["content_type" => "subject"]
                )
            ),
            ServiceFactory::get('templating')->render(
                $template,
                array_merge(
                    $arg,
                    ["content_type" => "body"]
                )
            )
        );
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }



}