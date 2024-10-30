<?php
/**
 * The pagecontent block class.
*
* @since	  5.0.0
* @package	hayyabuild
* @subpackage hayyabuild/includes/blocks
* @author	 zintaThemes <>
*/

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildHayyabuildcontent' )) return;

class HayyaBuildTemplateparts extends HayyaBuildBlocks
{

	/**
	 * The single instance of HayyaBuild.
	 * @var	 object
	 * @access  private
	 * @since	 3.0.0
	 */
	private static $_instance = false;

	/**
	 * Define the pagecontent block class.
	 *
	 * @access		public
	 * @since		1.0.0
	 * @var		  unown
	 */
	public function __construct() {
		if ( self::$_instance ) return self::$_instance;

		return self::$_instance = true;
	} // End __construct()

	/**
	 * render_callback
	 *
	 * @param	  array   $attributes  The attributes
	 *
	 * @return	 string  ( description_of_the_return_value )
	 */
	public function render_callback( $atts ) {
		if ( ! is_array( $atts ) || ! isset( $atts['type'] ) || empty( $atts['type'] ) ) {
			return false;
		}
		$output = '';
		if ( function_exists('hayyabuild') ) {
			ob_start();
			hayyabuild( $atts['type'] );
			$output = ob_get_contents();
			ob_end_clean();
		}
		return $output;
	} // End render_callback()

} // End HayyaBuild {} class
