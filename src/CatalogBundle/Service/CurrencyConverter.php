<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 08.12.16
 * Time: 21:34
 */

namespace CatalogBundle\Service;


use CatalogBundle\Classes\Search\EstateSearchCriteria;
use CatalogBundle\Entity\Estate;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class CurrencyConverter
{
    private $container;

    /**
     * CurrencyConverter constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->session = $this->container->get('session');
    }

    /**
     * Конвертирует поля объектов или массива объектов отвечающих за валюту
     * @param $object
     * @param $backConvert, true - возврат коэф. для преобразования из валюты пользоваля в базовую
     * @return mixed
     */
    public function convert($object, $backConvert = false) {
        if (is_array($object) || $object instanceof Collection) {
            foreach ($object as $item) {
                $this->runEntityConverter($item, $backConvert);
            }
        } else {
            $this->runEntityConverter($object, $backConvert);
        }
        return $object;
    }

    /**
     * Конвертирует поля объекта отвечающие за цену
     * @param $object
     * @param $backConvert, true - возврат коэф. для преобразования из валюты пользоваля в базовую
     */
    private function runEntityConverter($object, $backConvert) {
        static $currency;

        if (!$currency) $currency = $this->session->get('currency') ? : 'USD';

        $factor = $this->getExchangeRateFactor($currency, $backConvert);
        if ($object instanceof Estate) {
            $object->setPriceSell($object->getPriceSell() * $factor);
            $object->setPriceRent($object->getPriceRent() * $factor);
        }

        if ($object instanceof EstateSearchCriteria) {
            if (!is_null($object->price['min']))
                $object->price['min'] = (int)floor($object->price['min'] * $factor);
            if (!is_null($object->price['max']))
                $object->price['max'] = (int)ceil($object->price['max'] * $factor);
        }
    }

    /**
     * Возвращает умножающий коэффициент для преобразования из базовой валюты в валюту пользователя
     * @param $currency, идентификатор валюты https://ru.wikipedia.org/wiki/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D0%B7%D0%BD%D0%B0%D0%BA%D0%BE%D0%B2_%D0%B2%D0%B0%D0%BB%D1%8E%D1%82
     * @param $backConvert, true - возврат коэф. для преобразования из валюты пользоваля в базовую
     * @return float|int
     * @throws \Exception
     */
    private function getExchangeRateFactor($currency, $backConvert) {
        static $rates;

        if (!$rates) {
            /**@var \CatalogBundle\Entity\SystemParameter $currencyRates*/
            $currencyRates = $this->container->get('doctrine')->getRepository('CatalogBundle:SystemParameter')->findOneByName('currencyRates');
            if (!$currencyRates) throw new \Exception('В системных параметрах отсутствует информация о курсах валют');
            $rates = $currencyRates->getValue();
        }

        if (!isset($rates[$currency])) throw new \Exception('Отсутствует запрашиваемая валюта');

        if ($backConvert) return 1/$rates[$currency];
        return $rates[$currency];
    }


}