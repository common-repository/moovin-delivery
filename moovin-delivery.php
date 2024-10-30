<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.moovin.me/
 * @since             1.0.0
 * @package           Moovin_Delivery
 *
 * @wordpress-plugin
 * Plugin Name:       Moovin Delivery
 * Plugin URI:        https://www.moovin.me/
 * Description:       Vos vendés nosotros entregamos tus paquetes, activa nuestro plugin y Moovin se encargara de entregar tus productos.
 * Version:           1.0.24
 * Author:            Moovin Developer
 * Author URI:        https://www.moovin.me/contacto/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       moovin-delivery
 * Domain Path:       /languages
 */

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Check if WooCommerce is active
if (!in_array( 'woocommerce/woocommerce.php',apply_filters('active_plugins', get_option( 'active_plugins' )))) {
  die;
}

define( 'MOOVIN_DELIVERY_VERSION', '1.0.22' );
define( 'MOOVIN_PLUGIN_PATH', plugin_dir_path(__FILE__));
define( 'MOOVIN_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * activator
 */
$activator = null;

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-moovin-delivery-activator.php
 */
function activate_moovin_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-moovin-delivery-activator.php';
	$activator = new Moovin_Delivery_Activator();
	$activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-moovin-delivery-deactivator.php
 */
function deactivate_moovin_delivery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-moovin-delivery-activator.php';
	$activator = new Moovin_Delivery_Activator();


	require_once plugin_dir_path( __FILE__ ) . 'includes/class-moovin-delivery-deactivator.php';
	$deactivator = new Moovin_Delivery_Deactivator($activator);
	$deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_moovin_delivery' );
register_deactivation_hook( __FILE__, 'deactivate_moovin_delivery' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-moovin-delivery.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.1
 */
function run_moovin_delivery() {
	$plugin = new Moovin_Delivery();
	$plugin->run();
}

run_moovin_delivery();

/**
 * Check if shipping plugin is enable
 */
global $wpdb;


require_once plugin_dir_path( __FILE__ ) . 'includes/class-moovin-delivery-activator.php';
$activator = new Moovin_Delivery_Activator();

$status_plugin = $wpdb->get_results(
	$wpdb->prepare(
			"SELECT * from " . $activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_STATUS"
	), ARRAY_A
);		


/**
 * The code that include shipping method.
 * This action is documented in includes/moovin-shipping.php
 */
if ($status_plugin[0]["value"] == "1"){
	
	/**
	* 
	* Check of need automatic woocommerce zone config
	*/
    $woocommerceAuto =  $wpdb->get_results(
        "SELECT * from " . $activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_WOOCOMMERCE_ZONE'" , ARRAY_A);

    if(isset($woocommerceAuto[0]["value"])){
		define( 'MOOVIN_WOOCOMMERCE_AUTO', $woocommerceAuto[0]["value"] );
    }else{
		define( 'MOOVIN_WOOCOMMERCE_AUTO', "1" );
	}

	/**
	 * Check if route service is enable
	 */
	$route_service = $wpdb->get_results(
		$wpdb->prepare(
				"SELECT * from " . $activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_ROUTE_SERVICE"
		), ARRAY_A
	);	

	if ($route_service[0]["value"] == "1"){
		require_once plugin_dir_path( __FILE__ )  . 'moovin-shipping.php';
	}


	/**
	 * Check if express service is enable
	 */
	$express_service = $wpdb->get_results(
		$wpdb->prepare(
				"SELECT * from " . $activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EXPRESS_SERVICE"
		), ARRAY_A
	);

	if ($express_service[0]["value"] == "1"){

	     /**
		 * Check schedule 
		 */
		$schedule_service = $wpdb->get_results(
			$wpdb->prepare(
					"SELECT * from " . $activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EXPRESS_SCHEDULE"
			), ARRAY_A
		);

		if(count($schedule_service) > 0){
			$dateCurrent = strtotime(date_i18n('Y-m-d H:i:s'));
			$dateStart =  strtotime(date_i18n("Y-m-d ".$schedule_service[0]["value"]));
			$dateFinal =  strtotime(date_i18n("Y-m-d ".$schedule_service[0]["value1"]));
	
			if($dateCurrent > $dateStart && $dateFinal > $dateCurrent){
				require_once plugin_dir_path( __FILE__ )  . 'moovin-shipping-express.php';
			}
		}
	}
}

