<?php
/**
 * Settings Model
 *
 * @see Vallejo plugin Options
 * @link http://codex.wordpress.org/Creating_Options_Pages
 *
 * @package Vallejo Menu Plugin
 */

defined( 'ABSPATH' ) or exit;

/**
 * Vallejo Menu Settings class
 */
class Vallejo_Menu_Settings {

	/**
	 * Settings to register
	 *
	 * @see constructor
	 *
	 * @var array
	 */
	private $settings = array(
		'vmenu_currency',
		'vmenu_columns',
		'vmenu_menus',
		'vmenu_share',
	);


	/**
	 * Options Group for settings
	 *
	 * @var string
	 */
	private $options_group = 'vmenu_settings';


	/**
	 * Options
	 *
	 * @see constructor
	 *
	 * @var array
	 */
	public $options = array();


	/**
	 * The defaults
	 *
	 * @see constructor
	 *
	 * @var array
	 */
	public $defaults = array();


	/**
	 * Constructor
	 *
	 * Set settings, plugin_page & defaults
	 */
	function __construct() {

		$this->defaults = array(
			'vmenu_currency' => '$',
			'vmenu_columns' => '1',
			'vmenu_menus' => array(), // Menus.
			'vmenu_share' => array( // Auto Share.
				'activated' => '',
			),
		);

		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'run_defaults' ) );

		$this->get_options();

	}



	/**
	 * Add options page.
	 */
	public function add_plugin_page() {
		add_options_page(
			__( 'Vallejo Menu Plugin Settings', 'vmenu' ),
			__( 'Vallejo Menu Plugin', 'vmenu' ),
			'edit_plugins',
			'vmenu_settings',
			array( $this, 'plugin_settings' )
		);
	}

	/**
	 * Display plugin options
	 */
	public function plugin_settings() {

		wp_enqueue_media();

		// View.
		require_once VMENU_PATH . 'view/view-vmenu-settings.php';
	}


	/**
	 * Get Options
	 */
	private function get_options() {
		foreach ( $this->settings as $setting ) {

			$this->options[ $setting ] = get_option( $setting );
		}
	}


	/**
	 * Register Settings callback
	 */
	public function register_settings() {
		foreach ( $this->settings as $setting ) {

			register_setting( $this->options_group, $setting );
		}
	}


	/**
	 * Runs The default theme options
	 */
	public function run_defaults() {
		foreach ( $this->defaults as $key => $value ) {

			if ( ! get_option( $key ) ) {

				update_option( $key, $value );
			}
		}
	}


	/**
	 * Notifications helper
	 *
	 * @param  string $text Notification text.
	 * @param  string $type 'error' or 'update'.
	 */
	private function notification( $text, $type = 'update' ) {

		$class = 'updated';

		if ( 'error' === $type ) {
			$class = 'error';
		}

		?>

		<div class="<?php echo esc_attr( $class ); ?>">
			<p><?php echo esc_html( $text ); ?></p>
		</div>

		<?php
	}
}
