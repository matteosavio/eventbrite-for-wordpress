<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://digitalideas.io/
 * @since      1.0.0
 *
 * @package    Eventbrite_For_Wordpress
 * @subpackage Eventbrite_For_Wordpress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Eventbrite_For_Wordpress
 * @subpackage Eventbrite_For_Wordpress/admin
 * @author     Digital Ideas <matteo@digitalideas.io>
 */
class Eventbrite_For_Wordpress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'api_setting';


	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Eventbrite_For_Wordpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eventbrite_For_Wordpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/eventbrite-for-wordpress-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Eventbrite_For_Wordpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Eventbrite_For_Wordpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/eventbrite-for-wordpress-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Eventbrite Events', 'api_setting' ),
			__( 'Eventbrite Events', 'api_setting' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);
	}
	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page() {
		include_once 'partials/eventbrite-for-wordpress-admin-display.php';
	}
	/**
	 * Register all related settings of this plugin
	 *
	 * @since  1.0.0
	 */
	public function register_setting() {
		add_settings_section(
			$this->option_name . '_general',
			__( 'General123213', 'api_setting' ),
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);
		add_settings_field(
			$this->option_name . '_apikey',
			__( 'API KEY', 'api_setting' ),
			array( $this, $this->option_name . '_apikey_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_apikey' )
		);
		
		add_settings_field(
			$this->option_name . '_orgprofile',
			__( 'ORGANIZER_PROFILE', 'api_setting' ),
			array( $this, $this->option_name . '_orgprofile_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_orgprofile' )
		);
		register_setting( $this->plugin_name, $this->option_name . '_apikey');
		register_setting( $this->plugin_name, $this->option_name . '_orgprofile');
	}
	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function api_setting_general_cb() {
		echo '<p>' . __( 'Please change the settings accordingly --TESTS.', 'api_setting' ) . '</p>';
	}
	/**
	 * Render the radio input field for position option
	 *
	 * @since  1.0.0
	 */
	public function api_setting_apikey_cb() {
		$position = get_option( $this->option_name . '_apikey' );
		?>
			<fieldset>
				<label>
					<input type="text" name="<?php echo $this->option_name . '_apikey' ?>" id="<?php echo $this->option_name . '_apikey' ?>" value="">					
				</label>				
			</fieldset>
		<?php
	}
	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function api_setting_orgprofile_cb() {
		$day = get_option( $this->option_name . '_orgprofile' ); ?>
		<fieldset>
				<label>			
					<input type="text" name="<?php echo $this->option_name . '_orgprofile' ?>" id="<?php echo $this->option_name . '_orgprofile';?>" value=""> 
				</label>
		</fieldset>			
	<?php }
}

