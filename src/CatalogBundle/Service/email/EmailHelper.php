<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 18.01.17
 * Time: 10:16
 */

namespace CatalogBundle\Service\email;

use CatalogBundle\Entity\Estate;

class EmailHelper extends EmailHelperBase
{
    /**
     * Отправка email
     * @param $formData
     * @param Estate $estate
     * @throws \Exception
     */
    public function estateSendMessageActionEmail($formData, Estate $estate) {
        $to = [$this->getRecipient($formData, $estate)->getEmailCanonical()];

        $template = $this->container->get('twig')->loadTemplate('@Catalog/email/estate/email_estate_send_message.html.twig');
        $subject = $template->renderBlock('subject', []);
        $body    = $template->renderBlock('body', array_merge(
            [
                'client' => $formData,
                'estate' => $estate
            ],
            $this->getDefaultTemplateParameters()
        ));
        if ($formData['sendCopy'] ?? false) $to[] = $formData['email'];

        $this->sendEmail(
            $to,
            $subject,
            $body,
            $formData['email'] ?? ''
        );
    }

    public function estateCallMeRequestActionEmail($formData, Estate $estate) {
        $to = [$this->getRecipient($formData, $estate)->getEmailCanonical()];

        $template = $this->container->get('twig')->loadTemplate('@Catalog/email/estate/email_estate_call_me_request.html.twig');
        $subject = $template->renderBlock('subject', []);
        $body    = $template->renderBlock('body', array_merge(
            [
                'data'   => $formData,
                'estate' => $estate
            ],
            $this->getDefaultTemplateParameters()
        ));

        $this->sendEmail(
            $to,
            $subject,
            $body,
            $formData['email'] ?? ''
        );
    }

    /**
     * @param $formData
     * @return null|\UserBundle\Entity\User
     * @throws \Exception
     */
    private function getRecipient($formData, Estate $estate) {
        if (!isset($formData['user']) || !is_numeric($formData['user']))
            throw new \Exception('Получатель задан некорректно');

        $recipient = $this->container->get('doctrine')->getRepository('UserBundle:User')->find($formData['user']);
        if (!$recipient)
            throw new \Exception('Получатель не найден');

        if (!$estate->getContactProfiles()->contains($recipient))
            throw new \Exception('Указанный пользователь не является контактом объекта недвижимости');

        return $recipient;
    }

    private function getDefaultTemplateParameters() {
        return [
            'domain' => $this->container->getParameter('domain'),
            'contactEmail' => $this->container->getParameter('email_from')
        ];
    }

}