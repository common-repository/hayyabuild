<?php
/**
 * The Exception class.
 *
 *
 *
 * @since	1.0.0
 * @package	hayyabuild
 * @subpackage hayyabuild/includes
 * @author	 zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildException' ) ) return;

class HayyaBuildException extends Exception {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since	1.0.0
	 * @access	protected
	 * @var		string	$plugin_name	The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since	 1.0.0
	 * @access	protected
	 * @var	 string	$version	The current version of the plugin.
	 */
	protected $version;


	/**
	 * @access	public
	 * @since	1.0.0
	 * @var		unown
	 */
	public function __construct( $type ) {
		return '';
	}

	/**
	 * @access	public
	 * @since	 1.0.0
	 * @var	 unown
	 */
	public function errorMessage( $message = '' ) {
		//error message
		return 'Error on line ' . $this->getLine() . ' in '.$this->getFile() . ': <b>'.$this->getMessage().'</b> is not a valid E-Mail address';
	}
}
