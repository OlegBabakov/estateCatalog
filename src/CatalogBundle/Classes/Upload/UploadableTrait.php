<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 25.11.16
 * Time: 9:55
 */

namespace CatalogBundle\Classes\Upload;


trait UploadableTrait
{
    /**
     * @var array
     */
    private $file;

    /**
     * @var string
     */
    private $filePublicID;

    /**
     * Set file
     *
     * @param array $file
     *
     * @return Uploadable
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return array
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set filePublicID
     *
     * @param string $filePublicID
     *
     * @return Uploadable
     */
    public function setFilePublicID($filePublicID)
    {
        $this->filePublicID = $filePublicID;

        return $this;
    }

    /**
     * Get filePublicID
     *
     * @return string
     */
    public function getFilePublicID()
    {
        return $this->filePublicID;
    }
}