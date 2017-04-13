<?php

namespace CabinetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        $estateRep = $this->getDoctrine()->getRepository('CatalogBundle:Estate');

        return $this->render('@Cabinet/page/mainpage.html.twig', [
            'estateQuantity' => $estateRep->getCountByUser($this->getUser())
        ]);
    }
}
