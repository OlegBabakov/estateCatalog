<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.11.16
 * Time: 16:49
 */

namespace CatalogBundle\Service\manager;

use CatalogBundle\Classes\Search\EstateSearchCriteria;
use CatalogBundle\Entity\Estate;
use CatalogBundle\Service\CurrencyConverter;
use Doctrine\Bundle\DoctrineBundle\Registry;
use RestApiBundle\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class EstateManager
{
    /**@var Registry */
    private $doctrine;
    /**@var RequestStack */
    private $requestStack;
    /**@var CurrencyConverter*/
    private $currencyConverter;

    /**
     * EstateManager constructor.
     * @param $doctrine
     * @param $requestStack
     * @param $currencyConverter
     */
    public function __construct($doctrine, $requestStack, $currencyConverter) {
        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
        $this->currencyConverter = $currencyConverter;
    }

    public function search() {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) return [];

        $searchCriteria = EstateSearchCriteria::buildFromRequest($request);
        $this->currencyConverter->convert($searchCriteria, true);

        $result =  $this->doctrine->getRepository('CatalogBundle:Estate')->searchByCriteria($searchCriteria);
        $this->currencyConverter->convert($result['collection']);

        return $result;

    }

    public function save(Estate $estate) {
        $em = $this->doctrine->getManager();

        if (!$estate->getParent())
            $estate->setParent($this->getRootEstate());

        $this->copyDataFromParent($estate);

        $em->persist($estate);
        $em->flush();

        return true;
    }

    private function copyDataFromParent(Estate $estate) {
        if (!$estate->getParent() || !$estate->getParent()->getTreeLevel()) return false;
        $parent = $estate->getParent();

        //Копирование данных о местоположении
        if (!$estate->getLat() && !$estate->getLng() && $parent->getLat() && $parent->getLng())
            $estate->setAddress($parent->getAddress());

        //Копирование данных о здании
        if ($this->arrayDataIsEmpty($estate->getData()['building']) && !$this->arrayDataIsEmpty($parent->getData()['building'])) {
            $data = $estate->getData();
            $data['building'] = $parent->getData()['building'];
            $estate->setData($data);
        }

        return true;
    }

    private function getRootEstate() {
        $rootEstate = $this->doctrine->getRepository('CatalogBundle:Estate')->findOneByTreeLevel(0);

        if (!$rootEstate) {
            $em = $this->doctrine->getManager();
            $rootEstate = new Estate();
            $rootEstate->setEstateType('root');
            $em->persist($rootEstate);
            $em->flush();
        }

        return $rootEstate;
    }

    private function arrayDataIsEmpty($array) {
        foreach ($array as $value) {
            if (is_array($value) && !$this->arrayDataIsEmpty($value))
                return false;
            if (!is_array($value) && !is_null($value))
                return false;
        }
        return true;
    }

}