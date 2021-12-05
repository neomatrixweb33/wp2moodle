<?php

?><?php


define( 'WP2M_PUGIN_NAME', 'Wordpress 2 Moodle (SSO)');
define( 'WP2M_CURRENT_VERSION', '1.9' );
define( 'WP2M_CURRENT_BUILD', '1' );
define( 'EMU2_I18N_DOMAIN', 'wp2m' );
define( 'WP2M_MOODLE_PLUGIN_URL', '/auth/wp2moodle/login.php?data=');

function wp2m_set_lang_file() {
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if (@file_exists($moFile) && is_readable($moFile)) {
			load_textdomain(EMU2_I18N_DOMAIN, $moFile);
		}

	}
}

wp2m_set_lang_file();

function wp2m_register_shortcode() {
	add_shortcode('wp2moodle', 'wp2moodle_handler');
}


add_action( 'admin_menu', 'wp2m_create_menu' );
add_action( 'admin_init', 'wp2m_register_settings' );
register_activation_hook(__FILE__, 'wp2m_activate');
register_deactivation_hook(__FILE__, 'wp2m_deactivate');
register_uninstall_hook(__FILE__, 'wp2m_uninstall');
add_action ( 'init', 'wp2m_register_shortcode');
add_action ( 'init', 'wp2m_register_addbutton');

function wp2m_generate_encryption_key() {
	return base64_encode(openssl_random_pseudo_bytes(32));
}

function wp2m_is_base64($string) {
    $decoded = base64_decode($string, true);
    
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) return false;
    
    if (!base64_decode($string, true)) return false;
    
    if (base64_encode($decoded) != $string) return false;
    return true;
}


function wp2m_activate() {

	$shared_secret = wp2m_generate_encryption_key();

	add_option('wp2m_moodle_url', 'http://localhost/moodle');
	add_option('wp2m_shared_secret', $shared_secret);
}


function wp2m_deactivate() {
	delete_option('wp2m_moodle_url');
	delete_option('wp2m_shared_secret');
}


function wp2m_uninstall() {
	delete_option( 'wp2m_moodle_url' );
	delete_option( 'wp2m_shared_secret' );
}


function wp2m_create_menu() {
	add_menu_page(
		__('wp2Moodle', EMU2_I18N_DOMAIN),
		__('wp2Moodle', EMU2_I18N_DOMAIN),
		'manage_options',
		dirname(__FILE__).'/wp2m_settings_page.php',
		null,
		plugin_dir_url(__FILE__).'icon.svg'
	);
}


function wp2m_register_settings() {
	
	register_setting( 'wp2m-settings-group', 'wp2m_moodle_url' );
	register_setting( 'wp2m-settings-group', 'wp2m_shared_secret' );
}


function encrypt_string($value, $key) {
	if (wp2m_is_base64($key)) {
		$encryption_key = base64_decode($key);
	} else {
		$encryption_key = $key;
	}
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	$encrypted = openssl_encrypt($value, 'aes-256-cbc', $encryption_key, 0, $iv);
	$result = str_replace(array('+','/','='),array('-','_',''),base64_encode($encrypted . '::' . $iv));
	return $result;
}


function decrypt_string($data, $key) {
	if (wp2m_is_base64($key)) {
		$encryption_key = base64_decode($key);
	} else {
		$encryption_key = $key;
	}
	list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}



function wp2moodle_handler( $atts, $content = null ) {

	extract(shortcode_atts(array(
		"cohort" => '',
		"group" => '',
		"course" => '',
		"class" => 'wp2moodle',
		"target" => '_self',
		"authtext" => '',
		"activity" => 0,
		"cmid" => 0,
		"url" => '',
	), $atts));

	if ($content == null || !is_user_logged_in() ) {
		if (trim($authtext) == "") {
			$url = '<a href="' . wp_registration_url() . '" class="'.esc_attr($class).'">' . do_shortcode($content) . '</a>'; 
		} else {
			$url = '<a href="' . wp_registration_url() . '" class="'.esc_attr($class).'">' . do_shortcode($authtext) . '</a>'; 
		}
	} else {
		
		$url = '<a target="'.esc_attr($target).'" class="'.esc_attr($class).'" href="'.wp2moodle_generate_hyperlink($cohort,$group,$course,$activity,$url,$cmid).'">'.do_shortcode($content).'</a>'; 
	}
	return $url;
}

add_filter('mp_download_url', 'wp2m_download_url', 10, 3);
add_filter('woocommerce_download_file_redirect','woo_wp2m_download_url', 5, 2);
add_filter('woocommerce_download_file_force','woo_wp2m_download_url', 5, 2);
add_filter('woocommerce_download_file_xsendfile','woo_wp2m_download_url', 5, 2);


function woo_wp2m_download_url($filepath, $filename) {
	wp2m_download_url($filepath, "", "");
}


function wp2m_download_url($url, $order, $download) {

	if (strpos($url, 'wp2moodle.txt') !== false) {
		
		$path = $_SERVER['DOCUMENT_ROOT'] . parse_url($url)["path"];
		$cohort = "";
		$group = "";
		$urllog = "";
		$activity = 0;
		$cmid = 0;
		$data = file($path); 
		foreach ($data as $row) {
			$pair = explode("=",$row);
			switch (strtolower(trim($pair[0]))) {
				case "group":
					$group = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "cohort":
					$cohort = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "course":
					$course = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "activity":
					$activity = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "cmid":
					$cmid = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
				case "url":
					$urllog = trim(str_replace(array('\'','"'), '', $pair[1]));
					break;
			}
		}
		$url = wp2moodle_generate_hyperlink($cohort,$group,$course,$activity,$urllog,$cmid);
		if (ob_get_contents()) { ob_clean(); }
		header('Location: ' . $url, true, 301); 
		exit();
	}
	return $url;
}
function wp2moodle_generate_hyperlink($cohort,$group,$course,$activity = 0, $url = null, $cmid = 0) {

	
	global $current_user;
    wp_get_current_user();

	$update = get_option('wp2m_update_details') ?: "true";

    $enc = array(
		"offset" => rand(1234,5678),						
		"stamp" => time(),									
		"firstname" => $current_user->user_firstname,		
		"lastname" => $current_user->user_lastname,			
		"email" => $current_user->user_email,				
		"username" => $current_user->user_login,			
		"passwordhash" => $current_user->user_pass,			
		"idnumber" => $current_user->ID,					
		"cohort" => $cohort,								
		"group" => $group,									
		"course" => $course,								
		"activity" => $activity,							
		"cmid" => $cmid,									
		"url" => rawurlencode($url)							
	);

	
	$details = http_build_query($enc);

	
	return rtrim(get_option('wp2m_moodle_url'),"/").WP2M_MOODLE_PLUGIN_URL.encrypt_string($details, get_option('wp2m_shared_secret'));
	
}


function wp2m_register_addbutton() {
	if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
	    add_filter('mce_external_plugins', 'wp2m_add_plugin');
	    add_filter('mce_buttons', 'wp2m_register_button');
	}
}
function wp2m_register_button($buttons) {
   array_push($buttons,"|","wp2m"); 
   return $buttons;
}
function wp2m_add_plugin($plugin_array) {
   $plugin_array['wp2m'] = plugin_dir_url(__FILE__).'wp2m.js';
   return $plugin_array;
}

?>
