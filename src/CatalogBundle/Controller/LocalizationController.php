<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 26.11.16
 * Time: 21:52
 */

namespace CatalogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LocalizationController extends Controller
{
    const ALLOWED_CURRENCY = 'USD|EUR|RUB|VND';

    /**
     * Установка локали при заходе на корневой адрес URL="/"
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function rootRedirectAction(Request $request) {
        $locale = $request->getSession()->get('_locale');

        if (!$locale)
            $locale = $this->get('catalog.showcase_provider')->getShowcase()['locale'] ?? null;

        $referer = $request->headers->get('referer');
        if (!$locale && $referer) {
            $referer = $this->get('catalog.string_utils')->clearUrlPrefix($referer);
            try {
                $route = $this->get('router')->match($referer);
                $locale = isset($route['_locale']) ? $route['_locale'] : null;
            } catch (\Exception $e) {
            }
        }
        $locale = $locale ? : 'en';

        return $this->redirectToRoute('mainpage', [
            '_locale' => $locale
        ]);
    }

    public function changeLanguageAction(Request $request, $language) {
        $referer = $request->headers->get('referer');
        if ($referer) {
            $router = $this->get('router');
            $referer = $this->get('catalog.string_utils')->clearUrlPrefix($referer);
            try {
                $routeParams = $router->match($referer);
                if (isset($routeParams['_route'])) {
                    $redirectToRoute = $routeParams['_route'];
                    unset($routeParams['_route']);
                    unset($routeParams['_controller']);
                    $routeParams['_locale'] = $language;
                    return $this->redirect(
                        $router->generate(
                            $redirectToRoute,
                            $routeParams
                        )
                    );
                }
            } catch (\Exception $e) {
            }
        }
        return $this->redirectToRoute('root');
    }

    public function changeCurrencyAction($currency) {
        $request = $this->get('request_stack')->getCurrentRequest();
        if ($request) {
            if (strpos($this::ALLOWED_CURRENCY,$currency)===false) {
                throw new \Exception('This currency is not allowed');
            }
            $request->getSession()->set('currency', $currency);
        }

        $referer = $request->headers->get('referer');
        if ($referer) return $this->redirect($referer);
        return $this->redirect('/');
    }

}