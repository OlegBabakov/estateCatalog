<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 21.01.17
 * Time: 14:16
 */

namespace CatalogBundle\Command;

use CatalogBundle\Entity\SystemParameter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrencyRatesUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:currency-rates-update')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $rates = $this->updateFromCurrencyLayer();
            $this->save($rates);
        } catch (\Exception $e) {
            /*TODO: Оповестить разработчика если есть проблема с обновлением валют**/
//            $this->getContainer()->get('catalog.email_helper')
        }
    }

    private function updateFromCurrencyLayer() {
        $QUERY_URL = "http://www.apilayer.net/api/live?access_key=35a2a2a3530e6abfc1963910a9b40c3c&format=1";
        $RATES_QTY = 169; //Количество курсов валют поставляемых сервисом

        $content = file_get_contents($QUERY_URL);
        if (!$content)
            throw new \Exception('Проблема при обновлении курсов валют. Источник'. $QUERY_URL);

        $content = json_decode($content, true);
        if (!$content || !isset($content['source']) || !isset($content['quotes']))
            throw new \Exception('Пришли неполные данные. Источник: '. $QUERY_URL);

        if ($content['source'] !== 'USD')
            throw new \Exception('Базовая валюта не равна USD. Источник: '. $QUERY_URL);

        if (count($content['quotes']) != $RATES_QTY)
            throw new \Exception('Длина списка курсов валют не равна стандартному. Источник: '. $QUERY_URL);

        $result = [];
        foreach ($content['quotes'] as $currency => $rate) {
            $result[substr($currency, 3, 3)] = (float)$rate;
        }

        return $result;
    }

    private function save($rates) {
        $SYSTEM_PARAMETER_NAME = 'currencyRates';
        $rep = $this->getContainer()->get('doctrine')->getRepository('CatalogBundle:SystemParameter');
        $em  = $this->getContainer()->get('doctrine')->getManager();

        $entity = $rep->findOneByName($SYSTEM_PARAMETER_NAME);
        if (!$entity)
            $entity = new SystemParameter($SYSTEM_PARAMETER_NAME, null, SystemParameter::TYPE_ARRAY);
        $entity->setValue($rates);

        $em->persist($entity);
        $em->flush();
    }


}