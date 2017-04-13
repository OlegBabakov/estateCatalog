<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 22.11.16
 * Time: 13:21
 */

namespace CatalogBundle\Traits;


trait AddressableAdapterTrait
{
    protected $address;
    /**
     * Returns all address fields in an array(
     *       'country' => $this->getCountry(),
     *       'zipCode' => $this->getZipCode(),
     *       'streetNumber' => $this->getStreetNumber(),
     *       'streetName' => $this->getStreetName(),
     *       'city' => $this->getCity(),
     *       'latitude' => $this->getLatitude(),
     *       'longitude' => $this->getLongitude()
     *  )
     *
     * @return array
     */
    public function getAddress() {
        $res = [
           'latitude' => $this->getLatitude(),
           'longitude' => $this->getLongitude()
        ];
        $res = array_merge($res, $this->address ? : []);
        return $res;
    }

    public function setAddress($address) {
        $this->setLng($address['longitude']);
        $this->setLat($address['latitude']);
        unset($address['longitude']);
        unset($address['latitude']);
        $this->address = $address;
    }

    /**
     * Returns the latitude of the address.
     *
     * @return float
     */
    public function getLatitude() {
        return $this->lat;
    }

    /**
     * Returns the latitude of the address.
     *
     * @return float
     */
    public function getLongitude() {
        return $this->lng;
    }
}