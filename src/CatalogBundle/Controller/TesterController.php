<?php

namespace CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;


class TesterController extends Controller
{

    public function errorGeneratorAction($errorType)
    {
//        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
//            return Response::create('Доступ запрещен');
//        }

        if ($errorType == 'uncought_exception') {
            throw new Exception('Неперехваченое исключение');
        }

        if ($errorType == 'fatal_error') {
            require 'something';
        }

        if ($errorType == 'warning') {
            $a = 1 / 0;
            return Response::create('warning');
        }

        if ($errorType == 'notice') {
            $a = $b;
            return Response::create('notice');
        }

        $text = 'Тип ошибки не найден, убедитесь что параметр задан верно';
        return Response::create($text);

    }

}
