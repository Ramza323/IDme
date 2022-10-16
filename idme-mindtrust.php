<?php

/**
 * Plugin Name: ID.me configuration.
 * Plugin URI: https://mindtrust.com
 * Description: This plugin update the message on the ID.me section checkout and deactivate it
 * Version: 2.0.3
 * Author: Santiago Guarin Alzate
 * Author URI: https://www.facebook.com/zanty.guaro/
 */
require_once plugin_dir_path(__FILE__) . 'inc/template-calling.php';
require_once plugin_dir_path(__FILE__) . 'routes/dashboard.php';
require_once plugin_dir_path(__FILE__) . 'routes/wizard.php';
require_once plugin_dir_path(__FILE__) . 'routes/users.php';
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/Ramza323/IDme',
	__FILE__,
	'idme-mindtrust'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('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');

function registerStyles()
{
    wp_register_script('datatables', 'https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', array('jquery'), true);
    wp_enqueue_script('datatables');
    wp_register_script('datatables_bootstrap', 'https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js', array('jquery'), true);
    wp_enqueue_script('datatables_bootstrap');
    wp_register_style('datatables__css', 'https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css');
    wp_enqueue_style('datatables__css');
    wp_register_style('idme_dashboard_css', plugins_url('./assets/css/soft-design-system-pro.css?v=1.0.9', __FILE__));
    wp_enqueue_style('idme_dashboard_css');
    wp_register_script('idme_dashboard_js', plugins_url('./assets/js/functions.js', __FILE__));
    wp_enqueue_script('idme_dashboard_js');
    wp_register_script('idme_dashboard_bootbox_js', plugins_url('./assets/js/bootbox.js', __FILE__));
    wp_enqueue_script('idme_dashboard_bootbox_js');
    wp_register_style('idme_dashboard_fonts', "https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700");
    wp_enqueue_style('idme_dashboard_fonts');
}

function registerScript(){
    wp_register_script('idme_function_js', plugins_url('./assets/js/idme.js', __FILE__));
    wp_enqueue_script('idme_function_js');
    wp_register_style('idme_page_css', plugins_url('./assets/css/main.css', __FILE__));
    wp_enqueue_style('idme_page_css');
}

class IdMeConfiguration_2
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_idme_configuration'));
        add_action('cfw_coupon_module_end', 'add_ship_info', 5);
        add_action('wp_footer', 'bbloomer_add_jscript_checkout', 9999);
        add_action('woocommerce_after_checkout_shipping_form', 'wc_checkout_description_so_1');
        add_action('woocommerce_cart_calculate_fees', 'woocommerce_custom_surcharge');
        
        function woocommerce_custom_surcharge(WC_Cart $WC_Cart)
        {
            global $woocommerce;
            $values = get_values();

            if (is_admin() && !defined('DOING_AJAX'))
                return;

            $percentage = $values->discount;
            $surcharge = $woocommerce->cart->cart_contents_total * $percentage / 100;
            if ($_COOKIE['verification'] != '') {
                $WC_Cart->add_fee('Verified at ID.me -'.$values->discount.'%', -$surcharge, false, '');
            }
        }

        function wc_checkout_description_so_1($checkout){
            wp_register_script('idme_function_js', plugins_url('./assets/js/idme.js', __FILE__));
            wp_enqueue_script('idme_function_js');
        }

        function bbloomer_add_jscript_checkout()
        {
            global $wp;
            if (is_checkout()) {
                echo '<script src="https://s3.amazonaws.com/idme/developer/idme-buttons/assets/js/idme-wallet-button.js"></script>';
            }
        }

        function add_ship_info()
        {
            $values = get_values();
            if($values->enable == 1){
                echo '<span
                    id="idme-wallet-button"
                    data-scope="'.$values->scope.'"
                    data-client-id="'.$values->client_id.'"
                    data-redirect="'.$values->checkout.'"
                    data-response="code"
                    data-text="'.$values->message.'"
                    data-show-verify="true">
                </span>';
            }
        }

        add_action('rest_api_init', function () {
            register_rest_route(
                'idme/v1',
                '/verified/',
                array(
                    'methods' => 'POST',
                    'callback' => 'verified',
                )
            ); 
        });
        
        function verified(){
            setcookie('verification', 'verified');
        }

        add_action('wp_logout', 'end_session');
        add_action('wp_login', 'end_session');
        add_action('wp_authenticate','end_session');
        add_action('rest_api_init', function () {
            register_rest_route(
                'idme/v1',
                '/verification/',
                array(
                    'methods' => 'POST',
                    'callback' => 'id_me_verification',
                )
            );
        });

        function id_me_verification($data)
        {
            $values = get_values();
            $code = $data['code'];
            $uri = $data['uri'];
            $url = 'https://api.id.me/oauth/token';
            $curl = curl_init();
            if($uri == 'idme'){
                $redirect_url = $values->landing;
            } else if ($uri == 'checkout'){
                $redirect_url = $values->checkout;
            }
            $fields = array(
                'code' => $code,
                'client_id' => $values->client_id ,
                'client_secret' => $values->client_secret,
                'redirect_uri' => $redirect_url,
                'grant_type' => 'authorization_code'
            );

            $fields_string = http_build_query($fields);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);


            $data = curl_exec($curl);
            curl_close($curl);
            $obj = json_decode($data);
            if (array_key_exists('error', $obj)) {
                $_SESSION['verification'] = '';
                return $obj;
            } else {
                $request = wp_remote_get('https://api.id.me/api/public/v3/attributes.json?access_token=' . $obj->access_token);
                setcookie('verification', 'verified');
                $data = json_decode($request['body'], true);
                global $wpdb;
                $sql  = "INSERT INTO `idme_verified_users` VALUES (NULL, '".$data['attributes'][1]['value']."', '".$data['attributes'][2]['value']."', '".$data['attributes'][0]['value']."', '".$data['attributes'][3]['value']."', '".$data['status'][0]['group']."', CURRENT_TIMESTAMP)";
                if (!function_exists('dbDelta')) {
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                }
                dbDelta($sql);
                return 'verified';
            }
        }

        function end_session()
        {
            unset( $_COOKIE['verification'] );
            setcookie( 'verification', '', time() - ( 15 * 60 ) );
        }
    }

    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_idme_configuration()
    {
        add_menu_page('ID.me Config', 'ID.me Config', 'manage_options', __FILE__, 'clivern_render_plugin_page', plugins_url('/assets/img/icon.svg', __FILE__));
        add_submenu_page(__FILE__, 'Configuration', 'Configuration', 'manage_options', __FILE__ . '/configuration', 'clivern_render_custom_page');
        add_submenu_page(__FILE__, 'Users', 'Users', 'manage_options', '', 'clivern_render_about_page');
    }
}
new IdMeConfiguration_2();

register_activation_hook(__FILE__, 'child_plugin_activate');
function child_plugin_activate()
{
    if (!is_plugin_active('advanced-custom-fields-pro/acf.php') and !is_plugin_active('checkout-for-woocommerce/checkout-for-woocommerce.php') and current_user_can('activate_plugins')) {
        wp_die('Sorry, but this plugin requires the advanced-custom-fields-pro and checkoutWC to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
    }

    if (!get_option('tables_created_idme_mindtrust_2.0', false)) {

        global $wpdb;
        $sql  = 'CREATE TABLE idme_configuration(
            id INT(20) AUTO_INCREMENT,
            message VARCHAR(255),
            discount VARCHAR(255),
            scope VARCHAR(255),
            client_id VARCHAR(255),
            client_secret VARCHAR(255),
            landing VARCHAR(255),
            checkout VARCHAR(255),
            enable BOOLEAN,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(id))';

        if (!function_exists('dbDelta')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }
        dbDelta($sql);

        $sql  = 'CREATE TABLE idme_verified_users(
            id INT(20) AUTO_INCREMENT,
            first_name VARCHAR(255),
            last_name VARCHAR(255),
            email VARCHAR(255),
            state VARCHAR(255),
            group_name VARCHAR(255),
            verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(id))';
        dbDelta($sql);

        $sql  = "INSERT INTO `idme_configuration` (`id`, `message`, `discount`, `scope`, `client_id`, `client_secret`, `landing`, `checkout`, `enable`, `updated_at`) VALUES (1, NULL, NULL, NULL, NULL, NULL,NULL, NULL, 0, CURRENT_TIMESTAMP)";
        dbDelta($sql);
        update_option('tables_created_idme_mindtrust_2.0', true);
    }
}

if (function_exists('acf_add_local_field_group')) :
    acf_add_local_field_group(array(
        'key' => 'group_61dfab0dcbb9b',
        'title' => 'IDme Page Fields',
        'fields' => array(
            array(
                'key' => 'field_61dfab0dd062d',
                'label' => 'Hero',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_61dfab0dd0667',
                'label' => 'Hero',
                'name' => 'hero',
                'type' => 'group',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_61dfab0dd743a',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'key' => 'field_61dfab0dd7475',
                        'label' => 'Headline',
                        'name' => 'headline',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_61dfab0dd74af',
                        'label' => 'Lead',
                        'name' => 'lead',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_61dfab0dd06a0',
                'label' => 'IDme section',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_61dfab0dd06db',
                'label' => 'IDme section',
                'name' => 'idme_section',
                'type' => 'group',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_61dfab0dde90c',
                        'label' => 'Headline',
                        'name' => 'headline',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_61dfab0dde947',
                        'label' => 'Lead',
                        'name' => 'lead',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_61dfab0dd0715',
                'label' => 'Persons',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_61dfab0dd074e',
                'label' => 'Persons',
                'name' => 'Persons',
                'type' => 'group',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_61dfab0de9f75',
                        'label' => 'Cards',
                        'name' => 'cards',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'block',
                        'button_label' => '',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_61dfab0deb4c1',
                                'label' => 'Image',
                                'name' => 'image',
                                'type' => 'image',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'return_format' => 'url',
                                'preview_size' => 'medium',
                                'library' => 'all',
                                'min_width' => '',
                                'min_height' => '',
                                'min_size' => '',
                                'max_width' => '',
                                'max_height' => '',
                                'max_size' => '',
                                'mime_types' => '',
                            ),
                            array(
                                'key' => 'field_61dfab0deb4fc',
                                'label' => 'Headline',
                                'name' => 'headline',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_61dfab0deb535',
                                'label' => 'Lead',
                                'name' => 'lead',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_61dfab0deb573',
                                'label' => 'Links',
                                'name' => 'links',
                                'type' => 'repeater',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'collapsed' => '',
                                'min' => 0,
                                'max' => 0,
                                'layout' => 'block',
                                'button_label' => '',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_61dfab0defbdd',
                                        'label' => 'Url',
                                        'name' => 'url',
                                        'type' => 'text',
                                        'instructions' => '',
                                        'required' => 0,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'default_value' => '',
                                        'placeholder' => '',
                                        'prepend' => '',
                                        'append' => '',
                                        'maxlength' => '',
                                    ),
                                    array(
                                        'key' => 'field_61dfab0defc18',
                                        'label' => 'Text',
                                        'name' => 'text',
                                        'type' => 'text',
                                        'instructions' => '',
                                        'required' => 0,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'default_value' => '',
                                        'placeholder' => '',
                                        'prepend' => '',
                                        'append' => '',
                                        'maxlength' => '',
                                    ),
                                    array(
                                        'key' => 'field_61dfab0defc51',
                                        'label' => 'Button',
                                        'name' => 'button',
                                        'type' => 'clone',
                                        'instructions' => '',
                                        'required' => 0,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'clone' => array(
                                            0 => 'field_5fda06e9b9ea8',
                                        ),
                                        'display' => 'seamless',
                                        'layout' => '',
                                        'prefix_label' => 0,
                                        'prefix_name' => 0,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_61dfac588aa08',
                'label' => 'CTA',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_61dfac538aa04',
                'label' => 'CTA',
                'name' => 'cta',
                'type' => 'group',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_61dfac538aa05',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'key' => 'field_61dfac538aa06',
                        'label' => 'Headline',
                        'name' => 'headline',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_61dfac538aa07',
                        'label' => 'Lead',
                        'name' => 'lead',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_61dfac878aa09',
                        'label' => 'Button Name',
                        'name' => 'button_name',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_61dfac8f8aa0a',
                        'label' => 'Button URL',
                        'name' => 'button_url',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_61dfacb38aa0b',
                'label' => 'Pre Footer',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_61dfad028aa15',
                'label' => 'Pre Footer',
                'name' => 'pre_footer',
                'type' => 'group',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_61dfad028aa16',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'key' => 'field_61dfad028aa17',
                        'label' => 'Headline',
                        'name' => 'headline',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_61dfad028aa18',
                        'label' => 'Lead',
                        'name' => 'lead',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_61dfab0dd07ff',
                'label' => 'FAQs',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_61dfab0dd0839',
                'label' => 'FAQs',
                'name' => 'faqs',
                'type' => 'group',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_61dfab0e0f04b',
                        'label' => 'Headline',
                        'name' => 'headline',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_61dfab0e0f085',
                        'label' => 'FAQs',
                        'name' => 'faqs',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'block',
                        'button_label' => '',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_61dfab0e1136c',
                                'label' => 'Question',
                                'name' => 'question',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_61dfab0e113a6',
                                'label' => 'Answer',
                                'name' => 'answer',
                                'type' => 'wysiwyg',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'tabs' => 'all',
                                'toolbar' => 'basic',
                                'media_upload' => 0,
                                'delay' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => '../templates/page-idme.php',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(
            0 => 'permalink',
            1 => 'the_content',
            2 => 'excerpt',
            3 => 'discussion',
            4 => 'comments',
            5 => 'revisions',
            6 => 'slug',
            7 => 'author',
            8 => 'format',
            9 => 'page_attributes',
            10 => 'featured_image',
            11 => 'categories',
            12 => 'tags',
            13 => 'send-trackbacks',
        ),
        'active' => true,
        'description' => '',
    ));

endif;
