<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 26.11.16
 * Time: 11:56
 */

namespace CatalogBundle\Service;


use Symfony\Component\HttpFoundation\RequestStack;

class StringUtils
{
    private $requestStack;

    /**
     * StringUtils constructor.
     * @param $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Удаляет из URL http , домен, localhost, app_dev.php оставляя только часть, которая отражает route
     * @param $url
     * @return mixed|null
     */
    public function clearUrlPrefix($url) {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $result = str_replace('http://localhost', '', $url);
            $result = str_replace($request->getSchemeAndHttpHost(), '', $result);
            $result = str_replace($request->getBaseUrl(), '', $result);
            return $result;
        }
        return null;
    }
}