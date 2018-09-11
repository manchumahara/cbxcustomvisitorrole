<?php

	/**
	 *
	 * @link              https://codeboxr.com
	 * @since             1.0.0
	 * @package           cbxcustomvisitorrole
	 *
	 * @wordpress-plugin
	 * Plugin Name:       CBX Custom Visitor Role
	 * Plugin URI:        https://github.com/manchumahara/cbxcustomvisitorrole
	 * Description:       This plugin adds a new role named cbxcustomvisitorrole for guest or visitor user
	 * Version:           1.0.1
	 * Author:            Codeboxr
	 * Author URI:        https://codeboxr.com
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       cbxcustomvisitorrole
	 * Domain Path:       /languages
	 */

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}


	defined( 'CBXCUSTOMVISITORROLE_PLUGIN_NAME' ) or define( 'CBXCUSTOMVISITORROLE_PLUGIN_NAME', 'cbxcustomvisitorrole' );
	defined( 'CBXCUSTOMVISITORROLE_PLUGIN_VERSION' ) or define( 'CBXCUSTOMVISITORROLE_PLUGIN_VERSION', '1.0.0' );
	defined( 'CBXCUSTOMVISITORROLE_BASE_NAME' ) or define( 'CBXCUSTOMVISITORROLE_BASE_NAME', plugin_basename( __FILE__ ) );
	defined( 'CBXCUSTOMVISITORROLE_ROOT_PATH' ) or define( 'CBXCUSTOMVISITORROLE_ROOT_PATH', plugin_dir_path( __FILE__ ) );
	defined( 'CBXCUSTOMVISITORROLE_ROOT_URL' ) or define( 'CBXCUSTOMVISITORROLE_ROOT_URL', plugin_dir_url( __FILE__ ) );


	class CBXCustomVisitorRole
	{
		public function __construct()
		{
			//load translation
			load_plugin_textdomain('cbxcustomvisitorrole', false, dirname(plugin_basename(__FILE__)) . '/languages/');

			//help from https://wordpress.stackexchange.com/questions/107622/i-need-to-assign-a-role-to-visitors-guests
			global $user_login, $wp_roles;
			$exist_visitor_role = false;
			foreach ($wp_roles->role_names as $role => $name) {
				if ($role == "cbxcustomvisitorrole") {
					$exist_visitor_role = true;
					break;
				}
			}
			// Anyway visitor role must be handled even have not been be permanently defined
			if (! $exist_visitor_role == true) {
				add_role('cbxcustomvisitorrole', 'Visitor', array('read' => true));
			}
			// If the visitor is not logged in he gets necessarily the custom "cbxcustomvisitorrole" role
			// ####trebly:CAREFULL:The problem is that some plugins can have checked no role for visitor : this must be checked
			// by $user_login or wp_get_current_user() and check and role empty
			// if role has been just set a Warning is displayed to upgrade because he has read all capability by default
			if( ! $user_login ) {
				// can have the role already set
				$current_user = wp_get_current_user();
				$current_user->add_role('cbxcustomvisitorrole');
			}
		}
	}
	/**
	 * Load Plugin when all plugins loaded
	 *
	 * @return void
	 */
	function cbxcustomvisitorrole_load_plugin()
	{
		new CBXCustomVisitorRole();
	}

	add_action('plugins_loaded', 'cbxcustomvisitorrole_load_plugin', 5);
