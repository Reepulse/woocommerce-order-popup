<?php
/*
Plugin Name: woocommerce order popup
Description: Plugin to display popup on admin dashboard whenever new order is received
Version: 1.0
Author: Reepulse
*/

if(!defined('ABSPATH'))
{
    die;
}

defined('ABSPATH') or die('You shall not pass!');

if(!function_exists('add_action'))
{
    echo "You shall not pass!";
    exit;
}

//require woocommerce to install global coupons for woocommerce
add_action( 'admin_init', 'woocommerce_order_popup_require_woocommerce' );

function woocommerce_order_popup_require_woocommerce() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) 
    {
        add_action( 'admin_notices', 'woocommerce_order_popup_require_woocommerce_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) 
        {
            unset( $_GET['activate'] );
        }
    }
}

//throw admin notice if woocommerce is not active
function woocommerce_order_popup_require_woocommerce_notice(){
    ?>
    <style> #toplevel_page_woocommerce_order_popup{display:none;} </style>
    <div class="error"><p>Sorry, but Woocommerce Order Popup for Woocommerce requires the Woocommerce plugin to be installed and activated.</p></div>
    <?php
    return;
}

//settings link for plugin page
function woocommerce_order_popup_settings_link( $links ) 
{
    if(!is_admin()) exit();

	$links[] = '<a href="' .
		admin_url( 'admin.php?page=woocommerce_order_popup_settings' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}

//css for admin panel
function woocommerce_order_popup_admin_css() 
{
	wp_register_style('woocommerce-order-popup-admin-css', plugins_url('assets/woocommerce-order-popup.css',__FILE__ ), array(), rand(111,9999), 'all');
    wp_enqueue_style('woocommerce-order-popup-admin-css');
}

add_action( 'admin_init','woocommerce_order_popup_admin_css');

function woocommerce_order_popup_admin_js()
{
    wp_register_style('woocommerce-order-popup-admin-js', 'https://code.jquery.com/jquery-1.8.2.js' , array(), rand(111,9999), 'all');
    wp_enqueue_style('woocommerce-order-popup-admin-js');
    
}

add_action( 'admin_init','woocommerce_order_popup_admin_js');

if(!class_exists('WoocommerceOrderPopup'))
{
    class WoocommerceOrderPopup
    {
        function __construct()
        {
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'woocommerce_order_popup_settings_link');
            require_once(dirname(__FILE__) . '/woocommerce-order-popup-admin.php');
            require_once(dirname(__FILE__) . '/woocommerce-order-popup-settings.php');
        }
    }
}

if(class_exists('WoocommerceOrderPopup'))
{
    $woocommerceOrderPopup = new WoocommerceOrderPopup();
}

register_activation_hook( __FILE__, array($woocommerceOrderPopup, '__construct'));

