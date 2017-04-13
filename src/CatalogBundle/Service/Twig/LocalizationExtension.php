<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 26.11.16
 * Time: 22:07
 */

namespace CatalogBundle\Service\Twig;

use Symfony\Component\HttpFoundation\Session\Session;

class LocalizationExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**@var Session*/
    public $session;

    /**
     * LocalizationExtension constructor.
     * @param $session
     */
    public function __construct($session)
    {
        $this->session = $session;
    }

    public function getGlobals()
    {
        return [
            'currencySymbol' => $this->getCurrencySymbol()
        ];
    }

    public function getFilters()
    {
        return [
            'price' => new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        ];
    }

    public function priceFilter($price)
    {
        return number_format($price, 0, '.', ',') . ' ' . $this->getCurrencySymbol();
    }

    public function getCurrencySymbol() {
        static $currencySymbol;
        if (!$currencySymbol) {
            $currencySymbolList = [
                'USD' => '&#36;',
                'EUR' => '&#8364;',
                'RUB' => '&#8381;',
                'VND' => '&#8363;'
            ];
            $currency = $this->session->get('currency') ? : 'USD';
            $currencySymbol = isset($currencySymbolList[$currency]) ? $currencySymbolList[$currency] : '';
        }
        return $currencySymbol;
    }


}