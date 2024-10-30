<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.moovin.me/
 * @since      1.0.0
 *
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/includes
 * @author     Javier Hernández M <javier.hernandez@moovin.me>
 */
class Moovin_Delivery_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	private $moovin_activator;

    public function __construct($activator)
    {
        $this->moovin_activator = $activator;
    }

	public function deactivate() {
		global $wpdb;

		//Drop Tables when plugin uninstall
        //$wpdb->query("DROP TABLE IF EXISTS " . $this->moovin_activator->moovin_tbl_parameters());
        $wpdb->query("DROP TABLE IF EXISTS " . $this->moovin_activator->moovin_tbl_cat_documents());
        $wpdb->query("DROP TABLE IF EXISTS " . $this->moovin_activator->moovin_tbl_orders());
        $wpdb->query("DROP TABLE IF EXISTS " . $this->moovin_activator->moovin_tbl_order_products());
		$wpdb->query("DROP TABLE IF EXISTS " . $this->moovin_activator->moovin_tbl_pkgs_sizes());
		$wpdb->query("DROP TABLE IF EXISTS " . $this->moovin_activator->moovin_tbl_notification_order());

		//Drop Post
		$page = $wpdb->get_row(
			$wpdb->prepare(
					"SELECT ID from " . $wpdb->prefix . "posts WHERE post_name = %s", 'moovin-delivery-plg'
			)
		);

		$page_id = $page->ID;
		if($page_id > 0){
			wp_delete_post($page_id, true);
		}

	}
}
