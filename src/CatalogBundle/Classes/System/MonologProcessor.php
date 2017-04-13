<?php

namespace CatalogBundle\Classes\System;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use UserBundle\Entity\User;


class MonologProcessor
{
    const STARTPOS = 30;
    const ENDPOS = 30;

    private $level;
    /**@var ContainerInterface*/
    private $container;

    private $skipClassesPartials;

    public function __construct($level = Logger::DEBUG, array $skipClassesPartials = array('Monolog\\'))
    {
        $this->level = Logger::toMonologLevel($level);
        $this->skipClassesPartials = $skipClassesPartials;
    }

    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        // return if the level is not high enough
        if ($record['level'] < $this->level) {
            return $record;
        }

        $serverArray = print_r($_SERVER, 1);

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $record['main_message'] = $record['message'];
        if (mb_strlen($record['main_message']) > self::STARTPOS + self::ENDPOS) {
            $start = mb_substr($record['main_message'], 0, self::STARTPOS);
            $end = mb_substr($record['main_message'], -self::ENDPOS);
            $record['main_message'] = "{$start} ........... {$end}";
        }

        $record['extra'] = array_merge(
            $record['extra'],
            array(
                'currentUser' => $user ? $user->getUsername() : "null",
                'URL' => ($_SERVER['HTTP_HOST'] ?? '<HOST undefined>') . ($_SERVER['REQUEST_URI'] ?? '<URI undefined>'),
                '$_SERVER' => $serverArray
            )
        );

        return $record;
    }

    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    private function getUser()
    {
        /**
         * @var UsernamePasswordToken $token
         */
        $token = $this->container->get('security.token_storage')->getToken();

        if (!is_object($token)) {
            return null;
        }

        /**
         * @var User $user
         */
        $user = $token->getUser();

        if (!is_object($user)) {
            return null;
        }

        return $user;
    }
}
