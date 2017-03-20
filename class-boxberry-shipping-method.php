<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class WC_Boxberry_Shipping_Method extends WC_Shipping_Method{
	
	public function __construct( $instance_id = 0 ){
		add_action('woocommerce_update_options_shipping_methods', array(&$this, 'process_admin_options'));
		$this->instance_id = absint( $instance_id );
		$this->supports  = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal'
		);
		$this->id = 'boxberry_shipping_method';
		$this->method_title = __( 'Boxberry', 'woocommerce' );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables
		$this->enabled  = $this->get_option( 'enabled' );
		$this->title     = $this->get_option( 'title' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title' => array(
				'title'        => __( 'Курьерская служба Boxberry', 'woocommerce' ),
				'type'         => 'text',
				'description'  => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'      => __( 'Boxberry', 'woocommerce' ),
			),
			'token' => array(
				'title'        => __( 'Token', 'woocommerce' ),
				'type'         => 'text',
				'description'  => __('Ваш токен для входа в ЛК Боксберри. Token можно узнать по <a href="http://api.boxberry.de/?act=info&sub=api_info_lk" target="_blank">ссылке</a>. Пример: 15325.rvmpqdcc', 'woocommerce' ),
				'default'      => __( '', 'woocommerce' ),
			),
			'api_key' => array(
				'title'        => __( 'Ключ интеграции', 'woocommerce' ),
				'type'         => 'text',
				'description'  => __('Ключ интеграции можно получить по <a href="http://api.boxberry.de/?act=settings&sub=view" target="_blank">ссылке</a>. Пример ключа: QoI8ZDdHDRt5PHjy0RIaGg==', 'woocommerce' ),
				'default'      => __( '', 'woocommerce' ),
			)
		);
}

	public function is_available( $package ){
		global $wpdb;
		$option = $wpdb->get_results("SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE '%woocommerce_boxberry_shipping_method%'", ARRAY_A);
		if($option!==NULL){
			$boxberry_settings = get_option($option[0][option_name]);
			$token = $boxberry_settings[token];
			$api_key = $boxberry_settings[api_key];
		}
		if(!empty($token) && !empty($api_key)){
			
			$url='http://api.boxberry.de/json.php?token='.$token.'&method=ListCities';
			$handle = fopen($url, "rb");
			$contents = stream_get_contents($handle);
       		fclose($handle);
        	$data=json_decode($contents,true);
			if(count($data)<=0 or $data[0]['err']){
				echo $data[0]['err'];
			}else{
				function in_multi_array($needle, $data){
					$in_multi_array = false;
					if(in_array($needle, $data)){
						$in_multi_array = true;
					}else{    
						for($i = 0; $i < sizeof($data); $i++){
							if(is_array($data[$i])){
								if(in_multi_array($needle, $data[$i])){
									$in_multi_array = true;
									break;
								}
							}
						}
					}
					return $in_multi_array;
				}
			}
			if(!function_exists('mb_ucfirst') && extension_loaded('mbstring')){
				function mb_ucfirst($str, $encoding='UTF-8'){
					$str = mb_ereg_replace('^[\ ]+', '', $str);
					$str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
					mb_substr($str, 1, mb_strlen($str), $encoding);
					return $str;
				}
			}
			$city = trim(mb_convert_case($package['destination']['city'],MB_CASE_TITLE, 'UTF-8'));
			if(in_multi_array($city, $data)){
				return true;
			}else{
				return false;
			}
		}
	}
	public function calculate_shipping( $package = array() ){
    // send the final rate to the user. 
		$this->add_rate( array(
			'id'     => $this->id,
			'label'  => $this->title,
			'cost'   => $cost
		));
	}
}