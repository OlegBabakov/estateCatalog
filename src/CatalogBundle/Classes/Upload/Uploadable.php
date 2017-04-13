<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.10.16
 * Time: 23:33
 */
namespace CatalogBundle\Classes\Upload;

interface Uploadable
{
    /**
     * Returns UploadedFile entity at upload moment. This field have to be used as FileType filed in Form and SonataAdmin
     * @return mixed
     */
    public function getFile();
    /**
     * Saves in entity file info as array (URL, absolute path, original name)
     * @param array $absolutePath
     * @return mixed
     */
    public function setFile($absolutePath);

    /**
     * Saves in entity specific public id to identify entity in public URLs like that: /entityName/download/{publicID}
     * @param $publicID
     * @return mixed
     */
    public function setFilePublicID($publicID);

    /**
     * Returns in entity specific public id to identify entity in public URLs like that: /entityName/download/{publicID}
     * @return mixed
     */
    public function getFilePublicID();
}