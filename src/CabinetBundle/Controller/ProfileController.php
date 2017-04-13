<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 19.12.16
 * Time: 8:14
 */

namespace CabinetBundle\Controller;

use CabinetBundle\Controller\Traits\ControllerPopupsTrait;
use CabinetBundle\Form\profile\UserAvatarType;
use CabinetBundle\Form\profile\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    use ControllerPopupsTrait;

    public function indexAction(Request $request)
    {
        $estateRep = $this->getDoctrine()->getRepository('CatalogBundle:Estate');

        $avatarForm = $this->createForm(UserAvatarType::class, $this->getUser(), [
            'container' => $this->get('service_container')
        ]);

        $userForm = $this->createForm(UserType::class, $this->getUser());

        $userForm->handleRequest($request);
        if ($userForm->isValid()) {
            $user = $userForm->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->popupSuccess('message_saved');
        }

        return $this->render('@Cabinet/page/profile/profile.html.twig', [
            'user' => $this->getUser(),
            'avatarForm' => $avatarForm->createView(),
            'userForm'   => $userForm->createView(),
            'estateQuantity' => $estateRep->getCountByUser($this->getUser())
        ]);
    }

    public function avatarChangeAction(Request $request) {
        $avatarForm = $this->createForm(UserAvatarType::class, $this->getUser(), [
            'container' => $this->get('service_container')
        ]);

        $avatarForm->handleRequest($request);
        if ($avatarForm->isValid()) {
            $user = $avatarForm->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('cabinet_profile');
    }
}