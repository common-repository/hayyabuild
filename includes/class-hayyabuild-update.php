<?php
/**
 * Update database class
 *
 * @since			1.0.0
 * @package		hayyabuild
 * @subpackage hayyabuild/includes
 * @author		 zintaThemes <>
 */
if ( ! defined ( 'ABSPATH' ) || class_exists( 'HayyaBuildUpdate' ) ) return;

class HayyaBuildUpdate {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected static $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var unown
	 */
	public function __construct($type) {
		return true;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - HayyaBuildLoader. Orchestrates the hooks of the plugin.
	 * - HayyaBuildi18n. Defines internationalization functionality.
	 * - HayyaBuildAdmin. Defines all hooks for the admin area.
	 * - HayyaBuildPublic. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 */
	public static function update() {
		$DBversion	 = get_option('hayyabuild_version');

		if ( ! $DBversion || version_compare( $DBversion, HAYYABUILD_VERSION, '<' ) ) {

			update_option( 'hayyabuild_version', HAYYABUILD_VERSION );
			
			return true;
		}
		return false;
	}

	/**
	 * Check for any updates
	 *
	 * @since 2.0.0
	 */
	private static function checkForUpdate() {
		return true;
	}
}
