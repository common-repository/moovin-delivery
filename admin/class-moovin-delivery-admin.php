<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.moovin.me/
 * @since      1.0.0
 *
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Moovin_Delivery
 * @subpackage Moovin_Delivery/admin
 * @author     Javier Hernández M <javier.hernandez@moovin.me>
 */


class Moovin_Delivery_Admin {

    private $table_activator;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		include_once(MOOVIN_PLUGIN_PATH . '/includes/class-moovin-delivery-activator.php');
        $this->table_activator = new Moovin_Delivery_Activator();
	}

	//Create menu method  
	public function moovin_management_menu() {
		add_menu_page('Moovin', 'Moovin', 'manage_options', 'moovin-menu', array($this, "moovin_management_dashboard"), "dashicons-car", 22);
		add_submenu_page( 'moovin-menu', 'Rastreo de paquetes', 'Rastreo de paquetes','manage_options', 'moovin-menu', array($this, "moovin_management_dashboard"));
		add_submenu_page( 'moovin-menu', 'Ordenes', 'Ordenes','manage_options', 'moovin-menu-ordenes', array($this, "moovin_management_ordenes"));
		add_submenu_page( 'moovin-menu', 'Ajustes', 'Ajustes', 'manage_options', 'moovin-menu-conf',array($this, "moovin_management_config"));

    }

	public function moovin_management_config(){
		include_once(MOOVIN_PLUGIN_PATH."/admin/partials/moovin-delivery-admin-config.php"); 
	}

	public function moovin_management_ordenes(){
		include_once(MOOVIN_PLUGIN_PATH."/admin/partials/moovin-delivery-admin-orders.php");
	}
	
	public function moovin_management_dashboard(){
		include_once(MOOVIN_PLUGIN_PATH."/admin/partials/moovin-delivery-admin-dashboard.php"); 
	}

	public function moovin_lib_ajax_handler(){
        global $wpdb;

		$option = isset($_REQUEST['option']) ? trim($_REQUEST['option']) : "";

		switch($option){
			case "op_load_status":

				$current_env = "";
				$status_env = "0";
				$country = "CR";
				$credentials_sandbox = false;
				$credentials_prod = false;
				$credentials_google_maps = false;
				$default_location = false;
				$collect_location = null;
				$collect_inside = false;
				$default_package_size = false;
				$default_package_weigth = false;
				$moovin_contact = false;

				$parameters =  $wpdb->get_results(
											"SELECT * from " . $this->table_activator->moovin_tbl_parameters() , ARRAY_A);
			
				foreach($parameters as $row){
						switch($row["cod_parameter"]){
							case "MOOVIN_TOKEN_SANDBOX":
								if($row["value"] != ""){
									$credentials_sandbox =  true;
								}
							break;
							case "MOOVIN_TOKEN_PROD":
								if($row["value"] != ""){
									$credentials_prod =  true;
								}
							break;
							
							case "MOOVIN_PKG_SIZE":
								if($row["value"] != ""){
									$default_package_size =  true;
								}
							break;
							case "MOOVIN_PKG_WEIGHT":
								if($row["value"] != ""){
									$default_package_weigth =  true;
								}
							break;
							case "MOOVIN_DEFAULT_LOCATION": 
								if($row["value"] != "" && $row["value1"] != ""){
									$default_location =  true;
									$collect_location = $row;
								}
							break;
							case "MOOVIN_CURRENT_ENV":
								if($row["value"] == ""){
									$current_env =  "off";
								}else{
									$current_env = $row["value"];
								}
								break;
							case "MOOVIN_STATUS":
								$status_env = $row["value"];
								$country =  $row["value1"] == "" ? "CR" : $row["value1"];
							break;
							case "MOOVIN_CONTACT":
								$moovin_contact = $row["status"] == "1" ? true : false;
							break;
							case "MOOVIN_GOOGLE_MAP":
							case "MOOVIN_HERE_MAP":
								if($row["status"] == "1"){
									$credentials_google_maps = $row["status"] == "1" ? true : false;
								}
							break;
							
						}
				}

				if ($current_env != "off"){
					$response = $this->moovin_get_refresh_token();

					if($response["error"] == false){
			
						$get_url = $response["url"]."/rest/api/moovinEnterprise/partners/insideZoneCoverage?latitude=".$collect_location["value"]."&longitude=".$collect_location["value1"];
						
						$responseZones = wp_remote_post($get_url, array(
							'headers'     => array('Content-Type' => 'application/json; charset=utf-8' , 'token' =>  $response["token"]),
							'method'      => 'GET',
							'data_format' => 'body',
						));
	
						$zones = json_decode($responseZones["body"]);
	
						if($zones->status == "SUCCESS"){
							$collect_inside = true;
						}else{
							$collect_inside = false;
						}
					}
				}
				
			
			$bg_success = "bg-success";
			$bg_danger = "bg-danger";
			$ic_success = '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true"  width="24" height="24" focusable="false" data-prefix="fas" data-icon="check" class="svg-inline--fa fa-check fa-w-16" role="img" viewBox="0 0 512 512"><path fill="currentColor" d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"/></svg>';
			$ic_cancel = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" class="svg-inline--fa fa-times fa-w-11" role="img" viewBox="0 0 352 512"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>';

			$complete_install = '<div class="card">
								<h4 class="card-title mb-4">Completar instalación</h4>
								<div class="table-responsive">
									<table class="table table-nowrap align-middle table-hover mb-0">
										<tbody>
											<tr>
												<td style="width: 45px;">
													<div class="avatar-sm">
														<span class="avatar-title rounded-circle '.   ($credentials_sandbox || $credentials_sandbox ?  $bg_success : $bg_danger)  .' bg-soft text-primary font-size-24">
																'.   ($credentials_sandbox || $credentials_sandbox ?  $ic_success : $ic_cancel)  .'
														</span>
													</div>
												</td>
												<td>
													<h5 class="font-size-14 mb-1"><a href="javascript: page.goOption(2);" class="text-dark">Configurar Credenciales</a></h5>
													<small>Ingrese sus credenciales de usuario , si no las tiene aun solicite sus credenciales a ecommerce@moovin.me</small>

												</td>
												<td>
													<div class="text-center">
														<a href="javascript: page.goOption(2);" class="text-dark"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
					</a>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="avatar-sm">
														<span class="avatar-title rounded-circle '.   ($credentials_google_maps ?  $bg_success : $bg_danger)  .' bg-soft text-primary font-size-24">
															'.   ($credentials_google_maps ? $ic_success : $ic_cancel)  .'														
														</span>
													</div>
												</td>
												<td>
													<h5 class="font-size-14 mb-1"><a href="javascript: page.goOption(2);" class="text-dark">Configurar Proveedor de mapa</a></h5>
													<small>Seleccione su proveedor de mapas Google Map o Here Map</small>

												</td>
												<td>
													<div class="text-center">
														<a href="javascript: page.goOption(2);" class="text-dark"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
					</a>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="avatar-sm">
														<span class="avatar-title rounded-circle '.   ($moovin_contact ?  $bg_success : $bg_danger)  .' bg-soft text-primary font-size-24">
															'.   ($moovin_contact ?  $ic_success : $ic_cancel)  .'
														</span>
													</div>
												</td>
												<td>
													<h5 class="font-size-14 mb-1"><a href="javascript: page.goOption(3);" class="text-dark">Información de contacto</a></h5>
													<small>Ingrese el contacto para el punto de recolección de los paquetes</small>

												</td>
												<td>
													<div class="text-center">
														<a href="javascript: page.goOption(3);" class="text-dark"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
					</a>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="avatar-sm">
														<span class="avatar-title rounded-circle '.   ($default_location ?  $bg_success : $bg_danger)  .' bg-soft text-primary font-size-24">
														'.   ($default_location ?  $ic_success : $ic_cancel)  .'
														</span>
													</div>
												</td>
												<td>
													<h5 class="font-size-14"><a href="javascript: page.goOption(3);" class="text-dark">Ubicación de recolección</a></h5>
													<small>Seleccione el punto de recolección</small>

												</td>
												<td>
													<div class="text-center">
														<a href="javascript: page.goOption(3);" class="text-dark"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
					</a>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="avatar-sm">
														<span class="avatar-title rounded-circle '.   ($default_package_size  ?  $bg_success : $bg_danger)  .' bg-soft text-primary font-size-24">
														'.   ($default_package_size ?  $ic_success : $ic_cancel)  .'
														</span>
													</div>
												</td>
												<td>
													<h5 class="font-size-14"><a href="javascript: page.goOption(3);" class="text-dark">Tamaño del Paquete</a></h5>
													<small>Ingrese un tamaño de paquete por defecto</small>

												</td>
												<td>
													<div class="text-center">
														<a href="javascript: page.goOption(3);" class="text-dark"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
					</a>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div class="avatar-sm">
														<span class="avatar-title rounded-circle '.   ($status_env == "1" ?  $bg_success : $bg_danger)  .' bg-soft text-primary font-size-24">
														'.   ($status_env == "1" ?  $ic_success : $ic_cancel)  .'
														</span>
													</div>
												</td>
												<td>
													<h5 class="font-size-14"><a href="javascript: page.goOption(1);" class="text-dark">Activar Plugin</a></h5>
													<small>Seleccione el ambiente y active el plugin</small>
												</td>
												<td>
													<div class="text-center">
														<a href="javascript: page.goOption(1);" class="text-dark"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
					</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>';


				echo wp_json_encode(array("sandbox" => $credentials_sandbox, 
										"country" => $country,
										"prod" => $credentials_prod ,
										"googlemaps" => $credentials_google_maps ,
										"pckgsize" => $default_package_size  ,
										"pckgweight" => $default_package_weigth ,
										"complete_install" => $complete_install,
										"location" => $default_location ,
										"env" => $current_env ,
										"status_env" => $status_env,
										"collect_inside" =>$collect_inside,
										"error" => false,
										"url"=>$response["url"]));

				break;
				case "op_change_status":
					
					$current_env = "";
					$credentials_sandbox = false;
					$credentials_prod = false;
					$credentials_google_maps = false;
					$default_location = false;
					$default_package_size = false;
					$default_package_weigth = false;

					$parameters =  $wpdb->get_results(
												"SELECT * from " . $this->table_activator->moovin_tbl_parameters() , ARRAY_A);

					foreach($parameters as $row){
							switch($row["cod_parameter"]){
								case "MOOVIN_TOKEN_SANDBOX":
									if($row["value"] != ""){
										$credentials_sandbox =  true;
									}
								break;
								case "MOOVIN_TOKEN_PROD":
									if($row["value"] != ""){
										$credentials_prod =  true;
									}
								break;
								case "MOOVIN_PKG_SIZE":
									if($row["value"] != ""){
										$default_package_size =  true;
									}
								break;
								case "MOOVIN_PKG_WEIGHT":
									if($row["value"] != ""){
										$default_package_weigth =  true;
									}
								break;
								case "MOOVIN_DEFAULT_LOCATION": 
									if($row["value"] != "" && $row["value1"] != ""){
										$default_location =  true;
									}
								break;
								case "MOOVIN_CURRENT_ENV":
									if($row["value"] == ""){
										$current_env =  "off";
									}else{
										$current_env = $row["value"];
									}
									break;
								case "MOOVIN_GOOGLE_MAP":
								case "MOOVIN_HERE_MAP":
										if($row["status"] == "1"){
											$credentials_google_maps =  true;
										}
									break;
							}
					}

					if(sanitize_text_field($_POST["env"]) == "SANDBOX"){
						//Ambiente - SANDBOX
						if($credentials_sandbox && $credentials_google_maps && $default_location && $default_package_size && $default_package_weigth ){
							
							$updated = $wpdb->update(
								 $this->table_activator->moovin_tbl_parameters(),
								  array("value"=>"SANDBOX", "edited_at"=> date("Y-m-d H:m:s")), 
								  array("cod_parameter"=> "MOOVIN_CURRENT_ENV")
								);
							
							$updated = $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(),
									array("value"=>sanitize_text_field($_POST["status_env"]), "value1"=>sanitize_text_field($_POST["country"]), "status"=>sanitize_text_field($_POST["status_env"]),  "edited_at"=> date("Y-m-d H:m:s")), 
									array("cod_parameter"=> "MOOVIN_STATUS")
								);


							if ( false !== $updated ) {
								echo wp_json_encode(array(
									"error" => false , "message"=> "Solicitud completada!" ));
							} else {
								echo wp_json_encode(array(
									"error" => false , "message"=> "Error actualizando el ambiente, por favor intente denuevo " ));
							}
						}else{
							echo wp_json_encode(array(
								"error" => true , "message"=> "Debes de completar todas las configuraciónes para activar el ambiente!" ));
						}
					}else if(sanitize_text_field($_POST["env"]) == "PROD"){
						//Ambiente - PROD
						if($credentials_prod && $credentials_google_maps && $default_location && $default_package_size && $default_package_weigth ){
							
							$updated = $wpdb->update( 
								$this->table_activator->moovin_tbl_parameters(),
								 array("value"=>"PROD", "edited_at"=> date("Y-m-d H:m:s")),
								  array("cod_parameter"=> "MOOVIN_CURRENT_ENV")
								);

							$updated = $wpdb->update(
									$this->table_activator->moovin_tbl_parameters(),
										array("value"=>sanitize_text_field($_POST["status_env"]), "value1"=>sanitize_text_field($_POST["country"]), "status"=>sanitize_text_field($_POST["status_env"]),  "edited_at"=> date("Y-m-d H:m:s")), 
										array("cod_parameter"=> "MOOVIN_STATUS")
									);

							if ( false !== $updated ) {
								echo wp_json_encode(array(
									"error" => false , "message"=> "Solicitud completada!" ));
							} else {
								echo wp_json_encode(array(
									"error" => false , "message"=> "Error actualizando el ambiente, por favor intente denuevo" ));
							}
						}else{
							echo wp_json_encode(array(
								"error" => true , "message"=> "Debes de completar todas las configuraciónes para activar el ambiente!" ));
						}
					}else{
						echo wp_json_encode(array(
										"error" => true , "message"=> "No se encontro el ambiente seleccionado!" ));

					}

					break;
					case "op_save_credential_sandbox":

						try{
							$url_sandbox = "";
							$sandboxUrl =  $wpdb->get_results(
								"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_URL_SANDBOX' " , ARRAY_A);

							$moovinCountry =  $wpdb->get_results(
									"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_STATUS' " , ARRAY_A);
									
							//Detect country selected
							switch($moovinCountry[0]["value1"]){
								case "HN":
									$url_sandbox = $sandboxUrl[0]["value1"];
									break;
								default:
									$url_sandbox = $sandboxUrl[0]["value"];
									break;
							}

							if (count($sandboxUrl)>0){

								$post_url = $url_sandbox."/rest/api/moovinEnterprise/partners/login";
	
								
								$response = wp_remote_post($post_url, array(
									'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
									'body'        => json_encode(array('username' => sanitize_text_field($_POST["username"]), 'password' => sanitize_text_field($_POST["password"]) )),
									'method'      => 'POST',
									'data_format' => 'body',
								));
							
								$body = json_decode($response["body"]);

							
								if($body->status == "SUCCESS"){
									$tc = $this->moovin_get_exchange_values();

									$usernameUpdate =	$wpdb->update(
										$this->table_activator->moovin_tbl_parameters(), 
										array("value" => sanitize_text_field($_POST["username"]), "edited_at" => date_i18n("Y-m-d H:m:s")),
										array("cod_parameter" => "MOOVIN_USERNAME_SANDBOX")
									);

									$passwordUpdate =	$wpdb->update(
										$this->table_activator->moovin_tbl_parameters(), 
										array("value" => sanitize_text_field($_POST["password"]), "edited_at" => date_i18n("Y-m-d H:m:s")),
										array("cod_parameter" => "MOOVIN_PASSWORD_SANDBOX")
									);
							
									$tokenUpdate =	$wpdb->update(
										$this->table_activator->moovin_tbl_parameters(), 
										array("value" => $body->token, "value1" => $body->expirationDate , "value2"=> $tc , "edited_at" => date_i18n("Y-m-d H:m:s")),
										array("cod_parameter" => "MOOVIN_TOKEN_SANDBOX")
									);

									if ($usernameUpdate > 0 && $passwordUpdate > 0 && $tokenUpdate >0){
										echo wp_json_encode(array("error"=>false , "message" => "Credenciales actualizadas", "url"=>$url_sandbox));
									}else{
										echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando las credenciales", "url"=>$url_sandbox) );
									}
								}else{
									echo wp_json_encode(array("error"=>true , "message" => "Usuario o contraseña no valido", "url"=>$url_sandbox));
								}
							}else{
								echo wp_json_encode(array("error"=>true , "message" => "No se encontro URL sandbox configurado, por favor reinstale el plugin", "url"=>$url_sandbox));
							}
						
						}catch(Exeption $e){
							echo wp_json_encode(array("error"=>true , "message" => $e->getMessage()));
						}
				
					break;
					case "op_save_credential_prod":

						try{
							$url_prod = "";
							$prodUrl =  $wpdb->get_results(
									"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_URL_PROD' " , ARRAY_A);
							
						
							$moovinCountry =  $wpdb->get_results(
									"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_STATUS' " , ARRAY_A);
									
							//Detect country selected
							switch($moovinCountry[0]["value1"]){
								case "HN":
									$url_prod = $prodUrl[0]["value1"];
									break;
								default:
									$url_prod = $prodUrl[0]["value"];
									break;
							}

							if (count($prodUrl)>0){

								$post_url = $url_prod."/rest/api/moovinEnterprise/partners/login";						;
	
								$response = wp_remote_post($post_url, array(
									'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
									'body'        => json_encode(array('username' => sanitize_text_field($_POST["username"]), 'password' => sanitize_text_field($_POST["password"]))),
									'method'      => 'POST',
									'data_format' => 'body',
								));
							
								$body = json_decode($response["body"]);

								if($body->status == "SUCCESS"){
									$tc = $this->moovin_get_exchange_values();

									$usernameUpdate =	$wpdb->update(
										$this->table_activator->moovin_tbl_parameters(), 
										array("value" => sanitize_text_field($_POST["username"]), "edited_at" => date_i18n("Y-m-d H:m:s")),
										array("cod_parameter" => "MOOVIN_USERNAME_PROD")
									);

									$passwordUpdate =	$wpdb->update(
										$this->table_activator->moovin_tbl_parameters(), 
										array("value" => sanitize_text_field($_POST["password"]), "edited_at" => date_i18n("Y-m-d H:m:s")),
										array("cod_parameter" => "MOOVIN_PASSWORD_PROD")
									);

									$tokenUpdate =	$wpdb->update(
										$this->table_activator->moovin_tbl_parameters(), 
										array("value" => $body->token, "value1" => $body->expirationDate , "value2"=> $tc , "edited_at" => date_i18n("Y-m-d H:m:s")),
										array("cod_parameter" => "MOOVIN_TOKEN_PROD")
									);


									if ($usernameUpdate > 0 && $passwordUpdate > 0 && $tokenUpdate >0){
										echo wp_json_encode(array("error"=>false , "message" => "Credenciales actualizadas", "url"=>$url_prod));
									}else{
										echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando las credenciales", "url"=>$url_prod));
									}
								}else{
									echo wp_json_encode(array("error"=>true , "message" => "Usuario o contraseña no valido", "url"=>$url_prod));
								}
							}else{
								echo wp_json_encode(array("error"=>true , "message" => "No se encontro URL sandbox configurado, por favor reinstale el plugin", "url"=>$url_prod));
							}
						
						}catch(Exeption $e){
							echo wp_json_encode(array("error"=>true , "message" => $e->getMessage()));
						}
			
					break;

					case "op_load_all_credentials":

							$username_sandbox = "";
							$password_sandbox = "";
							$username_prod = "";
							$password_prod = "";
							
							$google_api_key = "";
							$google_zoom = "";
							$google_user_location = "";
							$google_status = "";


							$here_api_key =  "";
							$here_zoom = "";
							$here_user_location = "";	
							$here_status = "";	

							$current_env = "";

							$parameters =  $wpdb->get_results(
											"SELECT * from " . $this->table_activator->moovin_tbl_parameters() , ARRAY_A);
			
							foreach($parameters as $row){
									switch($row["cod_parameter"]){
										case "MOOVIN_USERNAME_SANDBOX":
											if($row["value"] != ""){
												$username_sandbox =  $row["value"];
											}
										break;
										case "MOOVIN_PASSWORD_SANDBOX":
											if($row["value"] != ""){
												$password_sandbox =  $row["value"];
											}
										break;
										case "MOOVIN_USERNAME_PROD":
											if($row["value"] != ""){
												$username_prod =  $row["value"];
											}
										break;
										case "MOOVIN_PASSWORD_PROD": 
											if($row["value"] != "" ){
												$password_prod =  $row["value"];
											}
										break;
										case "MOOVIN_ZOOM_LOCATION": 
											if($row["value"] != "" ){
												$google_zoom =  $row["value"];
											}
										break;
										case "MOOVIN_USER_LOCATION": 
											if($row["value"] != ""){
												$google_user_location =  $row["value"];
											}
										break;
										case "MOOVIN_CURRENT_ENV": 
											if($row["value"] != ""){
												$current_env =  $row["value"];
											}
										break;
										case "MOOVIN_GOOGLE_MAP":
												$google_api_key =  $row["value"];
												$google_zoom = $row["value1"];
												$google_user_location = $row["value2"];			
												$google_status = $row["status"];	

										break;
										case "MOOVIN_HERE_MAP":
												$here_api_key =  $row["value"];
												$here_zoom = $row["value1"];
												$here_user_location = $row["value2"];	
												$here_status = $row["status"];										
										
										break;
									}
							}	

				
							echo wp_json_encode(array("sandbox" => array("username"=> $username_sandbox , "password"=>$password_sandbox ), 
													"prod" =>array("username"=>$username_prod , "password"=>$password_prod) ,
													"googlemaps" => array("key"=> $google_api_key , "zoom"=>$google_zoom , "location"=>$google_user_location  , "status"=> $google_status) ,
													"heremaps" => array("key"=> $here_api_key , "zoom"=>$here_zoom , "location"=>$here_user_location , "status"=> $here_status) ,
													"env" => $current_env ,
													"error" => false));

					break;

					case "op_save_google_maps":

						$here_key = $wpdb->get_results(
							$wpdb->prepare(
									"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_HERE_MAP"
							), ARRAY_A
						);

						if ($here_key[0]["status"] == "0"){
							$keyGoogle =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field($_POST["googlekey"]), "value1" => sanitize_text_field($_POST["zoom"])  , "value2" => sanitize_text_field($_POST["location"]) , "status" => sanitize_text_field($_POST["status"]) , "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_GOOGLE_MAP")
							);
	
							if ($keyGoogle  > 0 ){
								echo wp_json_encode(array("error"=>false , "message" => "Configuración actualizada"));
							}else{
								echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando la configuración"));
							}
						}else{
							echo wp_json_encode(array("error"=>true , "message" => "No puedes activar los mapas de Google Maps si tienes activo los mapas de Here Maps"));
						}
						
				
					break;

					case "op_save_here_maps":

						$google_key = $wpdb->get_results(
							$wpdb->prepare(
									"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_GOOGLE_MAP"
							), ARRAY_A
						);

						if ($google_key[0]["status"] == "0"){

							$keyHere =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field($_POST["herekey"]), "value1" => sanitize_text_field($_POST["zoom"]) , "value2" => sanitize_text_field($_POST["location"]) , "status" => sanitize_text_field($_POST["status"]), "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_HERE_MAP")
							);

							if ($keyHere  > 0 ){
								echo wp_json_encode(array("error"=>false , "message" => "Configuración actualizada"));
							}else{
								echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando la configuración"));
							}
						}else{
							echo wp_json_encode(array("error"=>true , "message" => "No puedes activar los mapas de Here Maps si tienes activo los mapas de Google Maps"));
						}
					
					break;
					case "op_save_general":

						$general =  $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["autocollect"]),  "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_COLLECT_AUTO")
						);

						$express =  $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["expressservices"]), "value1" => sanitize_text_field($_POST["expressname"]),  "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_EXPRESS_SERVICE")
						);

						$route =  $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["routeservices"]), "value1" => sanitize_text_field($_POST["routesname"]),  "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_ROUTE_SERVICE")
						);

						$email_notification = $this->moovin_exist_db_parameter("MOOVIN_EMAIL_NOTIFICATION");

						if($email_notification){
							$email_notification =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field($_POST["emailnotification"]),  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_EMAIL_NOTIFICATION")
							);
						}else{
							$email_notification = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_EMAIL_NOTIFICATION', 
								"name"=> "Enviar notificacion a cliente",
								'value' => sanitize_text_field($_POST["emailnotification"]),
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}

				
						$express_schedule = $this->moovin_exist_db_parameter("MOOVIN_EXPRESS_SCHEDULE");

						if($express_schedule){
							$express_schedule =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field($_POST["inithour"]), "value1" => sanitize_text_field($_POST["finalhour"]),  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_EXPRESS_SCHEDULE")
							);
							
						}else{
							$express_schedule = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_EXPRESS_SCHEDULE', 
								"name"=> "Horario de servicio express",
								'value' => sanitize_text_field($_POST["inithour"]),
								'value1' => sanitize_text_field($_POST["finalhour"]),
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}

						$outzone = $this->moovin_exist_db_parameter("MOOVIN_OUTZONE");

						if($outzone){
							$outzone =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field($_POST["outzone"]),  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_OUTZONE")
							);
							
						}else{
							$outzone = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_OUTZONE', 
								"name"=> "Permitir ventas fuera de zona de covertura",
								'value' => sanitize_text_field($_POST["outzone"]),
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}


						$mode = $this->moovin_exist_db_parameter("MOOVIN_MODE");

						if($mode){
							$mode =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field($_POST["extend"]), "value1" => "",  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_MODE")
							);
							
						}else{
							$mode = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_MODE', 
								"name"=> "Modo de vizualicación de checkout",
								'value' => sanitize_text_field($_POST["extend"]),
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}


						$woocommerceZone = $this->moovin_exist_db_parameter("MOOVIN_WOOCOMMERCE_ZONE");

						if($woocommerceZone){
							$woocommerceZone =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field($_POST["woocommerce"]), "value1" => "",  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_WOOCOMMERCE_ZONE")
							);
							
						}else{
							$woocommerceZone = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_WOOCOMMERCE_ZONE', 
								"name"=> "Crear zonas moovin automaticamente en woocommerce",
								'value' => sanitize_text_field($_POST["woocommerce"]),
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}

						$paymentConfirm = $this->moovin_exist_db_parameter("MOOVIN_PAYMENT_METHOD");
						$_POST["payments"] = $_POST["payments"] == null ? array() : $_POST["payments"];
						if($paymentConfirm){
							$paymentConfirm =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field(implode(",", $_POST["payments"])), "value1" => "",  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_PAYMENT_METHOD")
							);
							
						}else{
							$paymentConfirm = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_PAYMENT_METHOD', 
								"name"=> "Métodos de pago que requieren de una confirmación de pago",
								'value' => sanitize_text_field(implode(",", $_POST["payments"])),
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}


						$paymentCard = $this->moovin_exist_db_parameter("MOOVIN_PAYMENT_CARD");
						$_POST["paymentscard"] = $_POST["paymentscard"] == null ? array() : $_POST["paymentscard"];
						if($paymentCard){
							$paymentCard =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field(implode(",", $_POST["paymentscard"])), "value1" => "",  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_PAYMENT_CARD")
							);
							
						}else{
							$paymentCard = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_PAYMENT_CARD', 
								"name"=> "Métodos de pago contra entrega con tarjeta",
								'value' => sanitize_text_field(implode(",", $_POST["paymentscard"])),
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}

						$paymentMoney = $this->moovin_exist_db_parameter("MOOVIN_PAYMENT_MONEY");
						$_POST["paymentsmoney"] = $_POST["paymentsmoney"] == null ? array() : $_POST["paymentsmoney"];
						if($paymentMoney){
							$paymentMoney =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => sanitize_text_field(implode(",", $_POST["paymentsmoney"])), "value1" => "",  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_PAYMENT_MONEY")
							);
							
						}else{
							$paymentMoney = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_PAYMENT_MONEY', 
								"name"=> "Métodos de pago contra entrega con efectivo",
								'value' => sanitize_text_field(implode(",", $_POST["paymentsmoney"])),
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}



						$moovinPromo = $this->moovin_exist_db_parameter("MOOVIN_PROMO");

						if($moovinPromo){
							$moovinPromo =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => $_POST["statuspromo"], "value1" => $_POST["amountpromo"],  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_PROMO")
							);
						}else{
							$moovinPromo = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_PROMO', 
								"name"=> "Promo envio express gratis",
								'value' => $_POST["statuspromo"],
								'value1' => $_POST["amountpromo"],
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}


						$moovinPromo = $this->moovin_exist_db_parameter("MOOVIN_PROMO_RUTA");

						if($moovinPromo){
							$moovinPromo =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => $_POST["statuspromoruta"], "value1" => $_POST["amountpromo"],  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_PROMO_RUTA")
							);
						}else{
							$moovinPromo = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_PROMO_RUTA', 
								"name"=> "Promo envio ruta gratis",
								'value' => $_POST["statuspromoruta"],
								'value1' => $_POST["amountpromo"],
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}


						$amountRound = $this->moovin_exist_db_parameter("MOOVIN_AMOUNT_ROUND");

						if($amountRound){
							$amountRound =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => $_POST["round"], "value1" => "",  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_AMOUNT_ROUND")
							);
						}else{
							$amountRound = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_AMOUNT_ROUND', 
								"name"=> "Redondear monto envío a decima mas cercana",
								'value' => $_POST["round"],
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}

						$amountAdd = $this->moovin_exist_db_parameter("MOOVIN_AMOUNT_ADD");

						if($amountAdd){
							$amountAdd =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => $_POST["amountadd"], "value1" => "",  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_AMOUNT_ADD")
							);
						}else{
							$amountAdd = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_AMOUNT_ADD', 
								"name"=> "Monto adicional a tarifa de envío",
								'value' => $_POST["amountadd"],
								'value1' => "",
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}


						$moovinTC = $this->moovin_exist_db_parameter("MOOVIN_TC");

						if($moovinTC){
							$amountAdd =  $wpdb->update(
								$this->table_activator->moovin_tbl_parameters(), 
								array("value" => $_POST["tcvalue"], "value1" => $_POST["tcauto"],  "edited_at" => date("Y-m-d H:m:s")),
								array("cod_parameter" => "MOOVIN_TC")
							);
						}else{
							$amountAdd = $wpdb->insert($this->table_activator->moovin_tbl_parameters(), array(
								'cod_parameter' => 'MOOVIN_TC', 
								"name"=> "Tipo de cambio",
								'value' => $_POST["tcvalue"],
								'value1' => $_POST["tcauto"],
								'value2' => "",
								"edited_at" => date("Y-m-d H:m:s"),
								"created_at" => date("Y-m-d H:m:s"),
								"status" => "1"
							));
						}


						define( 'MOOVIN_WOOCOMMERCE_AUTO', $_POST["woocommerce"]);

						//Notification Email Orders
						if (sanitize_text_field($_POST["emailnotification"]) == "1"){
							if ( ! wp_next_scheduled( 'isa_add_every_three_minutes' ) ) {
								wp_schedule_event( time(), 'every_three_minutes', 'isa_add_every_three_minutes' );
							}
						}

						//Force send notifications
						$this->every_three_minutes_event_func();
						
						if ($general  > 0 && $express > 0 && $route >0 && $email_notification > 0 && $express_schedule > 0 ){
							echo wp_json_encode(array("error"=>false , "message" => "Configuración actualizada"));
						}else{
							echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando la configuración"));
						}
					
					break;


					case "op_load_configurations":

						$data =  $wpdb->get_results(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE 1 " , ARRAY_A);
							
						$weight = null;
						$size = null;
						$locationDefault = null;
						$contact = null;
						$documentTask = 0;
						$collectAuto = 0;
						$fulfillment = 0;
						$routes = 0;
						$express = 0;
						$expressName = "MOOVIN Express 4H";
						$routesName = "MOOVIN 24H a 48H";
						$statusPromo = 0;
						$statusPromoRuta = 0;
						$amountPromo = 0;

						$round = "0";
						$amountadd= 0;

						$initdate = "";
						$finaldate = "";
						$emailnotification = "0";
						$mode = "simple";
						$outzone = "0";
						$woocommerce = "1";
						$paymentCodes = array();
						$paymentList = array();
						$paymentMoney = array();
						$paymentCard = array();

						$tcauto = "0";
						$tcvalue = "0";

						foreach(WC()->payment_gateways->payment_gateways() as $row){
							$payment = array("code"=> $row->id, "name"=>$row->method_title);
							array_push( $paymentList,$payment);
						}

						foreach($data as $row){
							switch($row["cod_parameter"]){
								case "MOOVIN_AMOUNT_ROUND":
									$round = $row["value"];
								break;
								case "MOOVIN_AMOUNT_ADD":
									$amountadd = $row["value"];
								break;
								case "MOOVIN_PAYMENT_MONEY":
									$paymentMoney = explode(",", $row["value"]);
								break;
								case "MOOVIN_PAYMENT_CARD":
									$paymentCard = explode(",", $row["value"]);
								break;
								case "MOOVIN_PAYMENT_METHOD":
									$paymentCodes = explode(",", $row["value"]);
								break;
								case "MOOVIN_EXPRESS_SCHEDULE":
									$initdate = $row["value"];
									$finaldate = $row["value1"];
								break;
								case "MOOVIN_EMAIL_NOTIFICATION":
									$emailnotification = $row["value"];
								break;
								case "MOOVIN_PKG_WEIGHT":
									if($row["value"] != ""){
										$weight =  $row["value"];
									}
								break;
								case "MOOVIN_PKG_SIZE":
									if($row["value"] != ""){
										$size =  $row["value"];
									}
								break;
								case "MOOVIN_DEFAULT_LOCATION":
									$locationDefault = $row;
								break;
								case "MOOVIN_CONTACT":
									$contact = $row;
								break;
								case "MOOVIN_TASK_DOCUMENT":
									if($row["value"] != ""){
										$documentTask =  $row["value"];
									}
								break;
								case "MOOVIN_COLLECT_AUTO":
									if($row["value"] != ""){
										$collectAuto =  $row["value"];
									}
								break;
								case "MOOVIN_FULFILLMENT":
									$fulfillment =  $row["value"];
									break;
								case "MOOVIN_EXPRESS_SERVICE":
									$express  = $row["value"];
									$expressName  = $row["value1"] == "" ? "MOOVIN Express 4H": $row["value1"];
										break;
								case "MOOVIN_ROUTE_SERVICE":
									$routes  = $row["value"];
									$routesName  = $row["value1"] == "" ? "MOOVIN 24H a 48H": $row["value1"];

								break;
								case "MOOVIN_OUTZONE":
									$outzone = $row["value"];
									break;
								case "MOOVIN_MODE":
									$mode  = $row["value"];
									break;
								case "MOOVIN_WOOCOMMERCE_ZONE":
									$woocommerce  = $row["value"];
									break;
								case "MOOVIN_PROMO":
									$statusPromo = $row["value"] == "" ? 0 : $row["value"];
									$amountPromo = $row["value1"] == "" ? 0 : $row["value1"];
									break;
								case "MOOVIN_PROMO_RUTA":
									$statusPromoRuta = $row["value"] == "" ? 0 : $row["value"];
									break;
								case "MOOVIN_TC":
									$tcvalue = $row["value"] == "" ? 0 : $row["value"];
									$tcauto = $row["value1"] == "" ? 0 : $row["value1"];
								break;
	
							}
						}

						// define( 'MOOVIN_WOOCOMMERCE_AUTO', $woocommerce );

						echo wp_json_encode(array("error"=>false , 
												"location" =>array("lat"=> $locationDefault["value"] ,  "lng"=> $locationDefault["value1"]) ,
												"package" =>array("weight"=> $weight , "size"=> $size ) ,
												"contact"=> $contact ,
												"documentTask"=> $documentTask,
												"fulfillment"=>$fulfillment,
												"collectAuto"=>$collectAuto,
												"express"=>$express,
												"expressname"=>$expressName,
												"routes"=>$routes ,
												"routesname"=>$routesName,
												"emailnotification" => $emailnotification ,
												"initdate" => $initdate,
												"finaldate" =>$finaldate,
												"outzone" => $outzone,
												"mode" => $mode ,
												"woocommerce"=> $woocommerce,
												"paymentcodes" => $paymentCodes,
												"payments" => $paymentList,
												"statusPromo" => $statusPromo,
												"statusPromoRuta" => $statusPromoRuta,
												"amountPromo" => $amountPromo,
												"amountadd"=>$amountadd,
												"round"=>$round,
												"paymentMoney" => $paymentMoney,
												"paymentCard" => $paymentCard,
												"tcvalue" => $tcvalue,
												"tcauto" => $tcauto,
												)   
											);
	

					break;

					case "op_save_default_location":

						$keyUpdate = $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["lat"]), "value1"=> sanitize_text_field($_POST["lng"]), "edited_at" => date("Y-m-d H:m:s") , "name"=> sanitize_text_field($_POST["name"])),
							array("cod_parameter" => "MOOVIN_DEFAULT_LOCATION")
						);


						$keyFulfillment = $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["fulfillment"]),  "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_FULFILLMENT")
						);

						if ($keyUpdate > 0 && $keyFulfillment > 0){
							echo wp_json_encode(array("error"=>false , "message" => "Ubicación actualizada"));
						}else{
							echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando la configuración"));
						}

					break;
					
					case "op_save_task":
						$keyUpdate = $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["document"]) , "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_TASK_DOCUMENT")
						);

						if ($keyUpdate > 0){
							echo wp_json_encode(array("error"=>false , "message" => "Tareas actualizadas"));
						}else{
							echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando las tareas"));
						}

						break;
					case "op_save_contact":
						
						$keyUpdate = $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array( "name"=> sanitize_text_field($_POST["name"]), "value" => sanitize_text_field($_POST["phone"]), "value1"=> sanitize_textarea_field($_POST["notes"]), "value2"=> sanitize_email($_POST["email"]), "status"=> "1", "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_CONTACT")
						);

						if ($keyUpdate > 0){
							echo wp_json_encode(array("error"=>false , "message" => "Contacto actualizado"));
						}else{
							echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando información de contacto"));
						}

						break;

					case "op_save_default_package":

						$keyUpdate = $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["size"]), "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_PKG_SIZE")
						);

						$keyUpdate = $wpdb->update(
							$this->table_activator->moovin_tbl_parameters(), 
							array("value" => sanitize_text_field($_POST["weight"]),  "edited_at" => date("Y-m-d H:m:s")),
							array("cod_parameter" => "MOOVIN_PKG_WEIGHT")
						);

						if ($keyUpdate > 0){
							echo wp_json_encode(array("error"=>false , "message" => "Paquete actualizado"));
						}else{
							echo wp_json_encode(array("error"=>true , "message" => "Ocurrio un error actualizando la configuración"));
						}


						break;
					case "op_load_default_package":

						$package =  $wpdb->get_results(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_PKG_WEIGHT' OR  cod_parameter = 'MOOVIN_PKG_SIZE'" , ARRAY_A);
						
							foreach($package as $row){
								switch($row["cod_parameter"]){
									case "MOOVIN_PKG_WEIGHT":
										if($row["value"] != ""){
											$weight =  $row["value"];
										}
									break;
									case "MOOVIN_PKG_SIZE":
										if($row["value"] != ""){
											$size =  $row["value"];
										}
									break;

								}
							}

						echo wp_json_encode(array("error"=>false , "package" =>array("weight"=> $weight , "size"=> $size)));
						
					break;
					case "op_datatable_order":
							//Check pending notifications
							if ( ! wp_next_scheduled( 'isa_add_every_three_minutes' ) ) {
								wp_schedule_event( time(), 'every_three_minutes', 'isa_add_every_three_minutes' );
							}

							$orders =  $wpdb->get_results(
							"SELECT " . $this->table_activator->moovin_tbl_orders() .".*  , ".$wpdb->prefix."postmeta.meta_value as currency from " . $this->table_activator->moovin_tbl_orders() ."  LEFT JOIN ".$wpdb->prefix."postmeta ON  " . $this->table_activator->moovin_tbl_orders() .".order_id  = ".$wpdb->prefix."postmeta.post_id AND meta_key = '_order_currency' ORDER BY date_created DESC" , ARRAY_A);
						
							echo wp_json_encode(array("data"=>$orders));

						break;
					case "op_datatable_products":

							$products =  $wpdb->get_results(
								"SELECT * from " . $this->table_activator->moovin_tbl_order_products() ." WHERE `order_id` =  ".sanitize_text_field($_POST["order_id"]), ARRAY_A);
							
							
							echo wp_json_encode(array("data"=>$products ));
						break;
					case "op_order_cancel":

						$response = $this->moovin_get_refresh_token();

						if($response["error"] == false){

							$request = array(
								"idPackage"=>sanitize_text_field($_POST["id_package"])
							);
					
							$post_url = $response["url"]."/rest/api/ecommerceExternal/deleteOrder";
							$responseOrder = wp_remote_post($post_url, array(
								'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'token' =>  $response["token"]),
								'method'      => 'POST',
								'data_format' => 'body',
								'body' => json_encode($request)
							));
				
							$cancelOrder = json_decode($responseOrder["body"],true);

							if($cancelOrder["status"] == "SUCCESS"){
								$date =  date("Y-m-d H:i:s");
								$order =	$wpdb->update(
									$this->table_activator->moovin_tbl_orders(), 
									array("status_order_delivery_moovin" => "CANCEL", "date_order_ready" => $date , "response_order_ready" => json_encode($cancelOrder), "date_update_moovin"=> $date),
									array("order_id" => sanitize_text_field($_POST["id_order"]))
								);

								echo wp_json_encode(array("error"=>false , "message" => "La orden ha sido cancelada correctamente!" ));
						
													
							}else if($cancelOrder["status"] == "PERMISSIONDENIED"){
								echo wp_json_encode(array("error"=>true , "message" => "El paquete que intenta cancelar no es permitido!"));
							}else if($cancelOrder["status"]  == "DUPLICATE"){
								echo wp_json_encode(array("error"=>true , "message" =>"El paquete ya se encuentra cancelado" ));
							}else if($cancelOrder["status"]  == "NOPERMIT"){
								echo wp_json_encode(array("error"=>true, "message" => "No se permite cancelar el paquete!"));
							}else{
								echo wp_json_encode(array("error"=>true, "message" => "Error de comunicación por favor intente de nuevo, si el error persiste por favor comunicarse con soporte Moovin."));
							}
						}else{
							echo wp_json_encode(array("error"=>true , "Error de credenciales moovin"));
						}



						break;
					case "op_order_ready":

						$response = $this->moovin_get_refresh_token();

						if($response["error"] == false){

							$request = array(
								"idPackage"=>sanitize_text_field($_POST["id_package"])
							);
					
							$post_url = $response["url"]."/rest/api/ecommerceExternal/completeOrder";
							$responseOrder = wp_remote_post($post_url, array(
								'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'token' =>  $response["token"]),
								'method'      => 'POST',
								'data_format' => 'body',
								'body' => json_encode($request)
							));
				
							$completeOrder = json_decode($responseOrder["body"],true);

							if($completeOrder["status"] == "SUCCESS"){
								$date =  date("Y-m-d H:i:s");
								$order =	$wpdb->update(
									$this->table_activator->moovin_tbl_orders(), 
									array("status_order_delivery_moovin" => "READY", "date_order_ready" => $date , "response_order_ready" => json_encode($completeOrder), "date_update_moovin"=> $date),
									array("order_id" => sanitize_text_field($_POST["id_order"]))
								);

								$this->moovin_email_notification(sanitize_text_field($_POST["id_order"]), sanitize_text_field($_POST["email"]));

								echo wp_json_encode(array("error"=>false , "message" => "Solicitud de recolección completada , un mensajero de moovin recogera su paquete, por favor recuerde tener listo el paquete" ));
						
							}else if($completeOrder["status"] == "ERRORPARAMETERSCHANGE"){
								echo wp_json_encode(array("error"=>true , "message" => "Error cambio no permitido" ));
							}else if($completeOrder["status"] == "PROFILEISBLOCKED"){
								echo wp_json_encode(array("error"=>true , "message" =>"Su cuenta esta bloqueada por facturas pendientes y no podrá solicitar entregas de paquetes hasta que sean canceladas." ));
							}else if($completeOrder["status"] == "ERRORINTERNAL"){
								echo wp_json_encode(array("error"=>true, "message" => "Error de comunicación por favor intente de nuevo, si el error persiste por favor comunicarse con soporte Moovin - Cod.[ERRORINTERNAL]."));
							}else{
								echo wp_json_encode(array("error"=>true, "message" => "Error de comunicación por favor intente de nuevo, si el error persiste por favor comunicarse con soporte Moovin - Cod.[".$completeOrder["status"]."]"));
							}

						}else{
							echo wp_json_encode(array("error"=>true , "Error de credenciales moovin"));
						}

						break;

					case "op_check_status":

						$response = $this->moovin_get_refresh_token();

						if($response["error"] == false){

							$get_url = $response["url"]."/rest/api/moovinEnterprise/partners/deliveredPackage?idPackage=".sanitize_text_field($_POST["id_package"]);
							$responseOrder = wp_remote_post($get_url, array(
								'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'token' =>  $response["token"]),
								'method'      => 'GET'
							));
				
							$statusOrder = json_decode($responseOrder["body"]);

							if($statusOrder->status == "SUCCESS"){
								//{
								//   "statusPackage": "DELIVERED",
								//   "date": "2018-08-23T11:59:28Z",
								//   "status": "SUCCESS",
								//   "message": "Complete"
								// }
								$statusPackage = "";
								switch ($statusOrder->statusPackage) {
									case "INMOOVIN":
										$statusPackage = "En moovin";
										break;
									case "ASSIGNDRAWER":
										$statusPackage = "Paquete agregado";
										break;
									case "COORDINATE":
										$statusPackage = "Coordinado";
										break;
									case "DELIVERED":
										$statusPackage = "Entrega completa";
										break;
									case "COLLECTPICKUP":
										$statusPackage = "Paquete recogido";
										break;
									case "FAILDELIVERY":
										$statusPackage = "Entrega fallida";
										break;
									case "DELETEPACKAGE":
										$statusPackage = "Entrega eliminado";
										break;
									case "CANCEL":
										$statusPackage = "Cancelada";
										break;
									case "CANCELREQUEST":
										$statusPackage = "Cancelada";
										break;
									case "DEFAULT":
										$statusPackage = "Por defecto";
										break;
									case "INROUTE":
										$statusPackage = "En ruta";
										break;
									case "INSERVICE":
										$statusPackage = "En Servicio";
										break;
									case "INROUTEPICKUP":
										$statusPackage = "En ruta por recoger";
										break;
									case "ASSINGROUTE":
										$statusPackage = "Asignado a ruta";
										break;
									case "UNDELIVERED":
										$statusPackage = "Sin entregar";
										break;
									default:
										$statusPackage = $statusOrder->statusPackage;
								}

								echo wp_json_encode(array("error"=>false, "estado" => $statusPackage , "message" => "El estado del paquete es [".$statusPackage."]   ".$statusOrder->date ));

							}else{
								echo wp_json_encode(array("error"=>true, "message" => "Error de comunicación por favor intente de nuevo, si el error persiste por favor comunicarse con soporte Moovin."));
							}
						}else{
							echo wp_json_encode(array("error"=>true , "Error de credenciales moovin"));
						}

						
						break;
					case "op_load_zones":

						$env =  $wpdb->get_results(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_CURRENT_ENV' " , ARRAY_A);

							if ($env[0]["value"] != ""){
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
									echo wp_json_encode(array("error"=>true , "message" => "Usuario o contraseña no valido"));
								}
							}else{
								echo wp_json_encode(array("error"=>true , "message" => "Debes activar un ambiente para visualizar la zona de cobertura"));
							}
						
					break;
					case "op_order_confirmation":
					case "op_order_create":

						$order = $wpdb->get_results(
							$wpdb->prepare(
									"SELECT * from " . $this->table_activator->moovin_tbl_orders() . " WHERE order_id = %s ", sanitize_text_field($_POST["id_order"])
							), ARRAY_A
						);

						if(count($order) == 0){
							echo wp_json_encode(array("error"=>true , "message" => "No se encontro el encabezado de la orden"));
							wp_die(); 
						}

						$products = $wpdb->get_results(
							$wpdb->prepare(
									"SELECT * from " . $this->table_activator->moovin_tbl_order_products() . " WHERE order_id = %s ", sanitize_text_field($_POST["id_order"])
							), ARRAY_A
						);

						if(count($products) == 0){
							echo wp_json_encode(array("error"=>true , "message" => "No se encontraron productos en la orden"));
							wp_die(); 
						}

						$package =  $wpdb->get_results(
							"SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter in  ('MOOVIN_PKG_SIZE') " , ARRAY_A);		
		

						$response = $this->moovin_get_refresh_token();

						if($response["error"] == false){

							$listProduct =  array();

							$pointCollect = array(
								"latitude" => $order[0]["latitude_collect"],
								"longitude" => $order[0]["longitude_collect"],
								"locationAlias" => $order[0]["location_alias_collect"],
								"name" => $order[0]["contact_collect"],
								"phone" => $order[0]["phone_collect"],
								"notes" => $order[0]["notes_collect"]
							);
				
							//Check if need payment in site;
							$paymentSite =	explode('#', $order[0]["shipping_method"]);

							$paymentTask = array();
							if(count($paymentSite) > 1){
								if($paymentSite[1] != "none"){
									$currencyInSite = "";
									
									if(get_woocommerce_currency() == "USD"){
										$currencyInSite = "dolares";
									}elseif(get_woocommerce_currency() == "EUR"){
										$currencyInSite = "euros";
									}elseif(get_woocommerce_currency() == "CRC"){
										$currencyInSite = "colones";
									}elseif(get_woocommerce_currency() == "HNL"){
										$currencyInSite = "lempira";
									}
									
									if($paymentSite[1] == "card"){
										$paymentTask = array(
												array("description"=>"Total de cobro",
													"amount"=>($order[0]["total_sales"]+$order[0]["tax_total"]+$order[0]["shipping_total"]),
													"currency"=> $currencyInSite,
													"method"=>"creditCard")
												);
									}else if($paymentSite[1] == "money"){
										$paymentTask = array(
												array("description"=>"Total de cobro",
													"amount"=>($order[0]["total_sales"]+$order[0]["tax_total"]+$order[0]["shipping_total"]),
													"currency"=>$currencyInSite,
													"method"=>"cash")
												);
									}
								}
							}

							$pointDelivery = array(
								"latitude"=> $order[0]["latitude_delivery"],
								"longitude"=> $order[0]["longitude_delivery"],
								"locationAlias"=> $order[0]["location_alias_delivery"] ,
								"name"=> $order[0]["contact_delivery"],
								"phone"=> $order[0]["phone_delivery"],
								"notes"=> $order[0]["notes_delivery"],
								"documents" => $this->moovin_get_documents() ,
								"listPayment"=> $paymentTask
							);
				
							foreach($products as $rowProducts) { 
							
								$size =  $wpdb->get_results(
									"SELECT * from " . $this->table_activator->moovin_tbl_pkgs_sizes() ." WHERE length_cm >= ".$rowProducts["length"]."  AND  width_cm >= ".$rowProducts["width"]." AND high_cm >= ".$rowProducts["high"]." AND weight_kg >= ".$rowProducts["weight"] ." ORDER BY id_pkgs_size ASC LIMIT 1" , ARRAY_A);

								$product = array(
									"quantity"=> $rowProducts["quantity"],
									"nameProduct"=> $rowProducts["name_product"],
									"description"=> $rowProducts["description"],
									"size"=> isset($size[0]["name"]) ? $size[0]["name"] : $package[0]["value"],
									"weight"=> $rowProducts["weight"],
									"price"=> $rowProducts["price"],
									"codeProduct"=> $rowProducts["code_product"]
								);

								array_push($listProduct , $product);
							} 

							if($order[0]["fulfillment"] ==" 0"){
								//Request create order - regular
								$requestCreateOrder = array(
									"idEstimation" => $order[0]["id_estimation"],
									"idDelivery" => $order[0]["id_delivery"],
									"idOrder" => sanitize_text_field($_POST["id_order"]),
									"email" => $order[0]["email"],
									"emailAccount" => $order[0]["email"],
									"prepared" => false,
									"ensure" => true,
									"pointCollect" => $pointCollect,
									"pointDelivery" => $pointDelivery,
									"listProduct"=> $listProduct
									);
							}else{
									//Request create order - fulfillment
									$requestCreateOrder = array(
										"idEstimation" => $order[0]["id_estimation"],
										"idDelivery" => $order[0]["id_delivery"],
										"idOrder" => sanitize_text_field($_POST["id_order"]),
										"email" => $order[0]["email"],
										"emailAccount" => $order[0]["email"],
										"prepared" => false,
										"ensure" => true,
										"cediMoovin" => true,
										"pointDelivery" => $pointDelivery,
										"listProduct"=> $listProduct
									);
							}

						
							// Send request create order
							$post_url = $response["url"]."/rest/api/ecommerceExternal/createOrder";

							$responseCreateOrder = wp_remote_post($post_url, array(
								'headers'     => array('Content-Type' => 'application/json; charset=utf-8' , 'token' =>  $response["token"]),
								'method'      => 'POST',
								'data_format' => 'body',
								'body' => json_encode($requestCreateOrder)
							));
				
							$createOrder = json_decode($responseCreateOrder["body"]);

							//Order created 
							if($createOrder->status == "SUCCESS" || $createOrder->idPackage  > 0){

								$order_status =  $wpdb->update(
									$this->table_activator->moovin_tbl_orders(), 
									array(
									"status_order_delivery_moovin" => "CREATED",
									"id_package_moovin" => $createOrder->idPackage, 
									"qr_code" => $createOrder->orderQR,
									"response_order_created" => json_encode($responseCreateOrder["body"])),
									array("order_id" => $order[0]["order_id"])
								);

								// Add custom fields to order woocommerce
								update_post_meta( $order[0]["order_id"], 'delivery_latitude', $order[0]["latitude_delivery"]);
								update_post_meta( $order[0]["order_id"], 'delivery_longitude', $order[0]["longitude_delivery"]);
								update_post_meta( $order[0]["order_id"], 'delivery_address', $order[0]["location_alias_delivery"]);
								update_post_meta( $order[0]["order_id"], 'estimation_id', $order[0]["id_estimation"]);
								update_post_meta( $order[0]["order_id"], 'package_id', $createOrder->idPackage);
								update_post_meta( $order[0]["order_id"], 'moovin_qr', $createOrder->orderQR);

								//Check if autocollect is enabled
								$autocollect = $wpdb->get_results(
									$wpdb->prepare(
											"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_COLLECT_AUTO"
									), ARRAY_A
								);

								if ($autocollect[0]["value"] == "1" && $order[0]["shipping_method"] == "route" ){
									$requestCollect = array(
										"idPackage"=>$createOrder->idPackage
									);
							
									$post_url = $response["url"]."/rest/api/ecommerceExternal/completeOrder";
									$responseOrder = wp_remote_post($post_url, array(
										'headers'     => array('Content-Type' => 'application/json; charset=utf-8', 'token' =>  $response["token"]),
										'method'      => 'POST',
										'data_format' => 'body',
										'body' => json_encode($requestCollect)
									));
						
									$completeOrder = json_decode($responseOrder["body"],true);

									if($completeOrder["status"] == "SUCCESS"){
										//Register email notification
										$this->moovin_email_notification($order_id, $order[0]["email_account"]);

										$date =  date_i18n("Y-m-d H:i:s");
										$order =	$wpdb->update(
											$this->table_activator->moovin_tbl_orders(), 
											array("status_order_delivery_moovin" => "READY", "date_order_ready" => $date , "response_order_ready" => json_encode($responseOrder["body"]), "date_update_moovin"=> $date),
											array("order_id" => $order_id )
										);
									}
								}

								echo wp_json_encode(array("error"=>false ,"message"=> "Orden de recolección creada correctamente"));

							}else{
								echo wp_json_encode(array("error"=>true , "message"=> "[".$createOrder->status."]".$createOrder->message));
							}
						}else{
							echo wp_json_encode(array("error"=>true , "message"=>"Error de credenciales moovin"));
						}

					break;
		}


		wp_die(); 
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

	function add_every_three_minutes(){
			$schedules['every_three_minutes'] = array(
				'interval'  => 180,
				'display'   => __( 'Every 3 Minutes', 'textdomain' )
		);
		return $schedules;
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

	function moovin_exist_db_parameter($cod_parameter){
		global $wpdb;
		$parameter = $wpdb->get_results(
			$wpdb->prepare(
					"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", $cod_parameter
			), ARRAY_A
		);
		
		if (count($parameter) > 0){
			return true;
		}else{
			return false;
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
	

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

    	$valid_pages = array("moovin-menu" , "moovin-menu-ordenes", "moovin-menu-conf");

		$page = isset($_REQUEST["page"]) ? sanitize_text_field($_REQUEST["page"]) : "";
		if (in_array($page, $valid_pages)){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/moovin-delivery-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( "moovin-app", plugin_dir_url( __FILE__ ) . 'css/app.css', array(), $this->version, 'all' );
			wp_enqueue_style( "moovin-bootstrap", plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "moovin-dt-buttons", plugin_dir_url( __FILE__ ) . 'css/buttons.bootstrap4.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "moovin-dt-bootstrap", plugin_dir_url( __FILE__ ) . 'css/dataTables.bootstrap4.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "moovin-icons", plugin_dir_url( __FILE__ ) . 'css/icons.css', array(), $this->version, 'all' );
			wp_enqueue_style( "moovin-sweetalert", plugin_dir_url( __FILE__ ) . 'css/sweetalert2.min.css', array(), $this->version, 'all' );	
			wp_enqueue_style( "moovin-print-qr", plugin_dir_url( __FILE__ ) . 'css/moovin-print-qr.css', array(), $this->version, 'all' );	
			wp_enqueue_style( "moovin-select2", plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );	

		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $wpdb;

		$google_key = $wpdb->get_results(
			$wpdb->prepare(
					"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_GOOGLE_MAP"
			), ARRAY_A
		);

		$here_key = $wpdb->get_results(
			$wpdb->prepare(
					"SELECT * from " . $this->table_activator->moovin_tbl_parameters() . " WHERE cod_parameter = %s ", "MOOVIN_HERE_MAP"
			), ARRAY_A
		);


		$page = isset($_REQUEST["page"]) ? sanitize_text_field($_REQUEST["page"]) : "";
		switch($page){
				case "moovin-menu-conf" : 

					wp_enqueue_script( "moovin-maps", "https://maps.google.com/maps/api/js?key=".$google_key[0]["value"], $this->version, false );
					wp_enqueue_script( "moovin-bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-blockUI", plugin_dir_url( __FILE__ ) . 'js/jquery.blockUI.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-dt-jquery", plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-dt-bootstrap", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-sweetalert2", plugin_dir_url( __FILE__ ) . 'js/sweetalert2.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-simplebar", plugin_dir_url( __FILE__ ) . 'js/simplebar.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-utility", plugin_dir_url( __FILE__ ) . 'js/utility.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-global", plugin_dir_url( __FILE__ ) . 'js/global.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/partial/config.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-location-picker", plugin_dir_url( __FILE__ ) . 'js/locationpicker.jquery.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-select2", plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

					wp_localize_script($this->plugin_name, "initMoovin", array(
						"ajaxurl" => admin_url("admin-ajax.php"),
					));

				break;
				case "moovin-menu" : 

					wp_enqueue_script( "moovin-bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-blockUI", plugin_dir_url( __FILE__ ) . 'js/jquery.blockUI.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-dt-jquery", plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-dt-bootstrap", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-sweetalert2", plugin_dir_url( __FILE__ ) . 'js/sweetalert2.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-simplebar", plugin_dir_url( __FILE__ ) . 'js/simplebar.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-utility", plugin_dir_url( __FILE__ ) . 'js/utility.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-global", plugin_dir_url( __FILE__ ) . 'js/global.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/partial/tracking.js', array( 'jquery' ), $this->version, false );

				
					wp_localize_script($this->plugin_name, "initMoovin", array(
						"ajaxurl" => admin_url("admin-ajax.php"),
					));
				break;
				case "moovin-menu-ordenes" : 


					wp_enqueue_script( "moovin-maps", "https://maps.google.com/maps/api/js?key=".$google_key[0]["value"], $this->version, false );
					wp_enqueue_script( "moovin-bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-blockUI", plugin_dir_url( __FILE__ ) . 'js/jquery.blockUI.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-dt-jquery", plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-dt-bootstrap", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-sweetalert2", plugin_dir_url( __FILE__ ) . 'js/sweetalert2.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-simplebar", plugin_dir_url( __FILE__ ) . 'js/simplebar.min.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-utility", plugin_dir_url( __FILE__ ) . 'js/utility.js', array( 'jquery' ), $this->version, false );
					wp_enqueue_script( "moovin-global", plugin_dir_url( __FILE__ ) . 'js/global.js', array( 'jquery' ), $this->version, false );

					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/partial/order.js', array( 'jquery' ), $this->version, false );

					wp_localize_script($this->plugin_name, "initMoovin", array(
						"ajaxurl" => admin_url("admin-ajax.php"),
					));

					break;
		}
		
	}

}
