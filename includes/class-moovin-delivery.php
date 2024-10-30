<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.moovin.me/
 * @since      1.0.0
 *
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/includes
 * @author     Javier Hernández M <javier.hernandez@moovin.me>
 */
class Moovin_Delivery {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Moovin_Delivery_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MOOVIN_DELIVERY_VERSION' ) ) {
			$this->version = MOOVIN_DELIVERY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'moovin-delivery';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Moovin_Delivery_Loader. Orchestrates the hooks of the plugin.
	 * - Moovin_Delivery_i18n. Defines internationalization functionality.
	 * - Moovin_Delivery_Admin. Defines all hooks for the admin area.
	 * - Moovin_Delivery_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-moovin-delivery-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-moovin-delivery-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-moovin-delivery-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-moovin-delivery-public.php';

	
		$this->loader = new Moovin_Delivery_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Moovin_Delivery_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Moovin_Delivery_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Moovin_Delivery_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		//Action menu hook
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'moovin_management_menu');
		
		//Action hook admin ajax request
		$this->loader->add_action( 'wp_ajax_moovin_lib_handler', $plugin_admin, 'moovin_lib_ajax_handler');

		//Cron notifications
		$this->loader->add_filter( 'cron_schedules',$plugin_admin, 'add_every_three_minutes' );
		$this->loader->add_action( 'isa_add_every_three_minutes', $plugin_admin, 'every_three_minutes_event_func' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Moovin_Delivery_Public( $this->get_plugin_name(), $this->get_version());
	
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		
		$this->loader->add_action('woocommerce_after_checkout_billing_form', $plugin_public, 'moovin_woocommerce_after_checkout_billing_form');
		$this->loader->add_action('woocommerce_after_checkout_shipping_form', $plugin_public, 'moovin_woocommerce_after_checkout_shipping_form');
		
		$this->loader->add_action('woocommerce_review_order_after_shipping', $plugin_public, 'moovin_woocommerce_shipping_notice_displayed', 20 );
		$this->loader->add_action('woocommerce_after_shipping_calculator', $plugin_public, 'moovin_woocommerce_shipping_notice_displayed', 20 );

		//Woocommerce hook
		$this->loader->add_action('woocommerce_before_checkout_validation', $plugin_public, 'moovin_validate_address', 8, 2);
		$this->loader->add_action('woocommerce_after_checkout_validation', $plugin_public, 'moovin_validate_address', 8, 2);

		$this->loader->add_action('woocommerce_checkout_process', $plugin_public, 'moovin_validate_place_order', 10);

		$this->loader->add_filter('woocommerce_billing_fields', $plugin_public, 'moovin_manage_billing_address_fields', 999);
		$this->loader->add_filter('woocommerce_shipping_fields', $plugin_public, 'moovin_manage_shipping_address_fields', 999);
		$this->loader->add_action('woocommerce_thankyou', $plugin_public, 'moovin_delivery_complete_order', 10, 1);
		$this->loader->add_action('woocommerce_checkout_order_processed', $plugin_public, 'moovin_delivery_complete_order', 10, 1);

		$this->loader->add_filter('woocommerce_package_rates', $plugin_public, 'change_rates', 9999, 2);
		$this->loader->add_filter('woocommerce_checkout_update_order_review',$plugin_public, 'clear_wc_shipping_rates_cache', 9999, 2);
		$this->loader->add_filter('woocommerce_ship_to_different_address_checked',$plugin_public, 'moovin_ship_different_address' );
		$this->loader->add_filter('woocommerce_default_address_fields' ,$plugin_public, 'custom_override_checkout_fields', 99 );

		//Loader Ajax Calls
		$this->loader->add_action('wp_ajax_moovin_check_shipping_method', $plugin_public, 'moovin_check_shipping_method');
		$this->loader->add_action('wp_ajax_nopriv_moovin_check_shipping_method', $plugin_public, 'moovin_check_shipping_method');

		$this->loader->add_action('wp_ajax_moovin_address_insert', $plugin_public, 'moovin_delivery_addresses');
		$this->loader->add_action('wp_ajax_nopriv_moovin_address_insert', $plugin_public, 'moovin_delivery_addresses');

		$this->loader->add_action('wp_ajax_moovin_address_get', $plugin_public, 'moovin_get_delivery_address');
		$this->loader->add_action('wp_ajax_nopriv_moovin_address_get', $plugin_public, 'moovin_get_delivery_address');

		$this->loader->add_action('wp_ajax_moovin_zones_coverage_get', $plugin_public, 'moovin_zones_coverage_get');
		$this->loader->add_action('wp_ajax_nopriv_moovin_zones_coverage_get', $plugin_public, 'moovin_zones_coverage_get');

		$this->loader->add_action('wp_ajax_moovin_address_remove', $plugin_public, 'moovin_remove_delivery_address');
		$this->loader->add_action('wp_ajax_nopriv_moovin_address_remove', $plugin_public, 'moovin_remove_delivery_address');

		$this->loader->add_action('wp_ajax_moovin_address_clear', $plugin_public, 'moovin_clear_delivery_address');
		$this->loader->add_action('wp_ajax_nopriv_moovin_address_clear', $plugin_public, 'moovin_clear_delivery_address');

		$this->loader->add_action('wp_login', $plugin_public, 'moovin_session_end', 25, 5);

		//Cron notifications
		$this->loader->add_filter( 'cron_schedules', $plugin_public, 'add_every_three_minutes' );
		$this->loader->add_action( 'isa_add_every_three_minutes', $plugin_public, 'every_three_minutes_event_func' );
		
		//Update plugin
		$this->loader->add_action( 'plugins_loaded', $plugin_public, 'plugin_update' );
	}

	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Moovin_Delivery_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


}
