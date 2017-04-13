<?php

namespace CatalogBundle\Controller;

use CatalogBundle\Form\MapFilterType;
use FOS\RestBundle\Tests\Functional\Bundle\TestBundle\Controller\RequestBodyParamConverterController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MapController extends Controller
{

    public function indexAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $host = $request->getHost();

        $filterForm = $this->createForm(MapFilterType::class, null, [
            'container' => $this->container
        ]);
        return $this->render('CatalogBundle:page:index.html.twig', [
            'filterForm' => $filterForm->createView()
        ]);
    }
}
