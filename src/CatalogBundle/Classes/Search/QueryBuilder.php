<?php

/**
 * Методы для облегчения работы с DQL строкой
 * На данный момент реализованы методы позволяющие добавить части запроса до и после блока WHERE
 * Created by PhpStorm.
 * User: oleg
 * Date: 28.09.16
 * Time: 16:31
 */

namespace CatalogBundle\Classes\Search;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;

class QueryBuilder
{
    private $queryPart = [];
    private $em = null;
    /**@var Query|NativeQuery $query*/
    public $Query;

    /**
     * QueryBuilder constructor.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->init();
        $this->em = $entityManager;
    }

    /**
     * Сброс параметров
     */
    public function init() {
        $this->Query = null;
        $this->queryPart['beforeWhere'] = '';
        $this->queryPart['join'] = '';
        $this->queryPart['where'] = '';
        $this->queryPart['afterWhere'] = '';
    }

    public function setBeforeWhere($queryBody) {
        $this->checkChangeQueryPermittion();
        $this->queryPart['beforeWhere'] .= $queryBody;
    }

    public function setAfterWhere($queryBody) {
        $this->checkChangeQueryPermittion();
        $this->queryPart['afterWhere'] .= $queryBody;
    }

    /**
     * Добавляет JOIN условие в блок join перед where
     * @param $joinString
     */
    public function addJoin($joinString) {
        $this->queryPart['join'] .= $joinString.' ';
    }

    /**
     * Добавляет в блок WHERE строку условия
     * @param $condition_string
     * @param string $logicOperatorBefore
     */
    public function addCondition($condition_string = null, $logicOperatorBefore = 'AND') {
        $this->checkChangeQueryPermittion();
        $conditionAfterBracket = str_replace(' ', '', substr($this->queryPart['where'], strrpos($this->queryPart['where'], '(')+1)); //Возвращает строку без пробелов после последней открытой скобки
        $this->queryPart['where'] .= $conditionAfterBracket ? "{$logicOperatorBefore} " : '';
        $this->queryPart['where'] .= $condition_string ? $condition_string.' ' : '';
    }

    public function addOpenBracket() {
        $this->queryPart['where'] .= '(';
    }

    /**
     * Добавляет закрывающую скобку в условие и удаляет пустые скобки "()"
     */
    public function addCloseBracket() {
        $this->queryPart['where'] .= ')';
        $this->queryPart['where'] = str_replace('()','', $this->queryPart['where']);
    }

    /**
     * Добавляет параметр по его названию (без двоеточия) с проверкой на наличие параметра в строке DQL (можно добавлять параметры без проверки того был ли параметр указан в запросе)
     * @param $parameterName
     * @param $parameterValue
     */
    public function addParameter($parameterName, $parameterValue) {
        if (!$this->Query) {
            $this->Query = $this->em->createQuery($this->getSQL());
        }

        if ($this->Query && stripos($this->Query->getSQL(), ':'.$parameterName)!==FALSE) {
            $this->Query->setParameter($parameterName, $parameterValue);
        }
    }

    /**
     * @return string
     */
    public function getSQL() {
        $result = '';
        $result .= $this->queryPart['beforeWhere'].' ';
        $result .= $this->queryPart['join'].' ';
        $result .= str_replace(' ', '', $this->queryPart['where']) ? "WHERE {$this->queryPart['where']} " : '';
        $result .= $this->queryPart['afterWhere'];
        return $result;
    }

    private function checkChangeQueryPermittion() {
        if ($this->Query) {
            throw new \Exception('Нельзя менять запрос после инициализации объекта запроса');
        }
    }
}