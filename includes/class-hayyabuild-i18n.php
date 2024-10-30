<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since			 1.0.0
 * @package		 hayyabuild
 * @subpackage	hayyabuild/includes
 * @author			zintaThemes <>
 *
 *
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildi18n' ) ) return;

class HayyaBuildi18n {

	/**
	 * The domain specified for this plugin.
	 *
	 * @since		1.0.0
	 * @access	 private
	 * @var			string		$domain		The domain identifier for this plugin.
	 */
	private $domain;

		/**
		 * Initialize the collections used to maintain the actions and filters.
		 *
		 * @since		1.0.0
		 */
		public function __construct($domain) {
			$this->domain = $domain;
		}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since		1.0.0
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'hayyabuild', false, 'hayyabuild/languages/' );
		return true;
	}

	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since		1.0.0
	 * @param		string		$domain		The domain that represents the locale of this plugin.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}
