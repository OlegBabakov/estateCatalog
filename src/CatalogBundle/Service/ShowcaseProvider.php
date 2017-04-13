<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 24.01.17
 * Time: 11:51
 */

namespace CatalogBundle\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;

class ShowcaseProvider
{
    private $container;

    private $showcaseList = [
        'zimavteple' => [
            'name'   => 'zimavteple',
            'locale' => 'ru'
        ]
    ];

    /**
     * ShowcaseProvider constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getShowcase() {
        static $showcase;
        if (!$showcase)
            $showcase = $this->showcaseList[$this->detectShowcase()] ?? null;
        return $showcase;
    }

    private function detectShowcase() {
        static $showcase;
        if ($showcase) return $showcase;

        $request = $this->container->get('request_stack')->getCurrentRequest();
        if (!$request) return null;

        $hostParts = explode('.', $request->getHost());
        $subdomain = (isset($hostParts[0]) && count($hostParts) == 3) ? $hostParts[0] : null;
        if ($subdomain)
            $showcase = $subdomain;

        return $showcase;
    }
}