<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 25.11.16
 * Time: 12:59
 */

namespace CatalogBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressMapType extends \Addressable\Bundle\Form\Type\AddressMapType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'secure' => false,
                'google_api_key' => 'AIzaSyCPSUg1y-TDXM6N8hX4vD_eWdF4j8rzcsg',
                'map_width' => '100%',    // the width of the map
                'map_height' => '500px',  // the height of the map
                'default_lat' => 12.246746625048596,    // the starting position on the map
                'default_lng' => 109.20804977416992, // the starting position on the map
                'include_current_position_action' => false, // whether to include the set current position button
                'country_field' => array(
                    'name' => 'country',
                    'type' => 'text',
                    'options' => array(
                        'required' => false
                    )
                ),
                'city_field' => array(
                    'name' => 'city',
                    'type' => 'text',
                    'options' => array(
                        'required' => false
                    )
                ),
                'street_name_field' => array(
                    'name' => 'streetName',
                    'type' => 'text',
                    'options' => array(
                        'required' => false
                    )
                ),
                'street_number_field' => array(
                    'name' => 'streetNumber',
                    'type' => 'text',
                    'options' => array(
                        'required' => false
                    )
                ),
                'latitude_field' => array(
                    'name' => 'latitude',
                    'type' => 'hidden',
                    'options' => array(
                        'required' => true
                    )
                ),
                'longitude_field' => array(
                    'name' => 'longitude',
                    'type' => 'hidden',
                    'options' => array(
                        'required' => true
                    )
                ),
                'zipcode_field' => array(
                    'name' => 'zipCode',
                    'type' => 'hidden',
                    'options' => array(
                        'required' => false
                    )
                ),
                'zipacode' => array(
                    'name' => 'zipCode',
                    'type' => 'hidden',
                    'options' => array(
                        'required' => false
                    )
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['secure'] = $options['secure'];
        $view->vars['google_api_key'] = $options['google_api_key'];
        // fields
        $view->vars['country_field'] = $options['country_field']['name'];
        $view->vars['city_field'] = $options['city_field']['name'];
        $view->vars['streetname_field'] = $options['street_name_field']['name'];
        $view->vars['streetnumber_field'] = $options['street_number_field']['name'];
        $view->vars['lat_field'] = $options['latitude_field']['name'];
        $view->vars['lng_field'] = $options['longitude_field']['name'];
        $view->vars['zipcode_field'] = $options['zipcode_field']['name'];

        // conf
        $view->vars['map_width'] = $options['map_width'];
        $view->vars['map_height'] = $options['map_height'];
        $view->vars['default_lat'] = $options['default_lat'];
        $view->vars['default_lng'] = $options['default_lng'];
        $view->vars['include_current_position_action'] = $options['include_current_position_action'];
    }

}