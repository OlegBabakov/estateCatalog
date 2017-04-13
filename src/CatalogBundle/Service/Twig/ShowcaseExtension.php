<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 26.11.16
 * Time: 22:07
 */

namespace CatalogBundle\Service\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Showcase - это способ отображения сайта. В частности showcase - это витрина показа предложений отдельных пользователей/организаций на суб-доменах
 * Class ShowcaseExtension
 * @package CatalogBundle\Service\Twig
 */
class ShowcaseExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    public $container;

    /**
     * ShowcaseExtension constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            'showcase' => new \Twig_SimpleFunction('showcase', [$this, 'getShowcase']),
        ];
    }

    public function getShowcase() {
        static $showcase;
        if (!$showcase)
            $showcase = $this->container->get('catalog.showcase_provider')->getShowcase();
        return $showcase;
    }


}