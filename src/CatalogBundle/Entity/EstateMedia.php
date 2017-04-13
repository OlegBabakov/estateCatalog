<?php

namespace CatalogBundle\Entity;
use CatalogBundle\Classes\Upload\Uploadable;
use CatalogBundle\Classes\Upload\UploadableTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * EstateMedia
 */
class EstateMedia implements Uploadable
{
    use UploadableTrait;
    /**
     * @var int
     */
    private $id;


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
     * Возвращает данные сущности, доступные для публичного использования через API
     * @Groups({"api"})
     */
    public function getPublicData() {
        return [
            'url' => $this->file['url'] ?? null,
            'thumbUrl' => $this->file['thumbUrl'] ?? null,
            'thumbSmallUrl' => $this->file['thumbSmallUrl'] ?? null,
            'isVideo' => strpos($this->file['type'] ?? '', 'video') !== false
        ];
    }

    /**
     * @var string
     */
    private $description;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return EstateMedia
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @var \CatalogBundle\Entity\Estate
     */
    private $estate;


    /**
     * Set estate
     *
     * @param \CatalogBundle\Entity\Estate $estate
     *
     * @return EstateMedia
     */
    public function setEstate(\CatalogBundle\Entity\Estate $estate = null)
    {
        $this->estate = $estate;

        return $this;
    }

    /**
     * Get estate
     *
     * @return \CatalogBundle\Entity\Estate
     */
    public function getEstate()
    {
        return $this->estate;
    }

    public function __toString()
    {
        return "(#{$this->getId()}) {$this->getDescription()}";
    }
    /**
     * @var bool
     */
    private $isMainThumb;

    /**
     * @var int
     */
    private $position;


    /**
     * Set isMainThumb
     *
     * @param bool $isMainThumb
     *
     * @return EstateMedia
     */
    public function setIsMainThumb($isMainThumb)
    {
        $this->isMainThumb = $isMainThumb;

        return $this;
    }

    /**
     * Get isMainThumb
     *
     * @return bool
     */
    public function getIsMainThumb()
    {
        return $this->isMainThumb;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return EstateMedia
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
