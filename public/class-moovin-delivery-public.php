<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.moovin.me/
 * @since      1.0.1
 *
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/public
 * @author     Javier Hernández M <javier.hernandez@moovin.me>
 */
class Moovin_Delivery_Public  {

    private $table_activator;

	private $status_plugin;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		global $wpdb;

		include_once(MOOVIN_PLUGIN_PATH . '/includes/class-moovin-delivery-activator.php');
        $this->table_activator = new Moovin_Delivery_Activator();

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->status_plugin = $wpdb->get_results(
			$wpdb->prepare(
					"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_STATUS"
			), ARRAY_A
		);		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.1
	 */
	public function enqueue_styles() {
		global $wpdb;

		if ($this->status_plugin[0]["value"] == "1" ) {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/moovin-delivery-location-map-picker-public.css', array(), $this->version, 'all');
			wp_enqueue_style( "moovin-sweetalert", plugin_dir_url( __FILE__ ) . 'css/sweetalert2.min.css', array(), $this->version, 'all' );	

			$here_maps = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_HERE_MAP"
				), ARRAY_A
			);

			if($here_maps[0]["status"] == "1"){
				wp_enqueue_style( "moovin-hereui-css", 'https://js.api.here.com/v3/3.1/mapsjs-ui.css', array(), $this->version, 'all' );	
			}
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.1
	 */
	public function enqueue_scripts() {
		if ($this->status_plugin[0]["value"] == "1") {

			if ( ! wp_next_scheduled( 'isa_add_every_three_minutes' ) ) {
				wp_schedule_event( time(), 'every_three_minutes', 'isa_add_every_three_minutes' );
			}

			global $wpdb;

			$google_maps= $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_GOOGLE_MAP"
				), ARRAY_A
			);
			
			$here_maps = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_HERE_MAP"
				), ARRAY_A
			);

			wp_enqueue_script( "moovin-global", plugin_dir_url( __FILE__ ) . 'js/global.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "moovin-blockUI", plugin_dir_url( __FILE__ ) . 'js/jquery.blockUI.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "moovin-sweetalert2", plugin_dir_url( __FILE__ ) . 'js/sweetalert2.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( "moovin-utility", plugin_dir_url( __FILE__ ) . 'js/utility.js', array( 'jquery' ), $this->version, false );
			
			if($google_maps[0]["status"] == "1"){

				wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/moovin-delivery-location-googlemap-picker-public.js', array('jquery'), $this->version, false);
				wp_enqueue_script('moovin-googlemap-front', "https://maps.googleapis.com/maps/api/js?key=".$google_maps[0]["value"]."&libraries=places",array('jquery'), $this->version, false);
		
			}

			if($here_maps[0]["status"] == "1"){
				wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/moovin-delivery-location-heremap-picker-public.js', array('jquery'), $this->version, false);
		
				wp_enqueue_script('moovin-heremap-core', "https://js.api.here.com/v3/3.1/mapsjs-core.js",array('jquery'), $this->version, false);
				wp_enqueue_script('moovin-heremap-service', "https://js.api.here.com/v3/3.1/mapsjs-service.js",array('jquery'), $this->version, false);
				wp_enqueue_script('moovin-heremap-ui', "https://js.api.here.com/v3/3.1/mapsjs-ui.js",array('jquery'), $this->version, false);
				wp_enqueue_script('moovin-heremap-mapevents', "https://js.api.here.com/v3/3.1/mapsjs-mapevents.js",array('jquery'), $this->version, false);

				wp_localize_script($this->plugin_name, "moovinhere", array(
					"herekey" => $here_maps[0]["value"],
				));
			}
		}
	}

	function moovin_woocommerce_after_checkout_billing_form(){
		if ($this->status_plugin[0]["value"] == "1") {
			$section = "billing";
			include(MOOVIN_PLUGIN_PATH."/public/partials/moovin-delivery-location-map-picker-public-display.php");
		}
	}

	function moovin_woocommerce_after_checkout_shipping_form(){
		if ($this->status_plugin[0]["value"] == "1") {
			$section = "shipping";
			include(MOOVIN_PLUGIN_PATH."/public/partials/moovin-delivery-location-map-picker-public-display.php");
		}
	}

	function moovin_manage_billing_address_fields($fields){
		if ($this->status_plugin[0]["value"] == "1") {
			$address_type_section = "billing";
			include_once(MOOVIN_PLUGIN_PATH.'/public/partials/moovin-delivery-location-customised-fields.php');
		}
		return $fields;
	}

	function moovin_manage_shipping_address_fields($fields){
		if ($this->status_plugin[0]["value"] == "1") {
			$address_type_section = "shipping";
			include_once(MOOVIN_PLUGIN_PATH.'/public/partials/moovin-delivery-location-customised-fields.php');
		}
		return $fields;
	}

	function plugin_update() {
		global $plugin_version;
		$this->plugin_updates();
	}

	function plugin_updates() {
		global $wpdb, $plugin_version;
	
		$xs = $wpdb->update(
			$this->table_activator->moovin_tbl_pkgs_sizes(), 
			array("length_cm" => 16,
			 	  "width_cm" => 15 ,
			  	  "high_cm"=> 4 ,
			   	  "weight_kg" => 0.5),
			array("name" => "XS")
		);

		$s = $wpdb->update(
			$this->table_activator->moovin_tbl_pkgs_sizes(), 
			array("length_cm" => 24,
			 	  "width_cm" => 17 ,
			  	  "high_cm"=> 8 ,
			   	  "weight_kg" => 1),
			array("name" => "S")
		);

		$m = $wpdb->update(
			$this->table_activator->moovin_tbl_pkgs_sizes(), 
			array("length_cm" => 32,
			 	  "width_cm" => 22 ,
			  	  "high_cm"=> 18 ,
			   	  "weight_kg" => 2),
			array("name" => "M")
		);

		$l = $wpdb->update(
			$this->table_activator->moovin_tbl_pkgs_sizes(), 
			array("length_cm" => 39,
			 	  "width_cm" => 22 ,
			  	  "high_cm"=> 19 ,
			   	  "weight_kg" => 7),
			array("name" => "L")
		);

		$xl = $wpdb->update(
			$this->table_activator->moovin_tbl_pkgs_sizes(), 
			array("length_cm" => 97,
			 	  "width_cm" => 56 ,
			  	  "high_cm"=> 26 ,
			   	  "weight_kg" => 10),
			array("name" => "XL")
		);

		$xxl = $wpdb->update(
			$this->table_activator->moovin_tbl_pkgs_sizes(), 
			array("length_cm" => 120,
			 	  "width_cm" => 80 ,
			  	  "high_cm"=> 27 ,
			   	  "weight_kg" => 15),
			array("name" => "XXL")
		);

		$xxxl = $wpdb->update(
			$this->table_activator->moovin_tbl_pkgs_sizes(), 
			array("length_cm" => 160,
			 	  "width_cm" => 100 ,
			  	  "high_cm"=> 35 ,
			   	  "weight_kg" => 20),
			array("name" => "XXXL")
		);

		$prod = $wpdb->update(
			$this->table_activator->moovin_tbl_parameters(), 
			array("value" => "https://moovin.me/moovinApiWebServices-cr",
			 	  "value1" => "https://hn.moovin.me/moovinApiWebServices-hn" ),
			array("cod_parameter" => "MOOVIN_URL_PROD")
		);

		$dev = $wpdb->update(
			$this->table_activator->moovin_tbl_parameters(), 
			array("value" => "https://developer.moovin.me/moovinApiWebServices-cr",
			 	  "value1" => "https://hn.developer.moovin.me/moovinApiWebServices-hn"),
			array("cod_parameter" => "MOOVIN_URL_SANDBOX")
		);
	}

	function moovin_get_refresh_token(){
		global $wpdb;

		$parameters =  $wpdb->get_results(
			"SELECT * from " . $this->table_activator->moovin_tbl_parameters() , ARRAY_A);

			$url_sandbox = "";
			$url_prod = "";
			$url_sandbox_cr = "";
			$url_sandbox_hn = "";
			$url_prod_cr = "";
			$url_prod_hn = "";
			$current_country = "CR";

			$token_sandbox = "";
			$token_sandbox_date = "";
			$token_prod = "";
			$token_prod_date = "";
			$username_prod = "";
			$password_prod = "";
			$username_sandbox = "";
			$password_sandbox = "";
			$current_env = "";
			$tc = "";

			foreach($parameters as $row){
				switch($row["cod_parameter"]){
					case "MOOVIN_STATUS":
						if($row["value1"] != ""){
							$current_country = $row["value1"];
						}
					break;
					case "MOOVIN_TOKEN_SANDBOX":
						if($row["value"] != ""){
							$token_sandbox =  $row["value"];
							$token_sandbox_date = $row["value1"];
							$tc = $row["value2"];
						}
					break;
					case "MOOVIN_TOKEN_PROD":
						if($row["value"] != ""){
							$token_prod =  $row["value"];
							$token_prod_date = $row["value1"];
							$tc = $row["value2"];
						}
					break;
					case "MOOVIN_USERNAME_PROD":
						if($row["value"] != ""){
							$username_prod =  $row["value"]  ;
						}
					break;
					case "MOOVIN_PASSWORD_PROD":
						if($row["value"] != ""){
							$password_prod =  $row["value"];
						}
					break;
					case "MOOVIN_USERNAME_SANDBOX":
						if($row["value"] != ""){
							$username_sandbox = $row["value"];
						}
					break;
					case "MOOVIN_PASSWORD_SANDBOX": 
						if($row["value"] != "" ){
							$password_sandbox =  $row["value"];
						}
					break;
					case "MOOVIN_CURRENT_ENV":
						$current_env = $row["value"];
					break;
					case "MOOVIN_URL_SANDBOX":
						$url_sandbox_cr = $row["value"];
						$url_sandbox_hn = $row["value1"];

					break;
					case "MOOVIN_URL_PROD":
						$url_prod_cr = $row["value"];
						$url_prod_hn = $row["value1"];
					break;
				}
			}

			//Detect country selected
			switch($current_country){
				case "HN":
					$url_sandbox = $url_sandbox_hn;
					$url_prod = $url_prod_hn;
					break;
				default:
					$url_sandbox = $url_sandbox_cr;
					$url_prod = $url_prod_cr;
					break;
			}

			//Get token current country
			switch($current_env){
				case "SANDBOX":
					$currentDate = strtotime(date("Y-m-d H:i:s"));
					$tokenDate = strtotime($token_sandbox_date);

					if ($tokenDate > $currentDate ){
						return array("token"=> $token_sandbox , "url"=> $url_sandbox ,"tc"=> json_decode( $tc , true), "error" => false);
					}else{
						$post_url = $url_sandbox."/rest/api/moovinEnterprise/partners/login";
						$response = wp_remote_post($post_url, array(
							'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
							'body'        => json_encode(array('username' => $username_sandbox , 'password' => $password_sandbox)),
							'method'      => 'POST',
							'data_format' => 'body',
						));
					
						$body = json_decode($response["body"]);

						if($body->status == "SUCCESS"){
							$tc = $this->moovin_get_exchange_values();
							$tokenUpdate =	$wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => $body->token, "value1" => $body->expirationDate , "value2"=> $tc , "edited_at" => date_i18n("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_TOKEN_SANDBOX")
							);

							return array("token"=> $body->token , "url"=> $url_sandbox , "tc"=> json_decode( $tc , true) , "error" => false);
						}else{
							return array("token"=> $body->token , "url"=> $url_sandbox , "error" => true);
						}
					}
					break;

				case "PROD":

					$currentDate = strtotime(date("Y-m-d H:i:s"));
					$tokenDate = strtotime($token_prod_date);

					if ($tokenDate > $currentDate ){
						return array("token"=>$token_prod, "url"=> $url_prod, "tc"=> json_decode( $tc , true), "error" => false);
					}else{
						$post_url = $url_prod."/rest/api/moovinEnterprise/partners/login";
						$response = wp_remote_post($post_url, array(
							'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
							'body'        => json_encode(array('username' => $username_prod , 'password' => $password_prod)),
							'method'      => 'POST',
							'data_format' => 'body',
						));
					
						$body = json_decode($response["body"]);

						if($body->status == "SUCCESS"){
							$tc = $this->moovin_get_exchange_values();
							$tokenUpdate =	$wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => $body->token, "value1" => $body->expirationDate , "value2"=> $tc, "edited_at" => date_i18n("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_TOKEN_PROD")
							);

							return array("token"=> $body->token , "url"=> $url_prod , "tc"=> json_decode($tc , true) , "error" => false);
						}else{
							return array("token"=> "", "url"=> $url_prod, "error" => true);
						}
					}

					break;
			}
	}

		function moovin_zones_coverage_get(){
			try{
				$response = $this->moovin_get_refresh_token();

				if($response["error"] == false){
					$get_url = $response["url"]."/rest/api/moovinEnterprise/partners/zoneCoverageV2";
			
					$responseZones = wp_remote_post($get_url, array(
						'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
						'method'      => 'GET',
						'data_format' => 'body',
					));
			
					if ( is_wp_error( $responseZones ) ) {
						echo wp_json_encode(array("error"=>true , "message" => $response->get_error_message()));
					} else {
						$zones = json_decode($responseZones["body"]);
						echo wp_json_encode(array("error"=>false , "message" => "Petición completada" , "points"=> $zones->zones));
					}
				}else{
					echo wp_json_encode(array("error"=>true , "message" => "Error obteniendo token moovin" ));
				}
			}catch(Exception $e ){
				echo wp_json_encode(array("error"=>true , "message" => $e->get_message() ));
			}
			wp_die();
		}

		function clear_wc_shipping_rates_cache(){
			$packages = WC()->cart->get_shipping_packages();
		
			foreach ($packages as $key => $value) {
				$shipping_session = "shipping_for_package_$key";
				unset(WC()->session->$shipping_session);
			}
		}

		function get_point_collect(){
				global $wpdb;
				$response = $this->moovin_get_refresh_token();

				if($response["error"] == false){

					if (WC()->session->get('moovin_address_selected') != ""){

						$package =  $wpdb->get_results(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter in  ('MOOVIN_PKG_WEIGHT' , 'MOOVIN_PKG_SIZE', 'MOOVIN_DEFAULT_LOCATION' , 'MOOVIN_CONTACT') " , ARRAY_A);		
				
						$weight = "";
						$size  = "";
						$latitudeCollect  = "";
						$longitudeCollect  = "";
						$address  = "";
						$contactCollect = null;

						foreach($package as $row){
							switch($row["cod_parameter"]){
								case "MOOVIN_PKG_WEIGHT":
									$weight =  $row["value"];
								break;
								case "MOOVIN_PKG_SIZE":
									$size =  $row["value"];
								break;
								case "MOOVIN_DEFAULT_LOCATION":
									$address =  $row["name"];

									if($row["value"] != ""){
										$latitudeCollect =  $row["value"];
									}
									if($row["value1"] != ""){
										$longitudeCollect =  $row["value1"];
									}
								break;
								case 'MOOVIN_CONTACT':
									$contactCollect = $row;
									break;
							}
						}

					}

					return array("latitudeCollect" => $latitudeCollect,
								"longitudeCollect" => $longitudeCollect  ,
								"address" => $address ,
								"weight" => $weight ,
								"size"=>$size ,
								"nameContactCollect" => $contactCollect["name"],
								"phoneContactCollect" => $contactCollect["value"],
								"notesContactCollect" => $contactCollect["value1"] ,
								"emailContactCollect" => $contactCollect["value2"] );
			}
		}

		function moovin_get_exchange_values(){
			global $wpdb;
			$tc = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_TC"
				), ARRAY_A
			);
	
			$tcauto = "0";
			$tcvalue = "0";
			if(count($tc) > 0){
				$tcvalue = $tc[0]["value"];
				$tcauto = $tc[0]["value1"];
			}
	
			if($tcauto =="1"){
				//Attemp #1 - Get from softcr
				$tc =	$this->moovin_get_tc_softcr();
				if($tc != false){
					return $tc;
				}else{
					//Attemp #2 - Get from API Hacienda
					$tc =	$this->moovin_get_tc_dgtd();
					if($tc != false){
						return $tc;
					}else{
						//Attemp #3 - Get from BNCR
						$tc =	$this->moovin_get_tc_bncr();
						if($tc != false){
							return $tc;
						}else{
							return json_encode(array("euro"=>array( "fecha"=> date("Y-m-d"), "dolares"=> 1, "colones"=>1 ) , "dolar"=> array("venta"=>array("valor"=> 1, "fecha"=>date("Y-m-d"),"error"=>""), "compra"=>array("valor"=> 1, "fecha"=>date("Y-m-d"),"error"=>""))));
						}
					}
				}
				return json_encode(array("euro"=>array( "fecha"=> date("Y-m-d"), "dolares"=> 1, "colones"=>1 ) , "dolar"=> array("venta"=>array("valor"=> 1, "fecha"=>date("Y-m-d"),"error"=>""), "compra"=>array("valor"=> 1, "fecha"=>date("Y-m-d"),"error"=>""))));
			}else{
				return json_encode(array("euro"=>array( "fecha"=> date("Y-m-d"), "dolares"=> 0, "colones"=>0 ) , "dolar"=> array("venta"=>array("valor"=> $tcvalue, "fecha"=>date("Y-m-d"),"error"=>""), "compra"=>array("valor"=> $tcvalue, "fecha"=>date("Y-m-d"),"error"=>""))));
			}
		}
	
		function moovin_get_tc_dgtd(){
			$tc_url = "https://api.hacienda.go.cr/indicadores/tc";
			$responseTC = wp_remote_post($tc_url, array(
				'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
				'method'      => 'GET',
				'data_format' => 'body'
			));
	
			if (is_wp_error( $responseTC ) ) {
				return false;
			}else{
				return $responseTC["body"];			
			}
		}
	
		function moovin_get_tc_bncr(){
			$TCventa = 0 ;
			$TCventaIntentos = 0 ;
			$TCventaContinue = true;
	
			while($TCventaContinue) {
				$tc_url = "https://gee.bccr.fi.cr/Indicadores/Suscripciones/WS/wsindicadoreseconomicos.asmx/ObtenerIndicadoresEconomicos?Indicador=318&FechaInicio=".date("d/m/Y")."&FechaFinal=".date("d/m/Y")."&Nombre=Moovin&SubNiveles=N&CorreoElectronico=developer@indigogc.com&Token=NV2OON2IEO";
				$responseTCVenta = wp_remote_get($tc_url, array(
					'headers'     => array(),
					'method'      => 'GET',
					'data_format' => 'body'
				));
				$errorMessageTCVenta = "";
				if ( is_wp_error( $responseTCVenta ) ) {
					$errorMessageTCVenta = $responseTCVenta->get_error_message();
				} else {
					$xml = $responseTCVenta["body"];
					if (strpos($xml, 'Your support ID is') !== false) {
						$TCventaIntentos ++;
						if ($TCventaIntentos >5){
							$TCventaContinue = false;
						}
					}else{
						$tipo_cambio = trim(strip_tags(substr($xml, strpos($xml, "<NUM_VALOR>"), strripos($xml, "</NUM_VALOR>"))));
						$TCventa = number_format($tipo_cambio, 2);
						$TCventaContinue = false;
					}
				}
			}
	
			$TCcompra = 0 ;
			$TCcompraIntentos = 0 ;
			$TCcompraContinue = true;
	
			//Compra
			while($TCcompraContinue) {
				$tc_url = "https://gee.bccr.fi.cr/Indicadores/Suscripciones/WS/wsindicadoreseconomicos.asmx/ObtenerIndicadoresEconomicos?Indicador=317&FechaInicio=".date("d/m/Y")."&FechaFinal=".date("d/m/Y")."&Nombre=Moovin&SubNiveles=N&CorreoElectronico=developer@indigogc.com&Token=NV2OON2IEO";
				$responseTCcompra = wp_remote_get($tc_url, array(
					'headers'     => array(),
					'method'      => 'GET',
					'data_format' => 'body',
				));
				$errorMessageTCcompra = "";
				if ( is_wp_error( $responseTCcompra ) ) {
					$errorMessageTCcompra = $responseTCcompra->get_error_message();
					
				} else {
					$xml = $responseTCcompra["body"];
					if (strpos($xml, 'Your support ID is') !== false) {
						$TCcompraIntentos ++;
						if ($TCcompraIntentos > 5){
							$TCcompraContinue = false;
						}
					}else{
						$tipo_cambio = trim(strip_tags(substr($xml, strpos($xml, "<NUM_VALOR>"), strripos($xml, "</NUM_VALOR>"))));
						$TCcompra = number_format($tipo_cambio, 2);
						$TCcompraContinue = false;
					}	
				}
			}
	
			if (strpos($a, 'are') !== false) {
				echo 'true';
			}
	
			if($errorMessageTCcompra == "" && $errorMessageTCVenta == "" &&  strpos($responseTCcompra["body"], "Rejected") !== true){
				return json_encode(array("euro"=>array( "fecha"=> date("Y-m-d"), "dolares"=> 0, "colones"=>0 ) , "dolar"=> array("venta"=>array("valor"=> $TCventa, "fecha"=>date("Y-m-d"),"error"=>$errorMessageTCVenta), "compra"=>array("valor"=> $TCcompra, "fecha"=>date("Y-m-d"),"error"=>$errorMessageTCcompra))));
			}else{
				return false;
			}
		}
	
		function moovin_get_tc_softcr(){
			$tc_url = "https://soft.cr/ce/api-ce/comprobante/get-tc";
			$responseTC = wp_remote_get($tc_url, array(
				'headers'     => array(),
				'method'      => 'GET',
				'data_format' => 'body',
			));
			$errorMessageTCcompra = "";
			if ( is_wp_error( $responseTC ) ) {
				return false;			
			} else {
				$tc = json_decode($responseTC["body"],true);
				return json_encode(array("euro"=>array( "fecha"=> $tc["date"], "dolares"=> $tc["usd"], "colones"=>0 ) , "dolar"=> array("venta"=>array("valor"=> $tc["usd"], "fecha"=>date("Y-m-d"),"error"=>""), "compra"=>array("valor"=> $tc["usd"], "fecha"=>date("Y-m-d"),"error"=>""))));
			}
		}

		function change_rates($rates, $packages) {
			global $woocommerce;
			global $wpdb;
			
			$address = WC()->session->get('moovin_address_selected');
			
			if ($address != ""){

					$response = $this->moovin_get_refresh_token();

					if($response["error"] == false ){
						$address = json_decode(WC()->session->get('moovin_address_selected'), TRUE);

						if (WC()->session->get('moovin_address_selected') != ""){

							$collect = $this->get_point_collect();
				
							$pointCollect = array(
								"latitude" => $collect["latitudeCollect"],
								"longitude" => $collect["longitudeCollect"]
							);
				
							$pointDelivery = array(
								"latitude"=>$address["position"]["lat"],
								"longitude"=>$address["position"]["lng"]
							);
				
							$listProduct =  array();
				
							$items = $woocommerce->cart->get_cart();
				
							$sizePackage =  $wpdb->get_results(
								"SELECT * from " . $this->table_activator->moovin_tbl_pkgs_sizes() ." WHERE name = '".$collect["size"]."'"  , ARRAY_A);		
					

							foreach($items as $item => $values) { 
								// Get the instance of the WC_Product Object
								$product = wc_get_product( $values['product_id'] );
				
								// Get the accessible array of product properties:
								$data = $product->get_data();
				
								$description = strlen($data["short_description"]) > 250 ? substr($data["short_description"], 0, 250)  : $data["short_description"];
								$product = array(		
									"quantity" => $values['quantity'],
									"nameProduct" => $data["name"],
									"description" => $description,
									"length" => $data["length"] == "" || $data["length"] == null ? $sizePackage[0]["length_cm"] : $this->moovin_calculate_dimmension($data["length"]),
									"width" => $data["width"] == "" || $data["width"] == null ? $sizePackage[0]["width_cm"] : $this->moovin_calculate_dimmension($data["width"]),
									"high" => $data["height"] == "" || $data["height"] == null ? $sizePackage[0]["high_cm"] : $this->moovin_calculate_dimmension($data["height"]) ,
									"weight" => $data["weight"] == "" || $data["weight"] == null ?  $collect["weight"]  : $this->moovin_calculate_weight($data["weight"]) ,
									"price" =>  $data["price"],
									"codeProduct" => $data["sku"]);					
				
								array_push($listProduct , $product);
							} 
				
							$fulfillment = $wpdb->get_results(
								$wpdb->prepare(
										"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_FULFILLMENT"
								), ARRAY_A
							);

							if($fulfillment[0]["value"] ==" 0"){
								//Request create order - regular
								$body = array(
									"ensure"=>true,
									"pointDelivery" => $pointDelivery,
									"pointCollect" => $pointCollect,
									"listProduct" => $listProduct
								);
							}else{
									//Request create order - fulfillment
								$body = array(
									"ensure"=>true,
									"pointDelivery" => $pointDelivery,
									"cediMoovin" => true,
									"listProduct" => $listProduct
								);	
							}
												
							$post_url = $response["url"]."/rest/api/ecommerceExternal/estimation";
							$responseEstimation = wp_remote_post($post_url, array(
								'headers'     => array('Content-Type' => 'application/json; charset=utf-8' , 'token' =>  $response["token"]),
								'method'      => 'POST',
								'data_format' => 'body',
								'body' => json_encode($body)
							));
				
							
							$responseEstimation = $responseEstimation["body"];
							
							WC()->session->set('moovin_estimation_get', "" );
							WC()->session->set('moovin_estimation_get', $responseEstimation);

						}
					

					if(isset($responseEstimation)){

						$estimation = json_decode($responseEstimation, TRUE);

						if($estimation["status"] =="SUCCESS"){

							$tc = $wpdb->get_results(
								$wpdb->prepare(
										"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_TC"
								), ARRAY_A
							);

							$tcauto = "0";
							$tcvalue = 700;
							if(count($tc) > 0){
								$tcvalue = $tc[0]["value"] == "0"? 700 : $tc[0]["value"];
								$tcauto = $tc[0]["value1"];
							}

							if($tcauto =="1"){
								$tcvalue = isset($response["tc"]["dolar"]["compra"]["valor"]) ? $response["tc"]["dolar"]["compra"]["valor"] : $tcvalue;
							}

							$amountRoute = 0;
							$amountExpress = 0;

							switch(get_woocommerce_currency()){
								case "USD":
									//Woocommerce in USD Currency
									foreach($estimation["optionService"] as $row){
										/*
										 * Check Service Type Moovin
										 */
										switch($row["type"]){
											case "Ondemand":
												/*
												*  Service Express
												*/
												if(isset($row["currency"])){
													if($row["currency"]["currency"] == "dollars"){
														$amountExpress = $row["amount"] ;
													}else{
														//Calculate COLON TO DOLAR 
														$amountExpress = $row["amount"] / $tcvalue ;
													}
												}else{
													$amountExpress = $row["amount"] ;
												}
											break;
											case "route":
												/*
												*  Service Route
												*/
												if(isset($row["currency"])){
													if($row["currency"]["currency"] == "dollars"){
														$amountRoute = $row["amount"] ;
													}else{
														//Calculate COLON TO DOLAR 
														$amountRoute = $row["amount"] / $tcvalue ;
													}
												}else{
													$amountRoute = $row["amount"] ;
												}
												break;
											default:
												/*
												*  Nothing to do
												*/
											break;
										}
									}
		
								break;
								case "HNL":
									//Woocommerce in HNL colon Currency
									foreach($estimation["optionService"] as $row){
										/*
										 * Check Service Type Moovin
										 */
										switch($row["type"]){
											case "Ondemand":
												/*
												*  Service Express
												*/
												if(isset($row["currency"])){
													if($row["currency"]["currency"] == "dollars"){
														//Calculate DOLAR TO LEMPIRA 
														$amountExpress = $row["amount"] * 25 ;
													}else{
														$amountExpress = $row["amount"] ;	
													}
												}else{
													$amountExpress = $row["amount"] ;
												}
											break;
											case "route":
												/*
												*  Service Route
												*/
												if(isset($row["currency"])){
													if($row["currency"]["currency"] == "dollars"){
														//Calculate COLON TO LEMPIRA 
														$amountRoute = $row["amount"] * 25 ;
													}else{
														$amountRoute = $row["amount"] ;
													}
												}else{
													$amountRoute = $row["amount"] ;
												}
												break;
											default:
												/*
												*  Nothing to do
												*/
											break;
										}
									}

									
								break;
								case "CRC":
									//Woocommerce in CRC colon Currency
									foreach($estimation["optionService"] as $row){
										/*
										 * Check Service Type Moovin
										 */
										switch($row["type"]){
											case "Ondemand":
												/*
												*  Service Express
												*/
												if(isset($row["currency"])){
													if($row["currency"]["currency"] == "dollars"){
														//Calculate DOLAR TO COLON 
														$amountExpress = $row["amount"] * $tcvalue ;

													}else{
														$amountExpress = $row["amount"] ;
													}
												}else{
													$amountExpress = $row["amount"] ;
												}
											break;
											case "route":
												/*
												*  Service Route
												*/
												if(isset($row["currency"])){
													if($row["currency"]["currency"] == "dollars"){
														//Calculate COLON TO DOLAR 
														$amountRoute = $row["amount"] * $tcvalue ;
													}else{
														$amountRoute = $row["amount"] ;
													}
												}else{
													$amountRoute = $row["amount"] ;
												}
												break;
											default:
												/*
												*  Nothing to do
												*/
											break;
										}
									}
								break;
								default:
									// TODO CASE EUR  $response["tc"]["euro"]
									wc_add_wp_error_notices(new WP_Error(1,'Error la moneda de la tienda no es compatible con el plugin moovin['.get_woocommerce_currency().']'));
								break;
							}


							$round = "0";
							$amountRoundInfo = $wpdb->get_results(
								$wpdb->prepare(
										"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_AMOUNT_ROUND"
								), ARRAY_A
							);

							if(isset($amountRoundInfo[0]["value"])){
								$round = $amountRoundInfo[0]["value"];
							}

							$addAmount = 0;
							$amountAddInfo = $wpdb->get_results(
								$wpdb->prepare(
										"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_AMOUNT_ADD"
								), ARRAY_A
							);

							if(isset($amountAddInfo[0]["value"])){
								$addAmount = $amountAddInfo[0]["value"] != "" ? $amountAddInfo[0]["value"] : 0;
							}

							// Assign amount to shipping
							foreach($rates as $rate_key => $rate_values ) {
								if( $rates[$rate_key]->method_id == "moovin_shipping_express" ){
									if($this->check_coupon_gives_free_shipping()){
										$rates[$rate_key]->cost = 0;
									}elseif($this->get_promo_free_delivery()){
										$rates[$rate_key]->cost = 0;
									}else{
										if($round == "1"){
											$rates[$rate_key]->cost = $this->roundDecimalMoovin($amountExpress + $addAmount);
										}else{
											$rates[$rate_key]->cost = $amountExpress + $addAmount;
										}
									}
								}else if($rates[$rate_key]->method_id == "moovin_shipping"){
									if($this->check_coupon_gives_free_shipping()){
										$rates[$rate_key]->cost = 0;
									}elseif($this->get_promo_free_delivery_ruta()){
										$rates[$rate_key]->cost = 0;
									}else{
										if($round == "1"){
											$rates[$rate_key]->cost = $this->roundDecimalMoovin($amountRoute + $addAmount);
										}else{
											$rates[$rate_key]->cost = $amountRoute + $addAmount;
										}
									}
								}
							}
						}else{
							wc_add_wp_error_notices(new WP_Error(1,'Error calculando estimación de entrega ['.$estimation["status"].']'));
						}
					}
				}
			}
					
			return $rates;
		}

		function roundDecimalMoovin($number) {
			$inumber = ceil($number);
			$mod_10 = $inumber % 10;
			$mod_5 = $inumber % 5;
			
			if($mod_10 == 0){
				return $inumber;
			}
			
			if($mod_5 == 0){
				return $inumber + 5;
			}
		
			if ($mod_10 < 5) {
				return $inumber + 5 - $mod_5 + 5;
			}
		
			if ($mod_10 > 5) {
				return $inumber + 10 - $mod_10 ;
			}
		
			return $inumber;
		}

		function get_promo_free_delivery(){
			/**
			 * 	Check if the user has a amount that gives free delivery
			 */
			global $wpdb;

			$data =  $wpdb->get_results(
				"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter IN ('MOOVIN_PROMO') " , ARRAY_A);
				
			if($data[0]["value"] == "1"){
				$amount = WC()->cart->get_displayed_subtotal();
				if((float)str_replace(",", "", $amount) >= (float)$data[0]["value1"]){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}


		function get_promo_free_delivery_ruta(){
			/**
			 * 	Check if the user has a amount that gives free delivery
			 */
			global $wpdb;

			$data =  $wpdb->get_results(
				"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter IN ('MOOVIN_PROMO_RUTA') " , ARRAY_A);
				
			if($data[0]["value"] == "1"){
				$amount = WC()->cart->get_displayed_subtotal();
				if((float)str_replace(",", "", $amount) >= (float)$data[0]["value1"]){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}

		function get_order_free_shipping_descriptions($order_id){
			/**
			 * Coupons support
			 * do we have a coupon in order or freeshipping amount
			 */
			$order = wc_get_order( $order_id );

			foreach( $order->get_used_coupons() as $coupon_code ){
				$this_coupon = new WC_Coupon( $coupon_code );
				if ( $this_coupon->get_free_shipping() ) {
					$shipping_rate = 0;
					return "Coupon: ". $coupon_code;
				}
			}

			/**
			 * 	Check if the user has a amount that gives free delivery
			 */
			global $wpdb;

			$data =  $wpdb->get_results(
				"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter IN ('MOOVIN_PROMO') " , ARRAY_A);
				
			if($data[0]["value"] == "1"){
				$amount = $order->get_subtotal();
				if((float)str_replace(",", "", $amount) >= (float)$data[0]["value1"]){
					return "Promo envío gratis por monto de compra";
				}else{
					return "";
				}
			}else{
				return "";
			}
		
			return "";
		}

	
		function check_coupon_gives_free_shipping(){
			/**
			 * Coupons support
			 * do we have a coupon that gives free shipping?
			 */
			$all_applied_coupons = WC()->cart->get_applied_coupons();
			if ( $all_applied_coupons ) {
				foreach ( $all_applied_coupons as $coupon_code ) {
					$this_coupon = new WC_Coupon( $coupon_code );
					if ( $this_coupon->get_free_shipping() ) {
						$shipping_rate = 0;
						return true;
					}
				}
			}
			return false;
		}

		function every_three_minutes_event_func() {
			global $wpdb;
	
			$notification = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EMAIL_NOTIFICATION"
				), ARRAY_A
			);
	
			if (count($notification)>0){
				if ($notification[0]["value"] == "1"){
	
					$pending = $wpdb->get_results(
						$wpdb->prepare(
								"SELECT  ".
								" `" . $this->table_activator->moovin_tbl_orders()."`.order_id ,".
								" `" . $this->table_activator->moovin_tbl_orders()."`.contact_delivery ,".
								" `" . $this->table_activator->moovin_tbl_orders()."`.phone_delivery ,".
								" `" . $this->table_activator->moovin_tbl_orders()."`.id_package_moovin ,".
								" `" . $this->table_activator->moovin_tbl_notification_order()."`.email ,".
								" `" . $this->table_activator->moovin_tbl_notification_order()."`.status ,".
								" `" . $this->table_activator->moovin_tbl_notification_order()."`.id_notification ".
								" FROM " .
								"".$this->table_activator->moovin_tbl_notification_order()  .
								" INNER JOIN ".$this->table_activator->moovin_tbl_orders() . 
								" ON `" . $this->table_activator->moovin_tbl_notification_order()."`.order_id  =  `".$this->table_activator->moovin_tbl_orders()."`.order_id".
								" WHERE `".$this->table_activator->moovin_tbl_notification_order()."`.status = 0 ;"
						), ARRAY_A
					);
	
					//star foreach
					foreach($pending as $row){
	
							require_once(str_replace("/wp-content/plugins/moovin-delivery/" , "", MOOVIN_PLUGIN_PATH). '/wp-load.php');
	
							$to = $row["email"];
							$subject = 'Acabamos de recibir un paquete para vos ;)';
	
							$body = '<meta charset="utf-8">
									<meta http-equiv="x-ua-compatible" content="ie=edge">
									<title>Acabamos de recibir un paquete para vos ;)</title>
									<meta name="viewport" content="width=device-width, initial-scale=1">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;border-collapse: collapse !important;">
									<tbody>
										<tr>
										<td align="center" bgcolor="#e9ecef" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;background-color: #ffff;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;border-collapse: collapse !important;">
											<tbody>
												<tr>
												<td align="center" valign="top" style="padding: 36px 24px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
													<img src="https://gallery.mailchimp.com/fc668b62059e3c7cb8447d790/images/362d7bb4-b971-439b-ab3f-d1fc8777116e.png" alt="Logo" border="0" style="display: block;max-width: 300px;min-width: 48px;-ms-interpolation-mode: bicubic;height: auto;line-height: 100%;text-decoration: none;border: 0;outline: none;">
												</td>
												</tr>
												<tr>
												<td align="center" valign="top" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
													<img src="https://mcusercontent.com/fc668b62059e3c7cb8447d790/images/2ce60106-6387-8beb-aa55-4e937629b85d.gif" alt="Logo" border="0" style="max-width: 590px;-ms-interpolation-mode: bicubic;height: auto;line-height: 100%;text-decoration: none;border: 0;outline: none;">
												</td>
												</tr>
											</tbody>
											</table>
										</td>
										</tr>
										<tr>
										<td align="center" bgcolor="#e9ecef" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;border-collapse: collapse !important;">
											<tbody>
												<tr>
												<td align="left" bgcolor="#ffffff" style="padding: 36px 24px 0;font-family: Source Sans Pro, Helvetica, Arial, sans-serif;border-top: 3px solid #d4dadf;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
													<h1 style="margin: 0; font-size: 32px; font-weight: 300; letter-spacing: -1px; line-height: 48px;">¡Hola '.$row["contact_delivery"].'!</h1>
												</td>
												</tr>
											</tbody>
											</table>
										</td>
										</tr>
										<tr>
										<td align="center" bgcolor="#e9ecef" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;border-collapse: collapse !important;">
											<tbody>
												<tr>
												<td align="left" bgcolor="#ffffff" style="padding: 24px;font-family: Source Sans Pro, Helvetica, Arial, sans-serif;font-size: 16px;line-height: 24px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
													<p style="margin: 0;"> 
														Acabamos de recibir un paquete para vos y tu número de paquete es <b>'.$row["id_package_moovin"].'</b> ;) ya nos hemos puesto en marcha para hacértelo llegar lo antes posible. Podés ver el estado de tu entrega en tiempo real haciendo click en el siguiente botón:</p>
												</td>
												</tr>
												<tr>
												<td align="left" bgcolor="#ffffff" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
													<table border="0" cellpadding="0" cellspacing="0" width="100%" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;border-collapse: collapse !important;">
													<tbody>
														<tr>
														<td align="center" bgcolor="#ffffff" style="padding: 12px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
															<table border="0" cellpadding="0" cellspacing="0" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;border-collapse: collapse !important;">
															<tbody>
																<tr>
																<td align="center" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
																	
																	<font color="#ffffff" face="Arial">
																	<a href="https://moovin.me/MoovinWebCliente/src/packageTracking/?idPackage='.$row["id_package_moovin"].'" class="myButton" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #ffffff;background: linear-gradient(to bottom, #ab2f2b 5%, #ab2f2b 100%);background-color: #ab2f2b;border-radius: 28px;border: 1px solid #ab2f2b;display: inline-block;cursor: pointer;font-family: Arial;font-size: 16px;padding: 19px 31px;text-decoration: none;text-shadow: 0px 1px 0px #2f6627;">VER ESTADO DE MI PAQUETE</a>
																	</font>
																	
																	<br>
																</td>
																</tr>
															</tbody>
															</table>
														</td>
														</tr>
													</tbody>
													</table>
												</td>
												</tr>
												<tr>
												<td align="left" bgcolor="#ffffff" style="padding: 24px;font-family: Source Sans Pro, Helvetica, Arial, sans-serif;font-size: 16px;line-height: 24px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
													<p style="margin: 0;">Si tenés cualquier consulta sobre la entrega de este pedido, no dudés en ponerte en contacto con nosotros llamándonos al 2289-0377 o escribiéndonos por WhatsApp al +506 8579 1341. Estaremos encantados de atenderte.</p>
												</td>
												</tr>
												<tr>
												<td align="left" bgcolor="#ffffff" style="padding: 24px;font-family: Source Sans Pro, Helvetica, Arial, sans-serif;font-size: 16px;line-height: 24px;border-bottom: 3px solid #d4dadf;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
												</td>
												</tr>
											</tbody>
											</table>
										</td>
										</tr>
										<tr>
										<td align="center" bgcolor="#e9ecef" style="padding: 24px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;border-collapse: collapse !important;">
											<tbody>
												<tr>
												<td align="center" bgcolor="#e9ecef" style="padding: 12px 24px;font-family: Source Sans Pro, Helvetica, Arial, sans-serif;font-size: 14px;line-height: 20px;color: #666;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;mso-table-rspace: 0pt;mso-table-lspace: 0pt;">
													
													Copyright © '.date("Y").' Moovin, Todos los derechos reservados 
									
													<p style="margin: 0;">¿Cómo podemos ayudarte? 
									info@moovin.me </p>
												</td>
												</tr>
											</tbody>
											</table>
										</td>
										</tr>
									</tbody>
									</table>';
	
							$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	
							$result = wp_mail( $to, $subject, $body, $headers, array( '' ) );
	
							if ($result > 0){
	
								$email_notification =  $wpdb->update(
									$this->table_activator->moovin_tbl_notification_order(), 
									array(
									"status" => "1",
									"sent_at" => date_i18n("Y-m-d H:m:s")),
									array("id_notification" => $row["id_notification"])
								);
	
							}else{
	
								$email_notification =  $wpdb->update(
									$this->table_activator->moovin_tbl_notification_order(), 
									array(
									"status" => "2",
									"sent_at" => date_i18n("Y-m-d H:m:s")),
									array("id_notification" => $row["id_notification"])
								);
	
							}
					}
					// end foreach
				}
			}
		}

		function moovin_validate_place_order(){
			if( is_checkout() ) {
				$outzoneValue = "0";

				$current_shipping_method = WC()->session->get( 'chosen_shipping_methods' );

				if(count($current_shipping_method) > 0){
					$shipping_selected =  $current_shipping_method[0];

					if ( strpos(strtolower($current_shipping_method[0]), "moovin") !== false ){
						global $wpdb;

						$addressSelected =	WC()->session->get('moovin_address_selected');

						if ($addressSelected != null){
							$outzone =  $wpdb->get_results(
								"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_OUTZONE'" , ARRAY_A);
							
							if(isset($outzone[0]["value"])){
								$outzoneValue = $outzone[0]["value"];
							}
			
							$estimationData =	json_decode(WC()->session->get('moovin_estimation_get'), true); 
							if(!isset($estimationData["optionService"])){
								wc_add_wp_error_notices(new WP_Error(1,'Por favor seleccione una ubicación de envió.'));
							}else {
								$hasOndemand = false;
								$hasRoute =  false;
								if(is_array($estimationData["optionService"])){
									foreach($estimationData["optionService"] as $row){
										if($row["type"] == "Ondemand"){
											$hasOndemand = true;
										}
										if($row["type"] == "route"){
											$hasRoute = true;
										}
									}
								}else{
									if($outzoneValue =="0"){
										wc_add_wp_error_notices(new WP_Error(1,'Tuvimos problemas estimando el costo de envió a tu ubicación, por favor selecciona otra ubicación o vuelve a intentarlo.'));
									}else{
										wc_add_wp_error_notices(new WP_Error(1,'Lo sentimos el método de envío seleccionado requiere de su ubicación de envió, por favor indique su ubicación en el mapa.'));
									}
								}
			
								if($hasOndemand == false && $hasRoute == false){
									if($outzoneValue =="0"){
										wc_add_wp_error_notices(new WP_Error(1,'Tuvimos problemas estimando el costo de envió a tu ubicación, por favor selecciona otra ubicación o vuelve a intentarlo.'));
									}else{
										wc_add_wp_error_notices(new WP_Error(1,'Lo sentimos el método de envío seleccionado requiere de su ubicación de envió, por favor indique su ubicación en el mapa.'));
									}
								}

								if (strpos(strtolower($shipping_selected ), "moovin_shipping:") !== false){
									if(!$hasRoute){
										wc_add_wp_error_notices(new WP_Error(1,'Lo sentimos para la ubicación de entrega solicitada Moovin no ofrece el servicio MOOVIN 24H a 48H'));
									}
								}

								if (strpos(strtolower($shipping_selected ), "moovin_shipping_express:") !== false){
									if(!$hasOndemand){
										wc_add_wp_error_notices(new WP_Error(1,'Lo sentimos para la ubicación de entrega solicitada Moovin no ofrece el Servicio Express 4H'));
									}
								}								
							}

						}else{
							wc_add_wp_error_notices(new WP_Error(1,'Lo sentimos el método de envío seleccionado requiere de su ubicación de envió, por favor indique su ubicación en el mapa.'));
						}
					}
				}
			}
		}
	
		function add_every_three_minutes(){
				$schedules['every_three_minutes'] = array(
					'interval'  => 180,
					'display'   => __( 'Every 3 Minutes', 'textdomain' )
			);
			return $schedules;
		}

		function moovin_calculate_weight($value){
			//Moovin Accept only kg values 
			switch(get_option('woocommerce_weight_unit')){
				case "kg":
					return $value;
				break;
				case "g":
					return $value/1000;
				break;
				case "lbs":
					return $value * 0.453592;	
				break;
				case "oz":
					return $value / 35.274;	
				break;
				default:
					return $value;
				break;
			}
		}

		function moovin_calculate_dimmension($value){
			//Moovin Accept only cm values 
			switch(get_option('woocommerce_dimension_unit')){
				case "m":
					return $value * 100;
				break;
				case "cm":
					return $value;
				break;
				case "mm":
					return $value / 10;	
				break;
				case "in":
					return $value * 2.54;	
				break;
				case "yd":
					return $value * 91.44;	
				break;
				default:
					return $value;
				break;
			}
		}

		function moovin_woocommerce_shipping_notice_displayed(){

			global $wpdb;

			$status_plugin = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_STATUS"
				), ARRAY_A
			);		
			
			/**
			 * The code that include shipping method.
			 * This action is documented in includes/moovin-shipping.php
			 */
			if ($status_plugin[0]["value"] == "1"){
				/**
				 * Check if route service is enable
				 */
				$route_service = $wpdb->get_results(
					$wpdb->prepare(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_ROUTE_SERVICE"
					), ARRAY_A
				);	
			
				/**
				 * Check if express service is enable
				 */
				$express_service = $wpdb->get_results(
					$wpdb->prepare(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EXPRESS_SERVICE"
					), ARRAY_A
				);

				$schedule_service = $wpdb->get_results(
					$wpdb->prepare(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EXPRESS_SCHEDULE"
					), ARRAY_A
				);
		
				if(count($schedule_service) > 0){
					$dateCurrent = strtotime(date_i18n('Y-m-d H:i:s'));
					$dateStart =  strtotime(date_i18n("Y-m-d ".$schedule_service[0]["value"]));
					$dateFinal =  strtotime(date_i18n("Y-m-d ".$schedule_service[0]["value1"]));
					
					if($dateCurrent > $dateStart && $dateFinal > $dateCurrent){
						$schedule = true;
					}else{
						$schedule = false;
					}
				}
				
				if($route_service[0]["value"] == "1" && $express_service[0]["value"] == "1" && $schedule){
					echo '</tr><tr class="shipping info"><th>&nbsp;</th><td data-title="Delivery info">Dos de tus métodos de envió requieren de tu ubicación para realizar el calculo de tarifa</td>';

				}else if(
					($route_service[0]["value"] == "1" && $express_service[0]["value"] == "0" ) || 
					($route_service[0]["value"] == "0" && $express_service[0]["value"] == "1") ||
					( $route_service[0]["value"] == "1" && $express_service[0]["value"] == "1" && $schedule == false )){
						echo '</tr><tr class="shipping info"><th>&nbsp;</th><td data-title="Delivery info">Uno de tus métodos de envió requieren de tu ubicación para realizar el calculo de tarifa</td>';
				}else{
					echo '';
				}
			}
		}


		//WP AJAX CALLS
		function moovin_session_end($user_login, $user){
				$user_id = $user->ID;
				$meta_key = 'sg_user_addresses';
				$new_address = get_user_meta($user_id, $meta_key, true);
				$session_addresses = json_encode(WC()->session->get('sg_user_addresses'),true);


				WC()->session->__unset('sg_user_addresses');
		}

		function moovin_check_shipping_method(){
			$current_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
 
			if(isset($current_shipping_method[0])){
				$current_shipping_method = $current_shipping_method[0];
				if ( strpos(strtolower($current_shipping_method), "moovin") !== false ){
					echo wp_json_encode(array("error"=>false , "moovin"=> true , "name"=>$current_shipping_method));
				}else{
					echo wp_json_encode(array("error"=>false , "moovin"=> false, "name"=>$current_shipping_method));
				}
			}else{
				echo wp_json_encode(array("error"=>false , "moovin"=> false, "name"=>$current_shipping_method));
			}
			wp_die();
		}

		function moovin_delivery_addresses(){

				//Object address google maps
				$addressNew = $_POST['address'];

				$latitude =	sanitize_text_field($addressNew["position"]["lat"]);
				$longitude = sanitize_text_field($addressNew["position"]["lng"]);

				$response = $this->moovin_get_refresh_token();

				if($response["error"] == false){
		
					$get_url = $response["url"]."/rest/api/moovinEnterprise/partners/insideZoneCoverage?latitude=".$latitude."&longitude=".$longitude;
					
					$responseZones = wp_remote_post($get_url, array(
						'headers'     => array('Content-Type' => 'application/json; charset=utf-8' , 'token' =>  $response["token"]),
						'method'      => 'GET',
						'data_format' => 'body',
					));

					$zones = json_decode($responseZones["body"]);
							
					if($zones->status == "SUCCESS"){

							$default_address_type_text = (get_option('sg_del_address_card_title') !== '') ? get_option('sg_del_address_card_title', 'Unknown') : 'Unknown';
							$default_address_btn_text = (get_option('sg_del_address_card_btn_text') !== '') ? get_option('sg_del_address_card_btn_text', 'Deliver here') : 'Deliver here';
							$address = new stdClass();
							$section = sanitize_text_field($_REQUEST['section']);
							foreach ($_REQUEST['address'] as $key => $value) {
								$address->$key = $value;
							}

							$address_id = sanitize_text_field($address->id);
							$has_default = sanitize_text_field($address->default);
							unset($address->default);

							
							$session_addresses = WC()->session->get('sg_user_addresses');
							if ($session_addresses) {
								array_unshift($session_addresses['addresses'], $address);
								if ($has_default === 'true') {
									$session_addresses['selected'] = $address_id;
								}
								WC()->session->set('sg_user_addresses', $session_addresses);

								//Guardo ubicacion de envío
								WC()->session->set('moovin_address_selected', json_encode($address));
							} else {
								$new_address = array('addresses' => array($address), 'selected' => $address_id);
								WC()->session->set('sg_user_addresses', $new_address);

								//Guardo ubicacion de envío
								WC()->session->set('moovin_address_selected', json_encode($address));
							}
					

							?>

							<div class="single-address address-inline available-address">

								<div class="sg-header-action-container">
									<?php
									if ($has_default === 'true' || $address_id === '0') {
									?>
										<input type="radio" class="sg-del-add-select" name="<?php echo esc_attr('selected_' . $section . '_deliver_address'); ?>" data-type="<?php echo esc_attr($section); ?>" value="<?php echo esc_attr($address->id); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_' . $address->id); ?>" checked="true">
									<?php
									} else {
									?>
										<input type="radio" class="sg-del-add-select" name="<?php echo esc_attr('selected_' . $section . '_deliver_address'); ?>" data-type="<?php echo esc_attr($section); ?>" value="<?php echo esc_attr($address->id); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_' . $address->id); ?>">
										<div class="sg-dropdown">
											<div class="sg-menu-icon sg-menu-action">
												<span class="sg-menu-option"><?php echo esc_html('...'); ?></span>
											</div>
											<div class="sg-dropdown-list">
												<div id="<?php echo esc_attr('sg_delivery_address_' . $section . '_remove_' . $address->id); ?>" class="sg-remove-button sg-dropdown-item danger"><?php esc_html_e('Remove', 'woocommerce-delivery-location-map-picker'); ?></div>
											</div>
										</div>
									<?php
									}
									?>
								</div>
								<div class="item">
									<h4 class="title"><?php esc_html_e(($address->address_type !== '') ? $address->address_type : $default_address_type_text, 'woocommerce-delivery-location-map-picker'); ?></h4>
									<p class="text-capitalize address"><?php echo esc_html($address->formatted_address); ?></p>
								</div>
								<p class="action-container">
									<label for="<?php echo esc_attr('sg_delivery_address_' . $section . '_' . $address->id); ?>" class="text-uppercase sg-button sg-del-add-select-button"><?php esc_html_e($default_address_btn_text, 'woocommerce-delivery-location-map-picker'); ?></label>
								</p>
							</div>

							<?php

							wp_die();

					}else{
						WC()->session->set('moovin_address_selected', null);

						echo wp_json_encode(array("error"=>true, "msg"=> "La ubicación de entrega se sale del area de cobertura Moovin, por favor intenta con otra ubicación o consulta con otro metodo de envío disponible en la tienda [".$zones->status."]"));
						wp_die();
					}
				}else{
					echo wp_json_encode(array("error"=>true , "msg"=> "Error de comunicación con moovin"));
					wp_die();
				}

		}

		function moovin_get_delivery_address(){
				$sg_del_add_status = get_option('sg_del_enable_address_picker', 'disable');
				$id = sanitize_text_field($_REQUEST['id']);
				$section = sanitize_text_field($_REQUEST['section']); // billing or shipping
				
				$session_addresses = WC()->session->get('sg_user_addresses');
				$new_address = $session_addresses['addresses'];
				if ((get_option('sg_del_enable_address_picker') === "enable_for_both" && $section === "billing") || (get_option('sg_del_enable_address_picker') === "enable_for_shipping" && $section === "shipping") || (get_option('sg_del_enable_address_picker') === "enable_for_billing" && $section === "billing")) {

					$session_addresses['selected'] = $id;
					WC()->session->set('sg_user_addresses', $session_addresses);
				}
				
				$result = '';
				foreach ($new_address as $key => $address) {
					if ($address->id === $id) {
						$result = $address;
					}
				}

				if ($result) {
					WC()->session->set('moovin_address_selected', json_encode($result));
					wp_send_json($result);
				}
		
		}

		function moovin_remove_delivery_address(){
			$address_id = sanitize_text_field($_REQUEST['id']);
			$addresses = WC()->session->get('sg_user_addresses')['addresses'];
			
			foreach ($addresses as $key => $address) {
				if ($address->id === $address_id) {
					unset($addresses[$key]);
				}
			}
			
			$new_address_list = WC()->session->get('sg_user_addresses');
			$new_address_list['addresses'] = $addresses;
			WC()->session->set('sg_user_addresses', $new_address_list);
		}

		function moovin_clear_delivery_address(){
			$meta_key = 'sg_user_addresses';
			WC()->session->__unset($meta_key);
			
		}

		function moovin_validate_address($fields, $errors){
			$current_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
 
			if(isset($current_shipping_method[0])){
				$current_shipping_method = $current_shipping_method[0];
				if ( strpos(strtolower($current_shipping_method), "moovin") !== false ){
					// Check if user use map
					$is_billing_valid = false;
					$is_shipping_valid = false;
					if (in_array("billing_address_latitude_required", $errors->get_error_codes())) {
						$is_billing_valid = true;
					} else if (in_array("billing_address_longitude_required", $errors->get_error_codes())) {
						$is_billing_valid = true;
					}

					if (in_array("shipping_address_latitude_required", $errors->get_error_codes())) {
						$is_shipping_valid = true;
					} else if (in_array("shipping_address_longitude_required", $errors->get_error_codes())) {
						$is_shipping_valid = true;
					}
					if ($is_billing_valid) {
						foreach ($errors->get_error_codes() as $code) {
							if ($is_billing_valid && $code === 'billing_address_latitude_required') {
								$errors->remove($code);
							} else if ($is_billing_valid && $code === 'billing_address_longitude_required') {
								$errors->remove($code);
							}
						}
						if ($is_billing_valid) {
							$errors->add('sg_billing_address', '<b>Seleccione una ubicación de envío</b>');
							return;
						}
					}
					if ($is_shipping_valid) {
						foreach ($errors->get_error_codes() as $code) {
							if ($is_shipping_valid && $code === 'shipping_address_latitude_required') {
								$errors->remove($code);
							} else if ($is_shipping_valid && $code === 'shipping_address_longitude_required') {
								$errors->remove($code);
							}
						}
						if ($is_shipping_valid) {
							$errors->add('sg_shipping_address', '<b>Seleccione una dirreción de envío</b>');
							return;
						}
					}
				}
			}	
		}
	
		function custom_override_checkout_fields($fields){
			global $wpdb;
			
			$mode =  $wpdb->get_results(
				"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_MODE'" , ARRAY_A);

			if($mode != null){
				if($mode[0]["value"]== "simple"){
					
					$fields['country']['required'] = false;
  					is_array($fields["country"]['class']) ?  array_push($fields["country"]['class'] , "sg_del_add_hidden_fields") :  $fields["country"]['class'] = array('sg_del_add_hidden_fields');
			
					$fields['address_1']['required'] = false;
  					is_array($fields["address_1"]['class']) ?  array_push($fields["address_1"]['class'] , "sg_del_add_hidden_fields") : $fields["address_1"]['class'] = array('sg_del_add_hidden_fields');

					$fields['address_2']['required'] = false;	
  					is_array($fields["address_2"]['class']) ?  array_push($fields["address_2"]['class'] , "sg_del_add_hidden_fields") : $fields["address_2"]['class'] = array('sg_del_add_hidden_fields');
					
					$fields['city']['required'] = false;	
  					is_array($fields["city"]['class']) ?  array_push($fields["city"]['class'] , "sg_del_add_hidden_fields") : $fields["city"]['class'] = array('sg_del_add_hidden_fields');
					
					$fields['state']['required'] = false;
  					is_array($fields["state"]['class']) ?  array_push($fields["state"]['class'] , "sg_del_add_hidden_fields") : $fields["state"]['class'] = array('sg_del_add_hidden_fields');
					
					$fields['postcode']['required'] = false;
  					is_array($fields["postcode"]['class']) ?  array_push($fields["postcode"]['class'] , "sg_del_add_hidden_fields") : $fields["postcode"]['class'] = array('sg_del_add_hidden_fields');
					
				} 
			}
			
			return $fields;
		}
	
		function moovin_ship_different_address(){
			return false;
		}

		function moovin_get_package(){
				global $wpdb;

				$package =  $wpdb->get_results(
					"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter in  ('MOOVIN_PKG_WEIGHT' , 'MOOVIN_PKG_SIZE', 'MOOVIN_DEFAULT_LOCATION') " , ARRAY_A);		

				$weight = "";
				$size  = "";
				$latitudeCollect  = "";
				$longitudeCollect  = "";

				foreach($package as $row){
					switch($row["cod_parameter"]){
						case "MOOVIN_PKG_WEIGHT":
							$weight =  $row["value"];
						break;
						case "MOOVIN_PKG_SIZE":
							$size =  $row["value"];
						break;
						case "MOOVIN_DEFAULT_LOCATION":
							if($row["value"] != ""){
								$latitudeCollect =  $row["value"];
							}
							if($row["value1"] != ""){
								$longitudeCollect =  $row["value1"];
							}
						break;
					}
				}

				$sizePackage =  $wpdb->get_results(
					"SELECT * from " . $this->table_activator->moovin_tbl_pkgs_sizes() ." WHERE name = '".$size."'"  , ARRAY_A);		

				return array("moovin" => $sizePackage, 
							"size"=>$size , 
							"weight" => $weight , 
							"latitudeCollect"=> $latitudeCollect ,
							"longitudeCollect"=>$longitudeCollect );
		}

		function moovin_get_documents(){
			global $wpdb;

			$documents = array();

			$cedula = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_TASK_DOCUMENT"
				), ARRAY_A
			);

			if ($cedula[0]["value"] == "1" ){
				$fields = array();
				$front = array( "name"=>"Cédula frontal",
								"type"=>"image",
								"description"=>"Que la cédula se encuentre en perfecto estado",
								"url"=>"Información sobre prodecimiento"
								);
				$back = array( "name"=>"Cédula reverso",
								"type"=>"image",
								"description"=>"Que la cédula se encuentre en perfecto estado",
								"url"=>"Información sobre prodecimiento"
								);
	
				array_push($fields ,$front );
				array_push($fields ,$back );

				$doc = array("name" => "Cédula" , "fields"=>$fields);
	
				array_push($documents, $doc);
			}
			
			return $documents;
		}


		function moovin_email_notification($order_id, $email){
			global $wpdb;
	
			$notification = $wpdb->get_results(
				$wpdb->prepare(
						"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EMAIL_NOTIFICATION"
				), ARRAY_A
			);
	
			if (count($notification) > 0){
				if($notification[0]["value"] == "1"){
					//Create table to notification orders
					if ($wpdb->get_var("show tables like '" . $this->table_activator->moovin_tbl_notification_order() . "'") != $this->table_activator->moovin_tbl_notification_order()) {
	
						$sql_notification_order="CREATE TABLE `" . $this->table_activator->moovin_tbl_notification_order() . "` ( 
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
	
					$email_notification = $wpdb->insert($this->table_activator->moovin_tbl_notification_order(), array(
						'order_id' => $order_id, 
						"email"=> $email,
						'status' =>"0",
						"created_at" => date_i18n("Y-m-d H:i:s"),
						"sent_at" => date_i18n("Y-m-d H:i:s")	
					));
				}	
			}
		}


		function moovin_delivery_complete_order($order_id){
			global $wpdb;
			$order = wc_get_order($order_id);

			
			if($order->get_status() == 'completed' || $order->get_status() == "processing" || $order->get_status() =='on-hold')
				{
				
				$data = $order->get_data();
				
				$orderMoovin = $wpdb->get_results("SELECT * from " . $this->table_activator->moovin_tbl_orders() . " WHERE order_id = " . $order_id, ARRAY_A);
				
				if(count($orderMoovin) == 0)
					{
					
					$order               = wc_get_order($order_id);
					$total_sales         = 0;
					$num_items_sold      = 0;
					$listProduct         = array();
					$pointCollect        = array();
					$pointDelivery       = array();
					$documents           = array();
					$shippingMethod      = "";
					$noteContentDelivery = $this->get_order_free_shipping_descriptions($order_id) . " " . $order->get_customer_note();
					
					
					if($order->has_shipping_method('moovin_shipping') || $order->has_shipping_method('moovin_shipping_express'))
						{
						
						if(is_a($order, 'WC_Order'))
							{
							//Payment method seletec 
							$chosen_payment_method = WC()->session->get('chosen_payment_method');
							
							
							// Get if payment method needs  a confirmation
							$paymentCode     = array();
							$paymentCodeList = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_PAYMENT_METHOD"), ARRAY_A);
							
							
							if(count($paymentCodeList) > 0)
								{
								$paymentCode = explode(",", $paymentCodeList[0]["value"]);
								} //count($paymentCodeList) > 0
							
							// Get if payment is in site money
							$paymentCodeMoneyList = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_PAYMENT_MONEY"), ARRAY_A);
							
							
							if(count($paymentCodeMoneyList) > 0)
								{
								$paymentMoneyCode = explode(",", $paymentCodeMoneyList[0]["value"]);
								} //count($paymentCodeMoneyList) > 0
							
							// Get if payment is in site card
							$paymentCodeCardList = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_PAYMENT_CARD"), ARRAY_A);
							
							if(count($paymentCodeMoneyList) > 0)
								{
								$paymentCardCode = explode(",", $paymentCodeCardList[0]["value"]);
								} //count($paymentCodeMoneyList) > 0
							
							// Get if payment is in site 
							//Check payment method is in the list
							$paymentInSite = "none";
							if(count($paymentCardCode) > 0 || count($paymentMoneyCode) > 0)
								{
								if(in_array($chosen_payment_method, $paymentCardCode))
									{
									$paymentInSite = "card";
									} //in_array($chosen_payment_method, $paymentCardCode)
								else if(in_array($chosen_payment_method, $paymentMoneyCode))
									{
									$paymentInSite = "money";
									} //in_array($chosen_payment_method, $paymentMoneyCode)
								} //count($paymentCardCode) > 0 || count($paymentMoneyCode) > 0
							
							$packageSize    = $this->moovin_get_package();
							$estimation     = array();
							$estimationData = json_decode(WC()->session->get('moovin_estimation_get'), true);
							
							//Get name of shipping method
							$titleExpress = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_EXPRESS_SERVICE"), ARRAY_A);
							
							$titleRoutes = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_ROUTE_SERVICE"), ARRAY_A);
							
							//GET estimation 
							foreach($estimationData["optionService"] as $row)
								{
								if($order->get_shipping_method() == ($titleExpress[0]["value1"] == "" ? "MOOVIN Express 4H" : $titleExpress[0]["value1"]))
									{
									if($row["type"] == "Ondemand")
										{
										$estimation     = $row;
										$shippingMethod = $row["type"];
										} //$row["type"] == "Ondemand"
									
									} //$order->get_shipping_method() == ($titleExpress[0]["value1"] == "" ? "MOOVIN Express 4H" : $titleExpress[0]["value1"])
								else if($order->get_shipping_method() == ($titleRoutes[0]["value1"] == "" ? "MOOVIN 24H a 48H" : $titleRoutes[0]["value1"]))
									{
									if($row["type"] == "route")
										{
										$estimation     = $row;
										$shippingMethod = $row["type"];
										} //$row["type"] == "route"
									} //$order->get_shipping_method() == ($titleRoutes[0]["value1"] == "" ? "MOOVIN 24H a 48H" : $titleRoutes[0]["value1"])
								} //$estimationData["optionService"] as $row
							
							//Point to collect
							$collect = $this->get_point_collect();
							
							$pointCollect = array(
								"latitude" => $collect["latitudeCollect"],
								"longitude" => $collect["longitudeCollect"],
								"locationAlias" => $collect["address"],
								"name" => $collect["nameContactCollect"],
								"phone" => $collect["phoneContactCollect"],
								"notes" => $collect["notesContactCollect"]
							);
							
							//Apply payments in site
							$paymentTask = array();
							if($paymentInSite != "none")
								{
								$currencyInSite = "";
								
								if(get_woocommerce_currency() == "USD")
									{
									$currencyInSite = "dolares";
									} //get_woocommerce_currency() == "USD"
								elseif(get_woocommerce_currency() == "EUR")
									{
									$currencyInSite = "euros";
									} //get_woocommerce_currency() == "EUR"
								elseif(get_woocommerce_currency() == "CRC")
									{
									$currencyInSite = "colones";
									} //get_woocommerce_currency() == "CRC"
								elseif(get_woocommerce_currency() == "HNL")
									{
									$currencyInSite = "lempira";
									} //get_woocommerce_currency() == "HNL"
								
								if($paymentInSite == "card")
									{
									$paymentTask = array(
										array(
											"description" => "Total de cobro",
											"amount" => $order->get_total(),
											"currency" => $currencyInSite,
											"method" => "creditCard"
										)
									);
									} //$paymentInSite == "card"
								else if($paymentInSite == "money")
									{
									$paymentTask = array(
										array(
											"description" => "Total de cobro",
											"amount" => $order->get_total(),
											"currency" => $currencyInSite,
											"method" => "cash"
										)
									);
									} //$paymentInSite == "money"
								} //$paymentInSite != "none"
							
							//Point to delivery
							$addressSelected = json_decode(WC()->session->get('moovin_address_selected'), true);
							
							$pointDelivery = array(
								"latitude" => $addressSelected["position"]["lat"],
								"longitude" => $addressSelected["position"]["lng"],
								"locationAlias" => (trim(strlen($addressSelected["landmark"])) > 0 ? $addressSelected["landmark"] . ", " : "") . (trim(strlen($addressSelected["address_2"])) > 0 ? $addressSelected["address_2"] . ", " : "") . (trim(strlen($addressSelected["door"])) > 0 ? $addressSelected["door"] . ", " : "") . $addressSelected["formatted_address"] . "[" . $addressSelected["address_type"] . "]",
								"name" => $data['billing']['first_name'] . " " . $data['billing']['last_name'],
								"phone" => $data['billing']['phone'],
								"notes" => (trim(strlen($addressSelected["landmark"])) > 0 ? $addressSelected["landmark"] . ", " : "") . $addressSelected["formatted_address"],
								"documents" => $this->moovin_get_documents(),
								"listPayment" => $paymentTask
							);
							
							
							if(!in_array($chosen_payment_method, $paymentCode) && $order->get_status() != "on-hold" )
								{
								// Payment Method without confirmation
								
								//GET Items request 
								foreach($order->get_items() as $item)
									{
									$product   = $item->get_product();
									$item_data = $item->get_data();
									
									$length = $product->get_length() == "" || $product->get_length() == null ? $packageSize["moovin"][0]["length_cm"] : $this->moovin_calculate_dimmension($product->get_length());
									$width  = $product->get_width() == "" || $product->get_width() == null ? $packageSize["moovin"][0]["width_cm"] : $this->moovin_calculate_dimmension($product->get_width());
									$weight = $product->get_weight() == "" || $product->get_weight() == null ? $packageSize["weight"] : $this->moovin_calculate_weight($product->get_weight());
									$high   = $product->get_height() == "" || $product->get_height() == null ? $packageSize["moovin"][0]["high_cm"] : $this->moovin_calculate_dimmension($product->get_height());
									
									$size = $wpdb->get_results("SELECT * from " . $this->table_activator->moovin_tbl_pkgs_sizes() . " WHERE length_cm >= " . $length . "  AND  width_cm >= " . $width . " AND high_cm >= " . $high . " AND weight_kg >= " . $weight . " ORDER BY id_pkgs_size ASC LIMIT 1", ARRAY_A);
									
									$description = strlen($product->get_short_description()) > 250 ? substr($product->get_short_description(), 0, 250) : $product->get_short_description();
									$product     = array(
										"quantity" => $item_data['quantity'],
										"nameProduct" => $product->get_name(),
										"description" => $description,
										"size" => isset($size[0]["name"]) ? $size[0]["name"] : $packageSize["size"],
										"weight" => $product->get_weight() == "" || $product->get_weight() == null ? $packageSize["weight"] : $this->moovin_calculate_weight($product->get_weight()),
										"price" => $item_data["total"],
										"codeProduct" => $product->get_sku()
									);
									
									$num_items_sold = $item_data['quantity'] + $num_items_sold;
									
									array_push($listProduct, $product);
									} //$order->get_items() as $item
								
								$response = $this->moovin_get_refresh_token();
								
								if($response["error"] == false)
									{
									//Check if fulfillment is enabled
									$fulfillment = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_FULFILLMENT"), ARRAY_A);
									
									if($fulfillment[0]["value"] == " 0")
										{
										//Request create order - regular
										$requestCreateOrder = array(
											"idEstimation" => $estimationData["idEstimation"],
											"idDelivery" => $estimation["id"],
											"idOrder" => $order_id,
											"email" => $collect["emailContactCollect"],
											"emailAccount" => $collect["emailContactCollect"],
											"prepared" => false,
											"ensure" => true,
											"pointCollect" => $pointCollect,
											"pointDelivery" => $pointDelivery,
											"listProduct" => $listProduct
										);
										} //$fulfillment[0]["value"] == " 0"
									else
										{
										//Request create order - fulfillment
										$requestCreateOrder = array(
											"idEstimation" => $estimationData["idEstimation"],
											"idDelivery" => $estimation["id"],
											"idOrder" => $order_id,
											"email" => $collect["emailContactCollect"],
											"emailAccount" => $collect["emailContactCollect"],
											"prepared" => false,
											"ensure" => true,
											"cediMoovin" => true,
											"pointDelivery" => $pointDelivery,
											"listProduct" => $listProduct
										);
										}
									
									//echo json_encode($requestCreateOrder);
									
									// Send request create order
									$post_url = $response["url"] . "/rest/api/ecommerceExternal/createOrder";
									
									$responseCreateOrder = wp_remote_post($post_url, array(
										'headers' => array(
											'Content-Type' => 'application/json; charset=utf-8',
											'token' => $response["token"]
										),
										'method' => 'POST',
										'data_format' => 'body',
										'body' => json_encode($requestCreateOrder)
									));
									
									$createOrder = json_decode($responseCreateOrder["body"]);
									
									//Order created 
									if(!is_wp_error($responseCreateOrder) && ($createOrder->status == "SUCCESS" || $createOrder->idPackage > 0))
										{
										
										// Order completed
										foreach($order->get_items() as $item)
											{
											$product = $item->get_product();
											
											$item_data = $item->get_data();
											
											$description = strlen($product->get_short_description()) > 250 ? substr($product->get_short_description(), 0, 250) : $product->get_short_description();
											$wpdb->insert($this->table_activator->moovin_tbl_order_products(), array(
												"order_id" => $order_id,
												"quantity" => $item_data['quantity'],
												"name_product" => $product->get_name(),
												"description" => $description,
												"length" => $product->get_length() == "" || $product->get_length() == null ? $packageSize["moovin"][0]["length_cm"] : $this->moovin_calculate_dimmension($product->get_length()),
												"width" => $product->get_width() == "" || $product->get_width() == null ? $packageSize["moovin"][0]["width_cm"] : $this->moovin_calculate_dimmension($product->get_width()),
												"weight" => $product->get_weight() == "" || $product->get_weight() == null ? $packageSize["weight"] : $this->moovin_calculate_weight($product->get_weight()),
												"high" => $product->get_height() == "" || $product->get_height() == null ? $packageSize["moovin"][0]["high_cm"] : $this->moovin_calculate_dimmension($product->get_height()),
												"price" => $item_data["total"],
												"code_product" => $product->get_sku()
											));
											
											$total_sales = $total_sales + $item_data["total"];
											} //$order->get_items() as $item
										
										$wpdb->insert($this->table_activator->moovin_tbl_orders(), array(
											"order_id" => $order_id,
											"date_created" => $data['date_created']->date('Y-m-d H:i:s'),
											"num_items_sold" => $num_items_sold,
											"total_sales" => $total_sales,
											"tax_total" => $data['total_tax'],
											"shipping_total" => $data['shipping_total'],
											"net_total" => $total_sales,
											"id_estimation" => $estimationData["idEstimation"],
											"id_delivery" => $estimation["id"],
											"email" => $collect["emailContactCollect"],
											"email_account" => $data['billing']['email'],
											"prepared" => false,
											"latitude_collect" => $collect["latitudeCollect"],
											"longitude_collect" => $collect["longitudeCollect"],
											"location_alias_collect" => $collect["address"],
											"contact_collect" => $collect["nameContactCollect"],
											"phone_collect" => $collect["phoneContactCollect"],
											"notes_collect" => $collect["notesContactCollect"],
											"latitude_delivery" => $addressSelected["position"]["lat"],
											"longitude_delivery" => $addressSelected["position"]["lng"],
											"location_alias_delivery" => (trim(strlen($addressSelected["landmark"])) > 0 ? $addressSelected["landmark"] . ", " : "") . (trim(strlen($addressSelected["address_2"])) > 0 ? $addressSelected["address_2"] . ", " : "") . (trim(strlen($addressSelected["door"])) > 0 ? $addressSelected["door"] . ", " : "") . $addressSelected["formatted_address"] . "[" . $addressSelected["address_type"] . "]",
											"contact_delivery" => $data['billing']['first_name'] . " " . $data['billing']['last_name'],
											"phone_delivery" => $data['billing']['phone'],
											"notes_delivery" => $noteContentDelivery,
											"ensure" => false,
											"status_order_delivery_moovin" => "CREATED",
											"id_package_moovin" => $createOrder->idPackage,
											"date_update_moovin" => "",
											"qr_code" => $createOrder->orderQR,
											"response_order_created" => json_encode($responseCreateOrder["body"]),
											"response_order_ready" => "",
											"date_order_created" => date_i18n("Y-m-d H:i:s"),
											"date_order_ready" => "",
											"shipping_method" => $shippingMethod . "#" . $paymentInSite,
											"fulfillment" => $fulfillment[0]["value"]
										));
										
										// Add custom fields to order woocommerce
										update_post_meta($order_id, 'delivery_latitude', $addressSelected["position"]["lat"]);
										update_post_meta($order_id, 'delivery_longitude', $addressSelected["position"]["lng"]);
										update_post_meta($order_id, 'delivery_address', $addressSelected["formatted_address"] . "[" . $addressSelected["address_type"] . "]");
										update_post_meta($order_id, 'estimation_id', $estimationData["idEstimation"]);
										update_post_meta($order_id, 'package_id', $createOrder->idPackage);
										update_post_meta($order_id, 'moovin_qr', $createOrder->orderQR);
										
										//Check if autocollect is enabled
										$autocollect = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_COLLECT_AUTO"), ARRAY_A);
										
										
										if($autocollect[0]["value"] == "1" && $shippingMethod == "route")
											{
											$requestCollect = array(
												"idPackage" => $createOrder->idPackage
											);
											
											$post_url      = $response["url"] . "/rest/api/ecommerceExternal/completeOrder";
											$responseOrder = wp_remote_post($post_url, array(
												'headers' => array(
													'Content-Type' => 'application/json; charset=utf-8',
													'token' => $response["token"]
												),
												'method' => 'POST',
												'data_format' => 'body',
												'body' => json_encode($requestCollect)
											));
											
											if(!is_wp_error($responseOrder))
												{
												$completeOrder = json_decode($responseOrder["body"], true);
												
												if($completeOrder["status"] == "SUCCESS")
													{
													
													//Register email notification
													$this->moovin_email_notification($order_id, $data['billing']['email']);
													
													
													$date  = date_i18n("Y-m-d H:i:s");
													$order = $wpdb->update($this->table_activator->moovin_tbl_orders(), array(
														"status_order_delivery_moovin" => "READY",
														"date_order_ready" => $date,
														"response_order_ready" => json_encode($responseOrder["body"]),
														"date_update_moovin" => $date
													), array(
														"order_id" => $order_id
													));
													} //$completeOrder["status"] == "SUCCESS"
												} //!is_wp_error($responseOrder)
											} //$autocollect[0]["value"] == "1" && $shippingMethod == "route"
										
										} //!is_wp_error($responseCreateOrder) && ($createOrder->status == "SUCCESS" || $createOrder->idPackage > 0)
									else
										{
										
										// Order pending
										foreach($order->get_items() as $item)
											{
											$product = $item->get_product();
											
											$item_data = $item->get_data();
											
											$description = strlen($product->get_short_description()) > 250 ? substr($product->get_short_description(), 0, 250) : $product->get_short_description();
											$wpdb->insert($this->table_activator->moovin_tbl_order_products(), array(
												"order_id" => $order_id,
												"quantity" => $item_data['quantity'],
												"name_product" => $product->get_name(),
												"description" => $description,
												"length" => $product->get_length() == "" || $product->get_length() == null ? $packageSize["moovin"][0]["length_cm"] : $this->moovin_calculate_dimmension($product->get_length()),
												"width" => $product->get_width() == "" || $product->get_width() == null ? $packageSize["moovin"][0]["width_cm"] : $this->moovin_calculate_dimmension($product->get_width()),
												"weight" => $product->get_weight() == "" || $product->get_weight() == null ? $packageSize["weight"] : $this->moovin_calculate_weight($product->get_weight()),
												"high" => $product->get_height() == "" || $product->get_height() == null ? $packageSize["moovin"][0]["high_cm"] : $this->moovin_calculate_dimmension($product->get_height()),
												"price" => $item_data["total"],
												"code_product" => $product->get_sku()
											));
											
											$total_sales = $total_sales + $item_data["total"];
											} //$order->get_items() as $item
										
										
										$wpdb->insert($this->table_activator->moovin_tbl_orders(), array(
											"order_id" => $order_id,
											"date_created" => $data['date_created']->date('Y-m-d H:i:s'),
											"num_items_sold" => $num_items_sold,
											"total_sales" => $total_sales,
											"tax_total" => $data['total_tax'],
											"shipping_total" => $data['shipping_total'],
											"net_total" => $total_sales,
											"id_estimation" => $estimationData["idEstimation"],
											"id_delivery" => $estimation["id"],
											"email" => $collect["emailContactCollect"],
											"email_account" => $data['billing']['email'],
											"prepared" => false,
											"latitude_collect" => $collect["latitudeCollect"],
											"longitude_collect" => $collect["longitudeCollect"],
											"location_alias_collect" => $collect["address"],
											"contact_collect" => $collect["nameContactCollect"],
											"phone_collect" => $collect["phoneContactCollect"],
											"notes_collect" => $collect["notesContactCollect"],
											"latitude_delivery" => $addressSelected["position"]["lat"],
											"longitude_delivery" => $addressSelected["position"]["lng"],
											"location_alias_delivery" => (trim(strlen($addressSelected["landmark"])) > 0 ? $addressSelected["landmark"] . ", " : "") . (trim(strlen($addressSelected["address_2"])) > 0 ? $addressSelected["address_2"] . ", " : "") . (trim(strlen($addressSelected["door"])) > 0 ? $addressSelected["door"] . ", " : "") . $addressSelected["formatted_address"] . "[" . $addressSelected["address_type"] . "]",
											"contact_delivery" => $data['billing']['first_name'] . " " . $data['billing']['last_name'],
											"phone_delivery" => $data['billing']['phone'],
											"notes_delivery" => $noteContentDelivery,
											"ensure" => false,
											"status_order_delivery_moovin" => "PENDING",
											"date_update_moovin" => "",
											"qr_code" => "",
											"id_package_moovin" => "",
											"response_order_created" => json_encode($responseCreateOrder["body"]),
											"response_order_ready" => "",
											"date_order_created" => date_i18n("Y-m-d H:i:s"),
											"date_order_ready" => "",
											"shipping_method" => $shippingMethod . "#" . $paymentInSite,
											"fulfillment" => $fulfillment[0]["value"]
										));
										
										}
									
									
									} //$response["error"] == false
								else
									{
									
									// Order with error
									foreach($order->get_items() as $item)
										{
										$product = $item->get_product();
										
										$item_data = $item->get_data();
										
										$description = strlen($product->get_short_description()) > 250 ? substr($product->get_short_description(), 0, 250) : $product->get_short_description();
										$wpdb->insert($this->table_activator->moovin_tbl_order_products(), array(
											"order_id" => $order_id,
											"quantity" => $item_data['quantity'],
											"name_product" => $product->get_name(),
											"description" => $description,
											"length" => $product->get_length() == "" || $product->get_length() == null ? $packageSize["moovin"][0]["length_cm"] : $this->moovin_calculate_dimmension($product->get_length()),
											"width" => $product->get_width() == "" || $product->get_width() == null ? $packageSize["moovin"][0]["width_cm"] : $this->moovin_calculate_dimmension($product->get_width()),
											"weight" => $product->get_weight() == "" || $product->get_weight() == null ? $packageSize["weight"] : $this->moovin_calculate_weight($product->get_weight()),
											"high" => $product->get_height() == "" || $product->get_height() == null ? $packageSize["moovin"][0]["high_cm"] : $this->moovin_calculate_dimmension($product->get_height()),
											"price" => $item_data["total"],
											"code_product" => $product->get_sku()
										));
										
										$total_sales = $total_sales + $item_data["total"];
										} //$order->get_items() as $item
									
									$wpdb->insert($this->table_activator->moovin_tbl_orders(), array(
										"order_id" => $order_id,
										"date_created" => $data['date_created']->date('Y-m-d H:i:s'),
										"num_items_sold" => $num_items_sold,
										"total_sales" => $total_sales,
										"tax_total" => $data['total_tax'],
										"shipping_total" => $data['shipping_total'],
										"net_total" => $total_sales,
										"id_estimation" => $estimationData["idEstimation"],
										"id_delivery" => $estimation["id"],
										"email" => $collect["emailContactCollect"],
										"email_account" => $data['billing']['email'],
										"prepared" => false,
										"latitude_collect" => $collect["latitudeCollect"],
										"longitude_collect" => $collect["longitudeCollect"],
										"location_alias_collect" => $collect["address"],
										"contact_collect" => $collect["nameContactCollect"],
										"phone_collect" => $collect["phoneContactCollect"],
										"notes_collect" => $collect["notesContactCollect"],
										"latitude_delivery" => $addressSelected["position"]["lat"],
										"longitude_delivery" => $addressSelected["position"]["lng"],
										"location_alias_delivery" => (trim(strlen($addressSelected["landmark"])) > 0 ? $addressSelected["landmark"] . ", " : "") . (trim(strlen($addressSelected["address_2"])) > 0 ? $addressSelected["address_2"] . ", " : "") . (trim(strlen($addressSelected["door"])) > 0 ? $addressSelected["door"] . ", " : "") . $addressSelected["formatted_address"] . "[" . $addressSelected["address_type"] . "]",
										"contact_delivery" => $data['billing']['first_name'] . " " . $data['billing']['last_name'],
										"phone_delivery" => $data['billing']['phone'],
										"notes_delivery" => $noteContentDelivery,
										"ensure" => false,
										"status_order_delivery_moovin" => "ERROR",
										"date_update_moovin" => "",
										"qr_code" => "",
										"response_order_created" => "",
										"response_order_ready" => "",
										"date_order_created" => "",
										"date_order_ready" => "",
										"shipping_method" => $shippingMethod . "#" . $paymentInSite,
										"id_package_moovin" => "",
										"fulfillment" => $fulfillment[0]["value"]
										
									));
									
									}
								
								} //!in_array($chosen_payment_method, $paymentCode)
							else
								{
								// Payment Method required confirmation
								
								foreach($order->get_items() as $item)
									{
									$product = $item->get_product();
									
									$item_data = $item->get_data();
									
									$description = strlen($product->get_short_description()) > 250 ? substr($product->get_short_description(), 0, 250) : $product->get_short_description();
									$wpdb->insert($this->table_activator->moovin_tbl_order_products(), array(
										"order_id" => $order_id,
										"quantity" => $item_data['quantity'],
										"name_product" => $product->get_name(),
										"description" => $description,
										"length" => $product->get_length() == "" || $product->get_length() == null ? $packageSize["moovin"][0]["length_cm"] : $this->moovin_calculate_dimmension($product->get_length()),
										"width" => $product->get_width() == "" || $product->get_width() == null ? $packageSize["moovin"][0]["width_cm"] : $this->moovin_calculate_dimmension($product->get_width()),
										"weight" => $product->get_weight() == "" || $product->get_weight() == null ? $packageSize["weight"] : $this->moovin_calculate_weight($product->get_weight()),
										"high" => $product->get_height() == "" || $product->get_height() == null ? $packageSize["moovin"][0]["high_cm"] : $this->moovin_calculate_dimmension($product->get_height()),
										"price" => $item_data["total"],
										"code_product" => $product->get_sku()
									));
									
									$total_sales    = $total_sales + $item_data["total"];
									$num_items_sold = $item_data['quantity'] + $num_items_sold;
									
									} //$order->get_items() as $item
								
								//Check if fulfillment is enabled
								$fulfillment = $wpdb->get_results($wpdb->prepare("SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_FULFILLMENT"), ARRAY_A);
								
								$wpdb->insert($this->table_activator->moovin_tbl_orders(), array(
									"order_id" => $order_id,
									"date_created" => $data['date_created']->date('Y-m-d H:i:s'),
									"num_items_sold" => $num_items_sold,
									"total_sales" => $total_sales,
									"tax_total" => $data['total_tax'],
									"shipping_total" => $data['shipping_total'],
									"net_total" => $total_sales,
									"id_estimation" => $estimationData["idEstimation"],
									"id_delivery" => $estimation["id"],
									"email" => $collect["emailContactCollect"],
									"email_account" => $data['billing']['email'],
									"prepared" => false,
									"latitude_collect" => $collect["latitudeCollect"],
									"longitude_collect" => $collect["longitudeCollect"],
									"location_alias_collect" => $collect["address"],
									"contact_collect" => $collect["nameContactCollect"],
									"phone_collect" => $collect["phoneContactCollect"],
									"notes_collect" => $collect["notesContactCollect"],
									"latitude_delivery" => $addressSelected["position"]["lat"],
									"longitude_delivery" => $addressSelected["position"]["lng"],
									"location_alias_delivery" => (trim(strlen($addressSelected["landmark"])) > 0 ? $addressSelected["landmark"] . ", " : "") . (trim(strlen($addressSelected["address_2"])) > 0 ? $addressSelected["address_2"] . ", " : "") . (trim(strlen($addressSelected["door"])) > 0 ? $addressSelected["door"] . ", " : "") . $addressSelected["formatted_address"] . "[" . $addressSelected["address_type"] . "]",
									"contact_delivery" => $data['billing']['first_name'] . " " . $data['billing']['last_name'],
									"phone_delivery" => $data['billing']['phone'],
									"notes_delivery" => $noteContentDelivery,
									"ensure" => false,
									"status_order_delivery_moovin" => "CONFIRMATION",
									"id_package_moovin" => "",
									"date_update_moovin" => "",
									"qr_code" => "",
									"response_order_created" => "",
									"response_order_ready" => "",
									"date_order_created" => date_i18n("Y-m-d H:i:s"),
									"date_order_ready" => "",
									"shipping_method" => $shippingMethod . "#" . $paymentInSite,
									"fulfillment" => $fulfillment[0]["value"]
								));
								}
							} //is_a($order, 'WC_Order')
						} //$order->has_shipping_method('moovin_shipping') || $order->has_shipping_method('moovin_shipping_express')
					} //count($orderMoovin) == 0
				} //$order->has_status('completed')
			}
}