<?php
/**
 * Plugin Name: DX Out of Date
 * Description: Display a notice above each post of yours that has been published a while ago and might be outdated.
 * 
 */

include_once 'dx-ood-helper.php';

class DX_Out_Of_Date {
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ), 3 );
		add_action( 'init', array( $this, 'load_files' ) );
		// register admin pages for the plugin
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
		add_action( 'template_redirect', array( $this, 'top_content_filter' ) );
		add_action( 'init', array( $this, 'add_shortcodes' ) );
	}
	
	public function top_content_filter() {
		$ood_setting = get_option( 'ood_setting', array() );
		if ( ! empty( $ood_setting['dx_ood_enable'] ) && is_single() ) {
			add_filter( 'the_content', array( $this, 'top_content_filter_callback' ) );
		}
	}
	
	public function top_content_filter_callback( $content ) {
		$ood_setting = get_option( 'ood_setting', array() );

		// If no options are set, bail
		if ( empty( $ood_setting['dx_ood_duration_frame'] ) 
			|| empty( $ood_setting['dx_ood_period'] )
			|| empty( $ood_setting['dx_ood_message'] ) ) {
			return $content;
		}
		
		$duration = $ood_setting['dx_ood_duration_frame'];
		$period = (int) $ood_setting['dx_ood_period'];

		$message = $ood_setting['dx_ood_message'];
		
		$post_date = DX_OOD_Helper::get_post_date();
		$current_date = DX_OOD_Helper::get_current_date();
		
		$interval = DX_OOD_Helper::get_date_interval( $post_date, $current_date, $duration );
		$box = '<div class="out-of-date">' . do_shortcode( $message ). '</div>';
		
		return $box . $content;
	}
	
	public function load_files() {
		
	}
	
	public function register_settings() {
		include_once 'dx-ood-settings.php';

		new DX_OOD_Settings();
	}
	
	/**
	 * Admin pages
	 */
	public function register_admin_page() {
		add_submenu_page( 'options-general.php', __( "Out of Date", 'dxbase' ), __( "Out of Date", 'dxbase' ),
		 'manage_options', 'dx-ood', array( $this, 'register_admin_page_callback' ) );
	}
	
	public function register_admin_page_callback() {
		include_once 'dx-ood-admin.php';
	}
	
	/**
	 * Notifying box content generator
	 */
	public function old_post_box() {
		
	}
	
	/**
	 * Shortcodes
	 */
	public function add_shortcodes() {
		add_shortcode( 'ood_date' , array( $this, 'ood_date_shortcode' ) );
	}
	
	public function ood_date_shortcode( $atts, $content ) {
		return get_the_date();
	}
	
}

new DX_Out_Of_Date();