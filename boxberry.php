<?php
/*Plugin Name: Boxberry Курьерская Доставка
Description: Плагин добавляет метод расчёта стоимости доставки через курьерскую службу <a href="http://boxberry.ru" target="_blank">Boxberry</a> в плагин WooCommerce.
Version: 0.1
Author: Anton Drobyshev
URI: https://vk.com/wordpress_woocommerce
*/
if ( ! defined( 'ABSPATH' ) ) {	
exit; // Exit if accessed directly.
}

/** * Check if WooCommerce is active */
$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
if ( in_array( 'woocommerce/woocommerce.php',  $active_plugins) ) {
	//Подключаем JavaScript на главную
	add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
	function my_scripts_method(){
		if(is_checkout() && !is_cart()){
			wp_enqueue_script( 'js_boxberry', plugins_url('/assets/js_boxberry.js', __FILE__ ), array('jquery'));
		}
	}
	add_filter( 'woocommerce_shipping_methods', 'add_boxberry_shipping_method' );
	function add_boxberry_shipping_method( $methods ) {
		$methods['boxberry_shipping_method'] = 'WC_Boxberry_Shipping_Method';
		return $methods;
		}
	add_action( 'woocommerce_shipping_init', 'boxberry_shipping_method_init' );
	function boxberry_shipping_method_init(){
		require_once 'class-boxberry-shipping-method.php';
	}
	add_action('woocommerce_before_checkout_billing_form','get_encrypted_key');
	function get_encrypted_key(){
		global $wpdb;
		$option = $wpdb->get_results("SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE '%woocommerce_boxberry_shipping_method%'", ARRAY_A);
		if($option!==NULL){
			$boxberry_settings = get_option($option[0][option_name]);
			$token = $boxberry_settings[token];
			$api_key = $boxberry_settings[api_key];
			echo "<span id='encrypted-token' style='display:none;'>{$api_key}</span>";
		}
	}
}