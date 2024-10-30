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

$default_address_type_text = (get_option('sg_del_address_card_title') !== '') ? get_option('sg_del_address_card_title', 'Desconocida') : 'Desconocida';
$default_address_btn_text = (get_option('sg_del_address_card_btn_text') !== '') ? get_option('sg_del_address_card_btn_text', 'Entregar Aqui') : 'Entregar Aqui';
$add_new_card_btn_text = (get_option('sg_del_add_new_address_card_btn_text') !== '') ? get_option('sg_del_add_new_address_card_btn_text', 'Agregar') : 'Agregar';
$add_new_form_title = (get_option('sg_del_add_new_address_form_title') !== '') ? get_option('sg_del_add_new_address_form_title', 'Guardar Direccion') : 'Guardar Direccion';
$add_new_form_submit_btn_text = (get_option('sg_del_add_new_address_form_btn_text') !== '') ? get_option('sg_del_add_new_address_form_btn_text', 'Guardar y continuar') : 'Guardar y continuar';
$custom_type_placeholder = (get_option('sg_del_address_type_placeholder') !== '') ? get_option('sg_del_address_type_placeholder', 'Casa Familiar') : 'Dad’s home, my man cave';
$address_card_title = (get_option('sg_del_add_title') !== '') ? get_option('sg_del_add_title', 'Direccion') : 'Direccion ';

global $wpdb;

$outzone =  $wpdb->get_results(
    "SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_OUTZONE'" , ARRAY_A);
    
$country =  $wpdb->get_results(
        "SELECT * from " . $this->table_activator->moovin_tbl_parameters() ." WHERE cod_parameter = 'MOOVIN_COUNTRY'" , ARRAY_A);
        
?>

<div class="sg-del-add-hidden-fields">
    <input type="hidden" name="sg_moovin_country" id="sg_moovin_country" value="<?php echo isset($country[0]["value"]) ?  $country[0]["value"] : "CR" ;?>">
    <input type="hidden" name="sg_moovin_outzone" id="sg_moovin_outzone" value="<?php echo isset($outzone[0]["value"]) ?  $outzone[0]["value"] : "0" ;?>">
    <input type="hidden" name="sg_del_add_map_user_locate_icon" id="sg_del_add_map_user_locate_icon" value="<?php echo esc_attr(plugin_dir_url(__DIR__) . 'img/icons/location-finder-grey.png'); ?>">
    <input type="hidden" name="sg_del_add_map_default_lat" id="sg_del_add_map_default_lat" value="<?php echo esc_attr((get_option('sg_del_default_lat') !== '') ? get_option('sg_del_default_lat', 9.93600) : 9.93600); ?>">
    <input type="hidden" name="sg_del_add_map_default_lng" id="sg_del_add_map_default_lng" value="<?php echo esc_attr((get_option('sg_del_default_long') !== '') ? get_option('sg_del_default_long', -84.10401) : -84.10401); ?>">
    <input type="hidden" name="sg_del_add_auto_detect_user_location" id="sg_del_add_auto_detect_user_location" value="<?php echo esc_attr(get_option('sg_del_allow_user_location', false)); ?>">
    <input type="hidden" name="sg_del_add_default_address_id" id="sg_del_add_default_address_id" value="<?php echo esc_attr($default_address_id); ?>">
    <input type="hidden" name="sg_del_add_ajax_url" id="sg_del_add_ajax_url" value="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>">
    <input type="hidden" name="sg_del_add_status" id="sg_del_add_status" value="<?php echo esc_attr(get_option('sg_del_enable_address_picker', 'disable')); ?>">
    <input type="hidden" name="sg_del_add_unnamed_error_status" id="sg_del_add_unnamed_error_status" value="<?php echo esc_attr(get_option('sg_del_add_enable_unnamed_error', 'no')); ?>">
    <input type="hidden" name="sg_del_add_unnamed_error" id="sg_del_add_unnamed_error" value="<?php echo esc_attr(get_option('sg_del_add_unnamed_error', 'Location is not specified')); ?>">
    <input type="hidden" name="sg_del_add_title_error" id="sg_del_add_title_error" value="<?php echo esc_attr(get_option('sg_del_add_title_error', 'Location is not specified')); ?>">
    <input type="hidden" name="sg_del_add_default_title" id="sg_del_add_default_title" value="<?php echo esc_attr(get_option('sg_del_address_card_title') !=='' ? get_option('sg_del_address_card_title', 'Unknown') : 'Unknown'); ?>">
</div>


<?php
if (get_option('sg_del_address_cards_column') && get_option('sg_del_address_cards_column') !== '') {
    $cards_per_col = get_option('sg_del_address_cards_column', 2);
?>
    <style>
        .addresses-section {
            justify-content: flex-start;
        }

        .addresses-section .single-address.address-inline {

            width: calc(<?php echo esc_html(($cards_per_col === '1') ? "100%" :  "100% /" . $cards_per_col . " - 10px"); ?>);
            margin-right: 2%;
        }

        <?php if ($cards_per_col && $cards_per_col > 3) {

        ?>.addresses-section .single-address .sg-button {
            display: block;
        }

        <?php } 
        ?>.addresses-section .single-address.address-inline:nth-child(<?php echo esc_html($cards_per_col . 'n'); ?>) {
            margin-right: 0px;
        }

        <?php
        echo esc_html(get_option('sg_del_address_custom_styles'));
    }
        ?>
    </style>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="section sg-del-add-section moovin-map">
    <div class="container sg-del-add-container-outer <?php echo esc_attr('sg-del-add-' . $section . '-container'); ?>">
        <div class="addresses-section sg-del-address sg-del-address-list <?php echo wp_is_mobile() ? esc_attr('mobile-cards') : esc_attr('web-cards'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_list'); ?>">
            <h4 class="sg-del-add-title"><?php esc_html_e('Seleccione una ubicación de envío', 'woocommerce-delivery-location-map-picker'); ?></h4>
            <div class="sg-del-address-list-inner">
                <?php
                $all_sg_addresses = [];
                $all_sg_addresses = WC()->session->get('sg_user_addresses');
           
                if (!empty($all_sg_addresses)) :
                    $default_address_id = $all_sg_addresses['selected'];
                ?>
                    <?php
                    foreach ($all_sg_addresses['addresses'] as $address_id => $address) {

                    ?>
                        <div class="single-address address-inline available-address <?php echo (!isset($address->position) || !isset($address->door)) ? esc_attr('removed') : esc_attr('default'); ?>">
                            <?php
                            if (!isset($address->position) || !isset($address->door)) {
                            ?>
                                <p><?php esc_html_e('Removed', 'woocommerce-delivery-location-map-picker'); ?></p>
                            <?php
                            }
                            ?>
                            <div class="sg-header-action-container">

                                <?php
                                if ( isset($address->id) &&  $default_address_id === $address->id) {
                                ?>
                                    <input type="radio" class="sg-del-add-select" name="<?php echo esc_attr('selected_' . $section . '_deliver_address'); ?>" data-type="<?php echo esc_attr($section); ?>" value="<?php echo esc_attr($address->id); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_' . $address->id); ?>" checked="true">
                                <?php
                                } else {
                                ?>
                                    <input type="radio" class="sg-del-add-select" name="<?php echo esc_attr('selected_' . $section . '_deliver_address'); ?>" data-type="<?php echo esc_attr($section); ?>" value="<?php echo esc_attr($address->id); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_' . $address->id); ?>">
                                    <div class="sg-dropdown">
                                        <div class="sg-menu-icon sg-menu-action">
                                            <span class="sg-menu-option">...</span>
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
                                <h4 class="title"><?php esc_html_e(( isset($address->address_type) && $address->address_type !== '') ? $address->address_type : $default_address_type_text, 'woocommerce-delivery-location-map-picker'); ?></h4>
                                <p class="text-capitalize address"><?php echo isset($address->address_type) ? esc_html($address->formatted_address) : ""; ?></p>
                            </div>
                            <p class="action-container">
                                <label for="<?php echo esc_attr('sg_delivery_address_' . $section . '_' . $address->id); ?>" class="text-uppercase sg-button sg-del-add-select-button"><?php esc_html_e($default_address_btn_text, 'woocommerce-delivery-location-map-picker'); ?></label>
                            </p>
                        </div>
                <?php
                    }
                endif;
                ?>
                <div id="<?php echo esc_attr('sg_delivery_address_' . $section . '_addnew'); ?>" class="sg-del-add-add-new-opt single-address address-inline add-new-address">

                    <div class="action-container">
                        <p class="text-uppercase sg-button button-outline"><?php esc_html_e($add_new_card_btn_text, 'woocommerce-delivery-location-map-picker'); ?></p>
                    </div>
                </div>
            </div>

        </div>
        <div style="display: none;" class=" sg-del-address">
            <h3 class="sg-del-add-title">Seleccione una ubicación de envío</h3>

            <div class="address-panel sg-del-add-selected-address">
                <p class="change-option"><?php esc_html_e('Cambiar', 'woocommerce-delivery-location-map-picker'); ?></p>
                <h4 class="sg-del-add-type"><?php esc_html_e((get_option('sg_del_address_card_title') === '') ? 'Unknown' : get_option('sg_del_address_card_title', 'Unknown'), 'woocommerce-delivery-location-map-picker'); ?></h4>
                <p class="sg-del-add-description"></p>
                <p class="sg-del-add-area"><?php esc_html_e('Zona', 'woocommerce-delivery-location-map-picker'); ?>: <span></span></p>
                <p class="sg-del-add-landmark"><?php esc_html_e('Otras Señas', 'woocommerce-delivery-location-map-picker'); ?>: <span></span></p>
            </div>
        </div>
    </div>
</div>
<div class="sg-overlay sg-del-add-overlay moovin-map <?php echo esc_attr(wp_is_mobile() ? 'is_mobile' : 'is_web'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_popup_window'); ?>">
    <div class="sg-del-add-popup-inner" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_popup_inner'); ?>">
        <div class="<?php echo esc_attr(wp_is_mobile() ? 'sg-bottom-slider' : 'sg-left-slider'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_popup_panel'); ?>">
            <div class="sg-action-container <?php echo esc_attr(wp_is_mobile() ? 'is_mobile' : 'is_web'); ?>">
                <div class="sg-popup-header">
                    <span data-type="<?php echo esc_attr($section); ?>" class="sg-button sg-popup-close-button close-img"><img src="<?php echo plugin_dir_url(__DIR__) . 'img/icons/close.png'; ?>" alt=""></span>
                    <span class="title"><?php esc_html_e($add_new_form_title, 'woocommerce-delivery-location-map-picker'); ?></span>
                </div>
                <div id="<?php echo esc_attr('sg_delivery_address_' . $section . '_create_form'); ?>" class="sg-address-generate-from sg-popup-content">
                    <?php
                    if (get_option('sg_del_show_address_type') === 'no') {
                    ?>
                        <div class="sg-option sg-field sg-del-add-card-title">
                            <label for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type'); ?>" class="sg-field-label">
                                <?php esc_html_e($address_card_title, 'woocommerce-delivery-location-map-picker'); ?>
                            </label>
                            <input type="text" name="sg_new_address_type" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type'); ?>" required data-clear="false">
                            <span class="sg-error"></span>

                        </div>
                    <?php } ?>
                    <div id="<?php echo esc_attr('sg_delivery_address_' . $section . '_picker_map'); ?>" class="sg-del-add-map-container">
                    </div>
                    <p class="sg-address-container sg-field">
                        <label for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address'); ?>" class="sg-field-label"><?php esc_html_e('Dirección', 'woocommerce-delivery-location-map-picker'); ?></label>
                        <input type="text" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address'); ?>"  id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address'); ?>" required data-clear="true">
                        <input type="hidden" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_lat'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_lat'); ?>" data-clear="true">
                        <input type="hidden" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_lng'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_lng'); ?>" data-clear="true">
                        <span class="sg-error"></span>
                    </p>
                    <div class="sg-mark-address-container">
                        <?php
                        ?>
                            <div class="sg-option sg-field">
                                <label class="sg-field-label" for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_area'); ?>"><?php esc_html_e('Direccion 2', 'woocommerce-delivery-location-map-picker'); ?></label>
                                <input type="text" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_area'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_area'); ?>" data-clear="true">
                            </div>
                        <?php
                        ?>
                            <input type="hidden" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_area'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_area'); ?>" data-clear="true">
                        <?php
                        

                        ?>
                            <div class="sg-option sg-field">
                                <label class="sg-field-label" for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_flat_no'); ?>"><?php esc_html_e('Casa/Unidad/Piso/Etc ', 'woocommerce-delivery-location-map-picker'); ?></label>
                                <input type="text" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_flat_no'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_flat_no'); ?>" data-clear="true">
                            </div>
                        <?php
                        ?>
                            <input type="hidden" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_flat_no'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_flat_no'); ?>" data-clear="true">
                        <?php
                        

                        ?>
                            <div class="sg-option sg-field">
                                <label class="sg-field-label" for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_landmark'); ?>"><?php esc_html_e('Otras señas', 'woocommerce-delivery-location-map-picker'); ?></label>
                                <input type="text" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_landmark'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_landmark'); ?>" data-clear="true">
                            </div>
                        <?php
                        ?>
                            <input type="hidden" name="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_landmark'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_landmark'); ?>" data-clear="true">
                        

                        <?php
                        


                        if (get_option('sg_del_show_address_type') !== 'no') {

                        ?>
                            <div class="sg-option sg-address-type">
                                <div class="show sg-address-inner">
                                    <input type="radio" name="sg_address_type" value="Casa" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type_home'); ?>" class="sg_del_address_type" data-clear="true">
                                    <label class="sg-field-label" for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type_home'); ?>"><?php esc_html_e('Casa', 'woocommerce-delivery-location-map-picker'); ?></label>

                                    <input type="radio" name="sg_address_type" value="Trabajo" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type_office'); ?>" class="sg_del_address_type" data-clear="true">
                                    <label class="sg-field-label" for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type_office'); ?>"><?php esc_html_e('Trabajo', 'woocommerce-delivery-location-map-picker'); ?></label>

                                    <input type="radio" name="sg_address_type" value="Otro" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type_other'); ?>" class="sg_del_address_type" data-clear="true">
                                    <label class="sg-field-label" for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type_other'); ?>"><?php esc_html_e('Otro', 'woocommerce-delivery-location-map-picker'); ?></label>
                                </div>
                                <div class="sg-address-inner address-other-option">
                                    <label for="" class="sg-field sg-other-address-type">
                                        <span class="sg-field-label" for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type_other'); ?>"><?php esc_html_e('Other', 'woocommerce-delivery-location-map-picker'); ?></span>
                                        <input type="text" name="sg_new_address_type" placeholder="<?php esc_attr_e($custom_type_placeholder, 'woocommerce-delivery-location-map-picker'); ?>" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_address_type'); ?>" data-clear="true">
                                        <span class="sg-del-add-type-other sg-btn" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_close_address_type_other'); ?>" class="sg-btn"><?php esc_html_e('cancel', 'woocommerce-delivery-location-map-picker'); ?></span>
                                    </label>
                                </div>
                            </div>

                        <?php } ?>

                        <div class="sg-option sg-field">
                             <button type="button" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_save_address'); ?>" class="sg-button text-uppercase sg-submit-btn sg-popup-footer" style="background-color: #459f2a;color: white;width:100%"><?php esc_html_e($add_new_form_submit_btn_text, 'woocommerce-delivery-location-map-picker'); ?></button>
                        </div>

                        <div class="sg-marked-default" style="margin-bottom:50px"> 
                            <input type="checkbox" name="sg_new_as_default" id="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_as_default'); ?>" value="true" checked="true" data-clear="true">
                            <label for="<?php echo esc_attr('sg_delivery_address_' . $section . '_new_as_default'); ?>">
                                <?php esc_html_e('Marcar como predeterminada', 'woocommerce-delivery-location-map-picker'); ?>
                            </label>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>