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


	class CBXCustomVisitorRole {
		public function __construct() {
			//load translation
			load_plugin_textdomain( 'cbxcustomvisitorrole', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			add_action( 'wp_roles_init', array( $this, 'init_custom_role' ), 10 );


			if ( ! is_user_logged_in() ) {
				// can have the role already set
				$current_user = wp_get_current_user();
				$current_user->add_role( 'cbxcustomvisitorrole' );
			}
		}


		/**
		 * Add new role and assign role to guest
		 *
		 * @param object $wp_roles
		 */
		public function init_custom_role( $wp_roles = null ) {
			// Attempt to get global roles if not passed in & not mid-initialization
			if ( ( null === $wp_roles ) && ! doing_action( 'wp_roles_init' ) ) {
				$wp_roles = $this->bbp_get_wp_roles(); //hired this method from bbpress
			}

			//help from https://wordpress.stackexchange.com/questions/107622/i-need-to-assign-a-role-to-visitors-guests


			$exist_visitor_role = false;
			foreach ( $wp_roles->role_names as $role => $name ) {
				if ( $role == "cbxcustomvisitorrole" ) {
					$exist_visitor_role = true;
					break;
				}
			}

			// Anyway visitor role must be handled even have not been be permanently defined
			if ( ! $exist_visitor_role == true ) {

				$capability = apply_filters( 'cbxcustomvisitorrole_capability',
					array( 'read' => true )
				);

				$role_details = apply_filters( 'cbxcustomvisitorrole_role_details',
					array(
						'name'         => 'Visitor',
						'capabilities' => $capability
					) );

				$wp_roles->roles['cbxcustomvisitorrole']        = $role_details;
				$wp_roles->role_objects['cbxcustomvisitorrole'] = new WP_Role( 'cbxcustomvisitorrole', $role_details['capabilities'] );
				$wp_roles->role_names['cbxcustomvisitorrole']   = $role_details['name'];
			}

			return $wp_roles;
		}

		/**
		 * Get the $wp_roles global without needing to declare it everywhere
		 *
		 * @since bbPress (r4293)
		 *
		 * @global WP_Roles $wp_roles
		 * @return WP_Roles
		 */
		function bbp_get_wp_roles() {
			global $wp_roles;

			// Load roles if not set
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}

			return $wp_roles;
		}
	}

	/**
	 * Load Plugin when all plugins loaded
	 *
	 * @return void
	 */
	function cbxcustomvisitorrole_load_plugin() {
		new CBXCustomVisitorRole();
	}

	add_action( 'plugins_loaded', 'cbxcustomvisitorrole_load_plugin', 5 );
