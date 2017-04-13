<?php

/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.10.16
 * Time: 23:05
 */

namespace CatalogBundle\Service\Upload;

use CatalogBundle\Classes\Upload\Uploadable;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class FileUploader
{

  private $kernelRootDir = null;

  /**
   * FileUploader constructor.
   * @param $kernelRooDir
   */
  public function __construct($kernelRootDir)
  {
    $this->kernelRootDir = $kernelRootDir;
  }

  /**
   * @param Uploadable $entity
   * @param $uploadWebPath, upload Directory (url which provides access to file from browser). Example: '/uploads/Document'
   * @param $preUpdateEventArgs, it's needed to pass PreUpdateEventArgs object and get old state of object
   * @return bool
   */
  public function upload(Uploadable $entity, $uploadWebPath, $preUpdateEventArgs = null)
  {
    $file = $entity->getFile();

    if (!$file instanceof UploadedFile)
    {
      if ($preUpdateEventArgs instanceof PreUpdateEventArgs && $preUpdateEventArgs->hasChangedField('file'))
      {
        $entity->setFile($preUpdateEventArgs->getOldValue('file')); //return old value back to prevent override update with empty file field
      }
      return false;
    }

    $filePublicName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
    $absoluteWebPath = $this->kernelRootDir . '/../web' . $uploadWebPath;
    $savedFile = $file->move($absoluteWebPath, $filePublicName);

    $entity->setFilePublicID($filePublicName);

    $fileInfo = [];
    $fileInfo['url'] = "{$uploadWebPath}/{$filePublicName}";
    $fileInfo['absolutePath'] = "{$absoluteWebPath}/{$filePublicName}";
    $fileInfo['originalName'] = $file->getClientOriginalName();
    $fileInfo['mimeType'] = $file->getClientMimeType();
    $fileInfo['size'] = $savedFile->getSize();

    $entity->setFile($fileInfo);
    return true;
  }

  /**
   * Returns file content to browser. File will be shown if browser supports preview of current file type
   * @param Uploadable $entity
   * @return Response
   */
  public function preview(Uploadable $entity)
  {
    return $this->fileResponseBuilder($entity);
  }

  /**
   * Returns file content as attachment
   * @param Uploadable $entity
   * @return Response
   */
  public function download(Uploadable $entity)
  {
    return $this->fileResponseBuilder($entity, true);
  }

  /**
   * return Response with file content
   * @param Uploadable $entity
   * @param bool $isAttachment
   * @return Response
   * @throws \Exception
   */
  private function fileResponseBuilder(Uploadable $entity, $isAttachment = false)
  {
    if (!is_array($entity->getFile()))
    {
      throw new \Exception('$entity->getFile() is not array');
    }

    $fileInfo = $entity->getFile();

    $filePath = $fileInfo['absolutePath'];
    $fileFound = file_exists($filePath);
    if (!$fileFound)
    {
      $filePath = $this->kernelRootDir . '/../web'. $fileInfo['url']; //Trying to get file from web dir by relative path
      $fileFound = file_exists($filePath);
    }

    if (!$fileFound)
    {
      throw new \Exception('File not found');
    }

    $response = new Response();
    $response->headers->set('Cache-Control', 'private');
    $response->headers->set('Content-type', $fileInfo['mimeType']);
    $response->headers->set('Content-Disposition', ($isAttachment ? 'attachment; ' : '') . 'filename="' . $fileInfo['originalName'] . '";');
    $response->headers->set('Content-length', filesize($filePath));
    $response->sendHeaders();
    $response->setContent(
            file_get_contents($filePath)
    );

    return $response;
  }

}
