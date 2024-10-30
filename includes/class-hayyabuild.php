<?php
/**
 * The core plugin class.
*
* @since		1.0.0
* @package	hayyabuild
* @subpackage hayyabuild/includes
* @author	 zintaThemes <>
*/

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuild' ) ) return;

class HayyaBuild
{

	/**
	 * The single instance of HayyaBuild.
	 * @var	 object
	 * @access	private
	 * @since	 3.0.0
	 */
	private static $_instance = false;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			Loader	$loader	Maintains and registers all hooks for the plugin.
	 */
	protected static $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @access		public
	 * @since		1.0.0
	 * @var			unown
	 */
	public function __construct(	$type	= null ) {
		if ( self::$_instance ) return self::$_instance;
		self::load_dependencies();
		is_admin() && $this->define_admin();
		return self::$_instance = true;
	} // End __construct()

	/**
	 * Load the required dependencies for this plugin.
	 * 
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since		1.0.0
	 * @access		private
	 *
	 */
	private static function load_dependencies() {
		require_once HAYYABUILD_PATH. 'includes' . DIRECTORY_SEPARATOR . 'class-hayyabuild-loader.php';
		return self::$loader = new HayyaBuildLoader();
	} // End load_dependencies()

	/**
	 * Load HayyaBuild updater class
	 * @since		3.0.0
	 * @return		 unknown
	 */
	public static function update() {
		return HayyaBuildUpdate::update();
	} // End update()

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since		1.0.0
	 * @access		private
	 */
	private static function set_locale() {
		self::get_loader()->add_action('plugins_loaded', 'HayyaBuildi18n', 'load_plugin_textdomain');
		return true;
	} // End set_locale()

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since		1.0.0
	 * @access		private
	 */
	public function define_admin() {
		return class_exists('HayyaBuildAdmin') && new  HayyaBuildAdmin() && $this->set_locale();
	} // End define_admin_hooks()

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since		1.0.0
	 * @access		private
	 */
	private static function define_public( $type = null ) {
		return function_exists( 'register_block_type' ) && class_exists('HayyaBuildPublic') && ( new HayyaBuildPublic( $type ) )::$output;
	} // End define_public_hooks()

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since	 1.0.0
	 * @return	Plugin_Name_Loader	Orchestrates the hooks of the plugin.
	 */
	public static function get_loader() {
		return self::$loader;
	} // End get_loader()

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-hayyabuild-activator.php
	 *
	 * @since	 1.0.0
	 */
	public static function hayyabuild_activate() {
		return HayyaBuildActivator::activate();
	} // End hayyabuild_activate()

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-hayyabuild-deactivator.php
	 *
	 * @since	 1.0.0
	 */
	public static function hayyabuild_deactivate() {
		return HayyaBuildDeactivator::deactivate();
	} // End hayyabuild_deactivate()

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since		1.0.0
	 */
	public static function run( $type = null ) {
		! self::$_instance && new self( $type );
		$return = ! is_admin() && self::define_public( $type );
		self::get_loader()->run();
		return $return;
	} // End run()

} // End HayyaBuild {} class
