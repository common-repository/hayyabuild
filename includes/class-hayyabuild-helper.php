<?php
/**
 * Helper class.
 *
 *
 * @since		1.0.0
 * @package	hayyabuild
 * @subpackage hayyabuild/includes
 * @author	 zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildHelper' ) ) return;

class HayyaBuildHelper {

	/**
	 * redirect static varibale
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			string	$plugin_name	The string used to uniquely identify this plugin.
	 */
	public static $redirect = array();

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			string	$plugin_name	The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			string	$version	The current version of the plugin.
	 */
	protected $version;

	/**
	 *
	 * @since		3.0.0
	 * @access		public
	 * @var		 array		$options
	 */
	public static $options = array();

	/**
	 * construct function
	 *
	 * @access		public
	 * @since		1.0.0
	 */
	public function __construct() {
		return true;
	}

	/**
	 * Admin notices function.
	 *
	 * @since	 1.0.0
	 * @param	 String		 $message	 notice message
	 * @param	 String		 $type		 notice type
	 */
	public static function _notices( $message = '', $type = '' ) {
		add_action('admin_notices', function() use ( $message, $type ) {
			echo '<div class="notice notice-'.esc_attr( $type ).' is-dismissible"><p>' . wp_kses_post( $message ) . '</p></div>';
		});
	} // End __notice()

	/**
	 *
	 * @access		public
	 * @since		1.0.0
	 * @param		 string		$param
	 */
	public static function _get($param) {
		return ( isset( $_GET[$param] ) ) ? wp_unslash( $_GET[$param] ) : false;
	} // End _get()

	/**
	 *
	 * @param		 string		 $param
	 */
	public static function _post($param) {
		return ( isset( $_POST[$param] ) ) ? wp_unslash( $_POST[$param] ) : false;
	} // End _post()

	/**
	 *
	 * @since	 1.0.0
	 */
	public static function _empty( $var = null ) {
		return ( null === $var ) ? '' : $var; // TODO: remove this functions
	} // End _empty()

	/**
	 * Get wpdb.
	 *
	 * @since	 1.0.0
	 */
	public static function _debug( $message ) {
		return ( ! empty( $message ) ) ? '<div class="_debug">'.$message.'</div>' : '';
	} // End _debug()

	/**
	 * Check current user
	 *
	 * @since	 3.2.0
	 */
	public static function _current_user( $capability = null ) {
		return current_user_can(
			$capability ? $capability : 'manage_options'
		);
	} // End _get_content()

	/**
	 *
	 * @since	 3.2.0
	 */
	public static function _ajax_nonce($process = null) {
		if ( null === $process ) return check_ajax_referer( $process );
	}

	/**
	 *
	 * @since	 6.0
	 */
	public static function _content_type($process = null) {
		return [ 'header', 'content', 'footer' ];
	}

}
