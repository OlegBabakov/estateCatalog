<?php

namespace CatalogBundle\Entity;

/**
 * SystemParameter
 */
class SystemParameter
{
    const TYPE_STRING = 0;
    const TYPE_BOOLEAN = 1;
    const TYPE_INTEGER = 2;
    const TYPE_ARRAY = 3;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * SystemParameter constructor.
     * @param string $name
     * @param mixed $value
     * @param int $type
     * @throws \Exception
     */
    public function __construct(string $name, $value = null, int $type = self::TYPE_STRING)
    {
        $this->name = $name;
        $this->checkType($type);
        $this->type = $type;
        $this->setValue($value);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return SystemParameter
     */
    protected function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return SystemParameter
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $value
     * @return $this
     * @throws \Exception
     */
    public function setValue($value)
    {
        $this->checkType($this->type);
        switch ($this->type) {
            case self::TYPE_STRING  : $this->value = (string)$value; break;
            case self::TYPE_BOOLEAN : $this->value = (bool)$value;   break;
            case self::TYPE_INTEGER : $this->value = (int)$value;    break;
            case self::TYPE_ARRAY   : $this->value = json_encode((array)$value);
        }

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        switch ($this->type) {
            case self::TYPE_STRING  : return $this->value;       break;
            case self::TYPE_BOOLEAN : return (bool)$this->value; break;
            case self::TYPE_INTEGER : return (int)$this->value;  break;
            case self::TYPE_ARRAY   : return json_decode($this->value, true);
        }
        return null;
    }

    public function getValueStringView() {
        return var_export($this->value, true);
    }

    /**
     * @param $type
     * @return bool
     * @throws \Exception
     */
    private function checkType($type) {
        if (!in_array($type, [self::TYPE_STRING, self::TYPE_BOOLEAN, self::TYPE_INTEGER, self::TYPE_ARRAY]))
            throw new \Exception('Задан некорректный тип системного параметра');
        return true;
    }
}

