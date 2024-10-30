<?php 

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function moovin_shipping_activation_express(){
    if(class_exists('WooCommerce')){
        $wc_version = WC()->version;
        if( version_compare( $wc_version, '2.6', '>' ) ){
            $available_zones =  WC_Shipping_Zones::get_zones();

            $shipping_countries =  moovin_shipping_countries_express();

            $all_countries =  WC()->countries->get_countries();

            $available_zone_names = array();
            foreach ( $available_zones as $zone){
                if (!in_array($zone['zone_name'], $available_zone_names)){
                    $available_zone_names[] = $zone['zone_name'];
                }
            }

            if(MOOVIN_WOOCOMMERCE_AUTO == "1"){
                if (!in_array('MOOVIN Express Costa Rica',$available_zone_names)){
                    $new_zone_cr = new WC_Shipping_Zone();
                    $new_zone_cr->set_zone_name('MOOVIN Express Costa Rica');
    
                    $new_zone_cr->add_location('CR' , 'country');
    
                    $new_zone_cr->save();
    
                    $new_zone_cr->add_shipping_method('moovin_shipping_express');
                }
            }
        }
    }
}

function moovin_shipping_init_express(){
    if(class_exists('WooCommerce')){
        // Code if WooCommerce is active
        add_action('woocommerce_shipping_init', 'moover_shipping_express');
        add_action('woocommerce_shipping_methods', 'moover_add_shipping_methods_express');
    }
}

add_action('plugins_loaded', 'moovin_shipping_init_express');

function moover_shipping_express(){
    if(!class_exists('Moovin_Shipping_Express')){
        class Moovin_Shipping_Express extends WC_Shipping_Method{

            public $wc_version = '';

            public function moovin_tbl_parameters() {
                global $wpdb;
                return $wpdb->prefix . "plgn_moovin_parameters";
            }

            public function __construct($instance_id = 1 ){
                global $wpdb;

                $title = $wpdb->get_results(
                    $wpdb->prepare(
                            "SELECT * from " . $this->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EXPRESS_SERVICE"
                    ), ARRAY_A
                );

                $this->wc_version = WC()->version;
                $this->id = "moovin_shipping_express";
                $this->method_title = __('Moovin Shipping Express' , 'moovin_shipping_express');
                $this->method_description = __('Moovin Express Shipping Settings' , 'moovin_shipping_express');
                $this->title = __(($title[0]["value1"] == "" ? "MOOVIN Express 4H" : $title[0]["value1"]), 'moovin_shipping_express');
         
                if ( version_compare($this->wc_version , '2.6', '>=')){
                    $this->instance_id = absint($instance_id);
                    $this->supports = array(
                        'shipping-zones',
                        'instance-settings',
                        'instance-settings-modal',
                    );
                    $this->instance_form_fields = $this->form_fields;
                }

                $this->init();
                $this->enable = true;
            }
        
            public function init(){
                $this->init_form_fields();
                $this->init_settings();
                $this->countries = array_keys(moovin_shipping_countries_express());
                $this->availability = 'including';

                add_action('woocommerce_update_options_shipping_'.$this->id,array($this,'process_admin_options'));

            }

            public function init_form_fields(){
                 if ( version_compare($this->wc_version , '2.6', '<')){
                    $this->form_fields = array(
                        'enable' => array(
                        'title' => __( 'Enable', 'moovin_shipping_express' ),
                        'type' => 'checkbox',
                        'description' => __( 'Enable this shipping.', 'moovin_shipping_express' ),
                        'default' => 'yes'
                        ),
                    );
                }
            }
     
            public function calculate_shipping($package = array()){
                       
                $rate = array(
                    'label' => $this->title,
                    'cost' => '0',
                    'calc_tax' => 'per_item',
                    'taxes'   => false
                );

                // Register the rate
                $this->add_rate($rate);
            }
        }  
        moovin_shipping_activation_express();
    }
}

function moovin_shipping_countries_express(){
    return array(
        'CR' => 1,
    );
}

function moover_add_shipping_methods_express($methods){
    $methods['moovin_shipping_express'] = 'Moovin_Shipping_Express';
    return $methods;
}
