<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.moovin.me/
 * @since      1.0.0
 *
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/includes
 * @author     Javier Hernández M <javier.hernandez@moovin.me>
 */
class Moovin_Delivery_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function activate() {

        global $wpdb;

		//Create table to store parameters moovin
		if ($wpdb->get_var("show tables like '" . $this->moovin_tbl_parameters() . "'") != $this->moovin_tbl_parameters()) {
			
			$sql_parameters = "CREATE TABLE `".$this->moovin_tbl_parameters()."` (
				`id_parameter` int(11)  AUTO_INCREMENT,
				`created_at` timestamp ,
				`edited_at` timestamp ,
				`cod_parameter` varchar(50) NOT NULL,
				`name` text ,
				`value` text ,
				`value1` text ,
				`value2` text ,
				`status` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id_parameter`)
			  ) ENGINE=InnoDB ";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
			dbDelta($sql_parameters);

			$sqlParametersInit = "INSERT INTO `" . $this->moovin_tbl_parameters() . "` (`id_parameter`, `created_at`, `edited_at`, `cod_parameter`, `name`, `value`, `value1`, `status`, `value2`) VALUES
								(1, '2021-06-05 01:27:02', '2021-06-05 01:27:02', 'MOOVIN_USERNAME_PROD', 'Usuario dado por moovin para la conectividad al ambiente de produccion', '', '', 1, ''),
								(2, '2021-06-05 01:27:02', '2021-06-05 01:27:02', 'MOOVIN_PASSWORD_PROD', 'Contraseña dada por Moovin para la conectividad al ambiente de produccion', '', '', 1, ''),
								(3, '2021-06-05 01:27:02', '2021-06-05 01:06:49', 'MOOVIN_USERNAME_SANDBOX', 'Usuario dada por Moovin para la conectividad al ambiente de pruebas', '', '', 1, ''),
								(4, '2021-06-05 01:27:02', '2021-06-05 01:06:49', 'MOOVIN_PASSWORD_SANDBOX', 'Contraseña dada por Moovin para la conectividad al ambiente de pruebas', '', '', 1, ''),
								(5, '2021-06-05 01:27:02', '2021-06-28 21:45:40', 'MOOVIN_URL_PROD', 'Direccion URL para ambiente de produccion', 'https://moovin.me/moovinApiWebServices-cr', 'https://hn.moovin.me//moovinApiWebServices-hn', 1, ''),
								(6, '2021-06-05 01:27:02', '2021-06-28 21:45:41', 'MOOVIN_URL_SANDBOX', 'Direccion URL para ambiente de pruebas', 'https://developer.moovin.me/moovinApiWebServices-cr', 'https://hn.developer.moovin.me/moovinApiWebServices-hn', 1, ''),
								(7, '2021-06-05 01:27:02', '2021-07-01 17:07:14', 'MOOVIN_TOKEN_SANDBOX', 'Token para autenticacion con api', '', '', 1, ''),
								(8, '2021-06-05 01:27:02', '2021-06-28 21:45:46', 'MOOVIN_TOKEN_PROD', 'Token para autenticacion con api', '', '', 1, ''),
								(9, '2021-06-05 01:27:02', '2021-06-28 21:45:49', 'MOOVIN_PKG_SIZE', 'Tamaño de paquete por defecto para envio', '', '', 1, ''),
								(10, '2021-06-05 01:27:02', '2021-06-28 21:45:52', 'MOOVIN_PKG_WEIGHT', 'Peso de paquete por defecto para envio', '', '', 1, ''),
								(11, '2021-06-05 01:27:02', '2021-06-30 12:06:04', 'MOOVIN_CURRENT_ENV', 'Ambiente actualmente seleccionado', '', '', 1, ''),
								(12, '2021-06-05 01:27:02', '2021-07-02 15:07:20', 'MOOVIN_DEFAULT_LOCATION', '', '', '', 1, ''),
								(13, '2021-06-05 01:27:02', '2021-06-29 14:06:33', 'MOOVIN_GOOGLE_MAP', 'Configuracion Google Map', '', '15', 0, '0'),
								(14, '2021-06-05 01:27:02', '2021-06-28 21:06:37', 'MOOVIN_HERE_MAP', 'Configuracion Here Map', '', '15', 0, '0'),
								(15, '2021-06-05 01:27:02', '2021-06-30 12:06:04', 'MOOVIN_STATUS', 'Estado de plugin ', '0', '', 1, ''),
								(16, '2021-06-13 03:14:33', '2021-06-28 21:46:04', 'MOOVIN_CONTACT', '', '', '', 0, ''),
								(17, '2021-06-14 18:28:47', '2021-06-28 21:46:06', 'MOOVIN_TASK_DOCUMENT', 'Pedir documentos de indentidad', '1', NULL, 1, ''),
								(18, '2021-06-29 01:42:30', '2021-06-29 18:06:33', 'MOOVIN_COLLECT_AUTO', 'Recoleccion Automatica', '0', NULL, 1, ''),
								(19, '2021-06-29 23:52:35', '2021-06-29 18:06:33', 'MOOVIN_EXPRESS_SERVICE', 'Servicios express', '1', 'MOOVIN Express 4H', 1, ''),
								(20, '2021-06-29 23:52:35', '2021-06-29 18:06:33', 'MOOVIN_ROUTE_SERVICE', 'Servicios en ruta', '1', 'MOOVIN 24H a 48H', 1, ''),
								(21, '2021-07-02 21:22:29', '2021-07-02 15:07:20', 'MOOVIN_FULFILLMENT', 'Fulfillment', '0', '1', 1, ''),
								(22, '2021-07-02 21:22:29', '2021-07-02 15:07:20', 'MOOVIN_EMAIL_NOTIFICATION', 'Enviar notificacion a cliente', '0', '', 1, ''),
								(23, '2021-07-02 21:22:29', '2021-07-02 15:07:20', 'MOOVIN_EXPRESS_SCHEDULE', 'Horario de servicio express', '', '', 1, ''),
								(24, '2021-07-02 21:22:29', '2021-07-02 15:07:20', 'MOOVIN_PROMO', 'Promo envio gratis', '0', '0', 1, '');";

			$wpdb->query(
				$sqlParametersInit
			);

		}


		//Create table to store catalog documents
		if ($wpdb->get_var("show tables like '" . $this->moovin_tbl_cat_documents() . "'") != $this->moovin_tbl_cat_documents()) {
			$sql_documents="CREATE TABLE `".$this->moovin_tbl_cat_documents()."` (
				`id_document` int(11)  AUTO_INCREMENT,
				`created_at` timestamp,
				`edited_at` timestamp,
				`cod_document` varchar(50) NOT NULL,
				`name` varchar(100) ,
				`description` varchar(100) ,
				`status` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id_document`)
			  ) ENGINE=InnoDB ";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
			dbDelta($sql_documents);

			$sqlDocumentsInit = "INSERT INTO `".$this->moovin_tbl_cat_documents()."` 
			(`id_document`, `created_at`, `edited_at`, `cod_document`, `name`, `description`, `status`) VALUES 
			(NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'image', 'Imagen', 'Documento de tipo imagen', '1'), 
			(NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'document', 'Documento', 'Documentos fisícos', '1'), 
			(NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'amount', 'Recoger dinero', 'Recoger dinero', '1'),
			(NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'review', 'Revision de documento', 'Revisión de un documento', '1'), 
			(NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'ironing', 'Tarjeta', 'Captura de relieve de tarjetas', '1');";

			$wpdb->query(
				$sqlDocumentsInit
			);
		}

		//Create table to store orders
		if ($wpdb->get_var("show tables like '" . $this->moovin_tbl_orders() . "'") != $this->moovin_tbl_orders()) {
			$sql_orders="CREATE TABLE `" . $this->moovin_tbl_orders() . "` (
				`order_id` int NOT NULL,
				`date_created` timestamp ,
				`num_items_sold` double ,
				`total_sales` double ,
				`tax_total` double ,
				`shipping_total` double ,
				`net_total` double ,
				`id_estimation` int ,
				`id_delivery` int ,
				`email` varchar(255) ,
				`email_account` varchar(255) ,
				`prepared` int ,
				`latitude_collect` varchar(100) ,
				`longitude_collect` varchar(100) ,
				`location_alias_collect` text ,
				`contact_collect` varchar(255) ,
				`phone_collect` varchar(255) ,
				`notes_collect` text ,
				`latitude_delivery` varchar(100) ,
				`longitude_delivery` varchar(100) ,
				`location_alias_delivery` text ,
				`contact_delivery` varchar(255) ,
				`phone_delivery` varchar(255) ,
				`notes_delivery` text ,
				`ensure` int ,
				`status_order_delivery_moovin` varchar(50) ,
				`date_update_moovin` timestamp ,
				`qr_code` text ,
				`response_order_created` text ,
				`date_order_created` timestamp ,
				`response_order_ready` text ,
				`date_order_ready` timestamp ,
				`shipping_method` varchar(50) ,
				`id_package_moovin` varchar(50) ,
				`fulfillment` tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (`order_id`)
			  ) ENGINE=InnoDB ";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
			dbDelta($sql_orders);
		}

		//Create table to store products order
		if ($wpdb->get_var("show tables like '" . $this->moovin_tbl_order_products() . "'") != $this->moovin_tbl_order_products()) {

			$sql_products="CREATE TABLE `" . $this->moovin_tbl_order_products() . "` ( 
				`id_order_product` INT(11) NOT NULL AUTO_INCREMENT ,
				`order_id` BIGINT NOT NULL , 
				`quantity` DOUBLE  , 
				`name_product` VARCHAR(250)  ,
				`description` TEXT NOT NULL ,
				`length` DOUBLE  , 
				`width` DOUBLE  , 
				`high` DOUBLE  , 
				`weight` DOUBLE  ,
				`price` DOUBLE  , 
				`code_product` VARCHAR(100) NOT NULL , 
				PRIMARY KEY (`id_order_product`)) 
				ENGINE = InnoDB;";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
			dbDelta($sql_products);		
		}


		//Create table to notification orders
		if ($wpdb->get_var("show tables like '" . $this->moovin_tbl_notification_order() . "'") != $this->moovin_tbl_notification_order()) {

			$sql_notification_order="CREATE TABLE `" . $this->moovin_tbl_notification_order() . "` ( 
					`id_notification` INT(11) NOT NULL AUTO_INCREMENT ,
					`order_id` BIGINT NOT NULL , 
					`email` VARCHAR(250)  , 
					`status` tinyint(1) NOT NULL DEFAULT '0' ,
					`created_at` timestamp ,
					`sent_at` timestamp  , 
					PRIMARY KEY (`id_notification`)) 
					ENGINE = InnoDB;";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
			dbDelta($sql_notification_order);		
		}


		//Create table to packages sizes
		if ($wpdb->get_var("show tables like '" . $this->moovin_tbl_pkgs_sizes() . "'") != $this->moovin_tbl_pkgs_sizes()) {

			$sql_pkgs="CREATE TABLE `" . $this->moovin_tbl_pkgs_sizes() . "` ( 
				`id_pkgs_size` INT NOT NULL AUTO_INCREMENT ,
				`name` VARCHAR(250) NOT NULL ,
				`length_cm` DOUBLE  ,
				`width_cm` DOUBLE  ,
				`high_cm` DOUBLE  , 
				`weight_kg` DOUBLE  ,
				`status` TINYINT(1) NOT NULL DEFAULT '1' ,
				PRIMARY KEY (`id_pkgs_size`)) ENGINE = InnoDB;";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
			dbDelta($sql_pkgs);		

			$sqlPkgsInit = "INSERT INTO `".$this->moovin_tbl_pkgs_sizes()."` 
			(`id_pkgs_size`, `name`, `length_cm`, `width_cm`, `high_cm`, `weight_kg`, `status`) VALUES 
			(NULL, 'XS', '16', '15', '4', '0.5', '1'), 
			(NULL, 'S', '24', '17', '9', '1', '1'), 
			(NULL, 'M', '32', '18', '22', '2', '1'),
			(NULL, 'L', '39', '19', '22', '7', '1'), 
			(NULL, 'XL', '97', '56', '26', '10', '1'), 
			(NULL, 'XXL', '120', '80', '27', '15', '1'), 
			(NULL, 'XXXL', '160', '100', '35', '20', '1');";

			$wpdb->query(
				$sqlPkgsInit
			);
		}

		$is_page_exists = $wpdb->get_row(
			$wpdb->prepare(
					"SELECT * from " . $wpdb->prefix . "posts WHERE post_name = %s", 'moovin-delivery-plg'
			)
		);

		if (!empty($is_page_exists)) {
			// we have already that page
		} else {
			$moovin_plugin_page = array(
				'post_title' => wp_strip_all_tags('Moovin Delivery Plugin'),
				'post_content' => '[plg_moovin]',
				'post_status' => 'publish',
				'post_author' => 1,
				'post_name' => 'moovin-delivery-plg',
				'post_type' => 'page',
			);
			wp_insert_post($moovin_plugin_page);
		}

	}

	public function moovin_tbl_parameters() {
        global $wpdb;
        return $wpdb->prefix . "plgn_moovin_parameters";
    }

	public function moovin_tbl_cat_documents() {
        global $wpdb;
        return $wpdb->prefix . "plgn_moovin_cat_documents";
    }

	public function moovin_tbl_orders() {
        global $wpdb;
        return $wpdb->prefix . "plgn_moovin_orders";
    }

	public function moovin_tbl_order_products() {
        global $wpdb;
        return $wpdb->prefix . "plgn_moovin_order_products";
    }

	public function moovin_tbl_notification_order() {
        global $wpdb;
        return $wpdb->prefix . "plgn_moovin_notification_order";
    }

	public function moovin_tbl_pkgs_sizes() {
        global $wpdb;
        return $wpdb->prefix . "plgn_moovin_pkgs_sizes";
    }

}
