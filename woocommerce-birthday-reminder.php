<?php
/**
 * Plugin Name: WooCommerce Birthday Reminder
 * Plugin URI: http://agenciamagma.com.br
 * Description: Send an email to the users with birthday in the current month and to the users with birthday in the current day
 * Version: 1.0.0
 * Author: agenciamagma, Carlos Cardoso Dias
 * Author URI: http://agenciamagma.com.br
 * Requires at least: 4.0
 * Tested up to: 4.1
 * 
 * Text Domain: woocommerce-birthday-reminder
 *
 * @package WooCommerce Birthday Reminder
 * @category Core
 * @author agenciamagma, Carlos Cardoso Dias
 *
 * License: GPL2
 **/

/**
 * Anti cheating code
 **/
defined( 'ABSPATH' ) or die( 'A Ag&ecirc;ncia Magma n&atilde;o deixa voc&ecirc; trapacear ;)' );

/**
 * Check if this plugin isn't already loaded and WooCommerce Extra is active
 **/
if ( ! class_exists( 'WooCommerce_Birthday_Reminder' ) && in_array( 'woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php' , apply_filters( 'active_plugins' , get_option( 'active_plugins' ) ) ) ):
final class WooCommerce_Birthday_Reminder {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function plugin_path() {
		return plugin_dir_path( __FILE__ );
	}

	public static function activate() {
		wp_schedule_event( strtotime( date( 'Y-m-d' ) . ' 00:00:01' ), 'daily' , 'check-user-birthday' );
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( 'check-user-birthday' );
	}

	public function __construct() {
		add_action( 'check-user-birthday', array( $this , 'check_birthday' ) );
		add_filter( 'woocommerce_email_classes' , array( $this , 'add_user_birthday_email' ) );
	}

	public function check_birthday() {
		// Check if today is the first day of the month
		if ( date( 'j' ) == '1' ) {
			// If it's the first day of the month, get all the month birthday users and trigger the action
			$args = array(
				'meta_key'      => 'billing_birthdate',
				'meta_value'    => sprintf( '/%s/' , date( 'm' ) ),
				'meta_compare'  => 'LIKE'
			);

			$users = new WP_User_Query( $args );			

			if ( ! empty( $users->results ) ) {
				// Trigger the action passing the users as argument
				WC()->mailer()->get_emails();
				do_action( 'birthdays_users_of_the_month' , $users->results );
			}
		}

		// Get the users with birthday today
		$args = array(
			'meta_key'      => 'billing_birthdate',
			'meta_value'    => sprintf( '^%s' , date( 'd/m' ) ),
			'meta_compare'  => 'REGEXP'
		);

		$users = new WP_User_Query( $args );

		if ( ! empty( $users->results ) ) {
			// Trigger the action for birthday users passing users as argument
			WC()->mailer()->get_emails();
			do_action( 'birthdays_users_of_the_day', $users->results );
		}
	}

	public function add_user_birthday_email( $email_classes ) {
		require_once( 'class-wc-user-birthday-email.php' );
		$email_classes['WC_User_Birthday_Email'] = new WC_User_Birthday_Email();;
		return $email_classes;
	}
}

/**
 * Registering activation and deactivation actions
 **/
register_activation_hook( __FILE__ , array( 'WooCommerce_Birthday_Reminder' , 'activate' ) );
register_deactivation_hook( __FILE__ , array( 'WooCommerce_Birthday_Reminder' , 'deactivate' ) );

/**
 * Initialize the plugin
 **/
add_action( 'plugins_loaded' , array( 'WooCommerce_Birthday_Reminder' , 'get_instance' ) );

endif;