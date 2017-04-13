<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 30.10.16
 * Time: 16:03
 */

namespace RestApiBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Implements symfony serializer wrap
 * Class Serializer
 * @package RestApiBundle\Service
 */
class SerializerService
{
    /**@var Serializer */
    private $serializer;
    /**
     * Serializer constructor.
     */
    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function getSerializer() {
        return $this->serializer;
    }
}