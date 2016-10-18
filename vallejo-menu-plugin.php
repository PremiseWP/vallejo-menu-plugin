<?php
/**
 * Plugin Name: Vallejo Menu Plugin
 * Plugin URI:  http://plugins.premisewp.com/premise-portfolio/vallejo-menu-plugin
 * Description: Add Menu Items & Menus to your Restaurant website. Automatically share your Menus on Facebook & Twitter. Originally designed for the Vallejo Restaurant theme.
 * Version:     1.0.1
 * Author:      Premise WP (@premisewp)
 * Author URI:  http://premisewp.com
 * License:     see LICENSE file
 *
 * @package Vallejo Menu Plugin
 */

// prevent direct access to this file
defined( 'ABSPATH' ) or exit;

// the plugin's path and url constants
define( 'VMENU_PATH', plugin_dir_path( __FILE__ ) );
define( 'VMENU_URL',  plugin_dir_url( __FILE__ ) );


// Install Plugin
register_activation_hook( __FILE__, array( 'Vallejo_Menu_Plugin', 'do_install' ) );

// Uninstall Plugin.
register_uninstall_hook( __FILE__, array( 'Vallejo_Menu_Plugin', 'do_uninstall' ) );


/**
 * The main function that returns Vallejo_Menu_Plugin
 *
 * The main function responsible for returning the one true Vallejo_Menu_Plugin
 * Instance that functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example         <?php $vmenu = vmenu(); ?>
 *
 * @return   object The one true Vallejo_Menu_Plugin Instance.
 */
function vmenu() {

	return Vallejo_Menu_Plugin::get_instance();
}




// Instantiate our main class and setup Vallejo Menu Plugin
// Must use 'plugins_loaded' hook.
add_action( 'plugins_loaded', array( vmenu(), 'vmenu_setup' ) );

/**
 * Load Vallejo Menu Plugin!
 *
 * This is Vallejo Menu Plugin main class.
 */
class Vallejo_Menu_Plugin {


	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 *
	 * @var object
	 */
	protected static $instance = null;




	/**
	 * Settings Object.
	 *
	 * This handles the admin settings and screens.
	 *
	 * @var object
	 */
	public $settings;




	/**
	 * Constructor. Intentionally left empty and public.
	 *
	 * @see 	vmenu_setup()
	 */
	public function __construct() {}





	/**
	 * Access this plugin’s working instance
	 *
	 * @return  object instance of this class
	 */
	public static function get_instance() {

		null === self::$instance and self::$instance = new self;

		return self::$instance;
	}





	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 */
	public function __clone() {

		// Cloning instances of the class is forbidden.
		exit;
	}




	/**
	 * Disable unserializing of the class.
	 *
	 * @return void
	 */
	public function __wakeup() {

		// Unserializing instances of the class is forbidden.
		exit;
	}





	/**
	 * Setup Vallejo Menu Plugin
	 *
	 * Does includes, registers hooks & load language.
	 */
	public function vmenu_setup() {

		$this->do_includes();
		$this->vmenu_hooks();
		$this->load_language( 'vmenu' );
	}






	/**
	 * Includes
	 */
	protected function do_includes() {

		// Require Premise WP.
		if ( ! class_exists( 'Premise_WP' ) ) {

			// Require Premise WP plugin with the help of TGM Plugin Activation.
			require_once VMENU_PATH . 'includes/class-tgm-plugin-activation.php';

			add_action( 'tgmpa_register', array( $this, 'vmenu_register_premisewp_plugin' ) );
		}

		/**
		 * Controllers
		 */

		if ( is_admin() ) {

			require_once VMENU_PATH . 'controller/controller-vmenu-settings.php';

			// Social Media Auto Publish plugin.
			if ( function_exists( 'premise_get_option' ) ) {
				if ( premise_get_option( 'vmenu_share[activated]' ) ) { // If Auto Share activated.

					require_once VMENU_PATH . 'includes/social-media-auto-publish/social-media-auto-publish.php';
				}
			}

		} else { // Front end.

			require_once VMENU_PATH . 'controller/controller-vmenu-menu-page.php';
		}

		require_once VMENU_PATH . 'controller/controller-vmenu-menu-item.php';
	}






	/**
	 * Install
	 *
	 * @param boolean $networkwide Network wide?.
	 */
	public static function do_install( $networkwide ) {
		// save an option in the DB when this plugin gets installed to flush rewrite rules on init
		if ( ! get_option( '_vmenu_activation_happened' ) )
			add_option( '_vmenu_activation_happened', true );

		// Social Media Auto Publish plugin.
		require_once VMENU_PATH . 'includes/social-media-auto-publish/social-media-auto-publish.php';
		require_once VMENU_PATH .  'includes/social-media-auto-publish/admin/install.php';

		smap_free_network_install( $networkwide );
	}





	/**
	 * Uninstall
	 *
	 * @param boolean $networkwide Network wide?.
	 */
	public static function do_uninstall( $networkwide ) {

		// // Social Media Auto Publish plugin.
		require_once VMENU_PATH . 'includes/social-media-auto-publish/social-media-auto-publish.php';
		require_once VMENU_PATH .  'includes/social-media-auto-publish/admin/destruction.php';

		smap_free_network_destroy( $networkwide );

		// remove rewrite rules check from DB
		delete_option( '_vmenu_activation_happened' );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}




	/**
	 * Premise Hooks
	 *
	 * Registers and enqueues scripts, adds classes to the body of DOM
	 */
	public function vmenu_hooks() {

		// Enqueue scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'vmenu_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'vmenu_scripts' ) );

		// Add classes to body.
		add_filter( 'body_class', array( $this, 'body_class' ) );

		// Add classes to body.
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );

		// add rewrite flush rules on init with a higher priority than 10
		add_action( 'init', array( $this, 'vmenu_maybe_flush_rules' ), 11 );
	}



	/**
	 * Flush rewrite rules if our plugin was just activated.
	 *
	 * @return void does not return anything
	 */
	public function vmenu_maybe_flush_rules() {
		// if this option exists we just activated the plugin, flush rewrite rules
		if ( get_option( '_vmenu_activation_happened' ) ) {
			flush_rewrite_rules();
			// delete the option so we dont flush rules again
			delete_option( '_vmenu_activation_happened' );
		}
	}




	/**
	 * Add premise classes to body of document in the front-end and backend
	 *
	 * @param  array|string $classes  array|string of classes being passed to the body.
	 * @return array|string           array|string including our new classes.
	 */
	public function body_class( $classes ) {

		if ( is_admin() ) {
			return $classes . 'vmenu vmenu-admin';
		}

		$classes[] = 'vmenu';

		return $classes;
	}






	/**
	 * Vallejo Menu Plugin CSS & JS
	 *
	 * Vallejo Menu Plugin loads 2 main files:
	 * Vallejo-Menu-Plugin.min.css, and Vallejo-Menu-Plugin.min.js.
	 *
	 * @author Dave Gandy http://twitter.com/davegandy
	 */
	public function vmenu_scripts() {

		// Register styles.
		wp_register_style( 'vmenu_style_css'   , VMENU_URL . 'css/Vallejo-Menu-Plugin.min.css', array() );

		// Register scripts.
		wp_register_script( 'vmenu_script_js'  , VMENU_URL . 'js/Vallejo-Menu-Plugin.min.js', array( 'jquery' ) );

		// Enqueue our styles and scripts for both admin and frontend.
		wp_enqueue_style( 'vmenu_style_css' );
		wp_enqueue_script( 'vmenu_script_js' );

		if ( is_admin() ) {

			// Msdropdown.
			wp_register_script(
				'msdropdown',
				VMENU_URL . 'js/lib/msdropdown/jquery.dd.min.js',
				array( 'jquery' )
			);

			wp_enqueue_script( 'msdropdown' );

			// Msdropdown CSS.
			wp_register_style(
				'msdropdown',
				VMENU_URL . 'js/lib/msdropdown/dd.css'
			);

			wp_enqueue_style( 'msdropdown' );
		}
	}





	/**
	 * Loads translation file.
	 *
	 * Currently not supported. but here for future integration
	 *
	 * @wp-hook init
	 *
	 * @param   string $domain Domain.
	 * @return  void
	 */
	public function load_language( $domain ) {
		load_plugin_textdomain(
			$domain,
			false,
			VMENU_PATH . 'languages'
		);
	}


	/**
	 * Register the Premise WP plugin for this theme.
	 *
	 * We register one plugin:
	 * - Premise-WP from a GitHub repository
	 *
	 * @link https://github.com/PremiseWP/Premise-WP
	 */
	function vmenu_register_premisewp_plugin() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(
			// Include Premise-WP plugin.
			array(
				'name'               => 'Premise-WP', // The plugin name.
				'slug'               => 'Premise-WP', // The plugin slug (typically the folder name).
				'source'             => 'https://github.com/PremiseWP/Premise-WP/archive/master.zip', // The plugin source.
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				// 'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
				'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				// 'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
				// 'external_url'       => '', // If set, overrides default API URL and points to an external URL.
				// 'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
			),
		);

		/*
		 * Array of configuration settings.
		 */
		$config = array(
			'id'           => 'vmenu-tgmpa',         // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'install_plugins',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}
}
