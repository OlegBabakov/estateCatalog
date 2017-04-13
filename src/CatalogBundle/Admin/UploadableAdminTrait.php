<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 25.11.16
 * Time: 10:25
 */

namespace CatalogBundle\Admin;
use CatalogBundle\Classes\Upload\Uploadable;

trait UploadableAdminTrait
{
    private function getFileFieldOptions() {
        $fileFieldOptions = [
            'required'   => false,
            'data_class' => null
        ];

        $object = $this->getSubject();
        if ($object instanceof Uploadable && is_array($object->getFile())) {
            $fileInfo = $object->getFile();

            if (stripos($fileInfo['mimeType'], 'image')!==false) {
                $fileFieldOptions['help'] = '<img src="'.$fileInfo['url'].'" class="admin-preview" style="width:50%" />';
            }

            if (stripos($fileInfo['mimeType'], 'video')!==false) {
                $fileFieldOptions['help'] = "<i class='fa fa-file-video-o fa-2x' aria-hidden='true'></i>&nbsp;&nbsp; {$fileInfo['originalName']}";
            }
        }

        return $fileFieldOptions;
    }
}