<?php

namespace CatalogBundle\Entity;
use Addressable\Bundle\Model\AddressableInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Serializer\Annotation\Groups;
use CatalogBundle\Traits\AddressableAdapterTrait;

/**
 * Estate
 */
class Estate implements AddressableInterface
{
    use AddressableAdapterTrait;
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lng;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $gallery;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gallery = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     * @Groups({"api","common"})
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return Estate
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     * @Groups({"api","common"})
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return Estate
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     * @Groups({"api","common"})
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @var integer
     */
    private $priceSell;

    /**
     * @var integer
     */
    private $priceRent;

    /**
     * @var array
     */


    /**
     * Set priceSell
     *
     * @param integer $priceSell
     *
     * @return Estate
     */
    public function setPriceSell($priceSell)
    {
        $this->priceSell = $priceSell;

        return $this;
    }

    /**
     * Get priceSell
     *
     * @return integer
     * @Groups({"api","common"})
     */
    public function getPriceSell()
    {
        return $this->priceSell;
    }

    /**
     * Set priceRent
     *
     * @param integer $priceRent
     *
     * @return Estate
     */
    public function setPriceRent($priceRent)
    {
        $this->priceRent = $priceRent;

        return $this;
    }

    /**
     * Get priceRent
     *
     * @return integer
     * @Groups({"api","common"})
     */
    public function getPriceRent()
    {
        return $this->priceRent;
    }

    /**
     * Add gallery
     *
     * @param \CatalogBundle\Entity\EstateMedia $gallery
     *
     * @return Estate
     */
    public function addGallery(\CatalogBundle\Entity\EstateMedia $gallery)
    {
        $this->gallery[] = $gallery;

        return $this;
    }

    /**
     * Remove gallery
     *
     * @param \CatalogBundle\Entity\EstateMedia $gallery
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeGallery(\CatalogBundle\Entity\EstateMedia $gallery)
    {
        return $this->gallery->removeElement($gallery);
    }

    /**
     * Get gallery
     *
     * @return \Doctrine\Common\Collections\Collection
     * @Groups({"api","common"})
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    public function __toString()
    {
        if ($this->treeLevel) return "(#{$this->getId()}) {$this->getTitle()}";
        return '<default>';
    }


    /**
     * @var array
     */
    private $image;

    public function setImageEntity(EstateMedia $image)
    {
        $imageInfo = $image->getFile();
        $this->image = [
            'url' => $imageInfo['url'],
            'thumbUrl' => $imageInfo['thumbUrl'],
            'thumbSmallUrl' => $imageInfo['thumbSmallUrl'],
        ];
        return $this;
    }

    public function getImageEntity()
    {
        return null;
    }

    /**
     * @param array $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return array
     * @Groups({"api","common"})
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * @var string
     */
    private $estateType;


    /**
     * Set estateType
     *
     * @param string $estateType
     *
     * @return Estate
     */
    public function setEstateType($estateType)
    {
        $this->estateType = $estateType;

        return $this;
    }

    /**
     * Get estateType
     *
     * @return string
     * @Groups({"api","common"})
     */
    public function getEstateType()
    {
        return $this->estateType;
    }
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $data;


    /**
     * Set title
     *
     * @param string $title
     *
     * @return Estate
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     * @Groups({"api","common"})
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Estate
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
     * Set data
     *
     * @param array $data
     *
     * @return Estate
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     * @Groups({"api","common"})
     */
    public function getData()
    {
        return $this->data;
    }

    private $creator;


    /**
     * Set creator
     *
     * @param \UserBundle\Entity\User $creator
     *
     * @return Estate
     */
    public function setCreator(\UserBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }
    /**
     * @var int
     */
    private $treeLeft;

    /**
     * @var int
     */
    private $treeRight;

    /**
     * @var int
     */
    private $treeLevel;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \CatalogBundle\Entity\Estate
     */
    private $root;

    /**
     * @var \CatalogBundle\Entity\Estate
     */
    private $parent;


    /**
     * Set treeLeft
     *
     * @param int $treeLeft
     *
     * @return Estate
     */
    public function setTreeLeft($treeLeft)
    {
        $this->treeLeft = $treeLeft;

        return $this;
    }

    /**
     * Get treeLeft
     *
     * @return int
     */
    public function getTreeLeft()
    {
        return $this->treeLeft;
    }

    /**
     * Set treeRight
     *
     * @param int $treeRight
     *
     * @return Estate
     */
    public function setTreeRight($treeRight)
    {
        $this->treeRight = $treeRight;

        return $this;
    }

    /**
     * Get treeRight
     *
     * @return int
     */
    public function getTreeRight()
    {
        return $this->treeRight;
    }

    /**
     * Set treeLevel
     *
     * @param int $treeLevel
     *
     * @return Estate
     */
    public function setTreeLevel($treeLevel)
    {
        $this->treeLevel = $treeLevel;

        return $this;
    }

    /**
     * Get treeLevel
     *
     * @return int
     */
    public function getTreeLevel()
    {
        return $this->treeLevel;
    }

    /**
     * Add child
     *
     * @param \CatalogBundle\Entity\Estate $child
     *
     * @return Estate
     */
    public function addChild(\CatalogBundle\Entity\Estate $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \CatalogBundle\Entity\Estate $child
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeChild(\CatalogBundle\Entity\Estate $child)
    {
        return $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set root
     *
     * @param \CatalogBundle\Entity\Estate $root
     *
     * @return Estate
     */
    public function setRoot(\CatalogBundle\Entity\Estate $root = null)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return \CatalogBundle\Entity\Estate
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param \CatalogBundle\Entity\Estate $parent
     *
     * @return Estate
     */
    public function setParent(\CatalogBundle\Entity\Estate $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CatalogBundle\Entity\Estate
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Профили пользователей, которые являются контактными лицами по объекту недвижимости
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contactProfiles;

    /**
     * Add contactProfile
     *
     * @param \UserBundle\Entity\User $contactProfile
     *
     * @return Estate
     */
    public function addContactProfile(\UserBundle\Entity\User $contactProfile)
    {
        $this->contactProfiles[] = $contactProfile;

        return $this;
    }

    /**
     * Remove contactProfile
     *
     * @param \UserBundle\Entity\User $contactProfile
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeContactProfile(\UserBundle\Entity\User $contactProfile)
    {
        return $this->contactProfiles->removeElement($contactProfile);
    }

    /**
     * Get contactProfiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContactProfiles()
    {
        return $this->contactProfiles;
    }
    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $deletedAt;


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Estate
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Estate
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Estate
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
