<?php
/**
 * The magicbox block class.
*
* @since	  5.0.0
* @package	hayyabuild
* @subpackage hayyabuild/includes/blocks
* @author	 zintaThemes <>
*/

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildMagicbox' )) return;

class HayyaBuildMagicbox extends HayyaBuildBlocks
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
	 * render callback
	 *
	 * @param	  array   $attrs	The attributes
	 * @param	  string  $content  The content
	 *
	 * @return	 string  ( description_of_the_return_value )
	 */
	public function render_callback( $attrs, $content ) {
		if ( empty($content) || empty( $attrs ) ) return '';

		if (
			($attrs['is_user_logged_in'] === 'hide' && is_user_logged_in()) ||
			($attrs['is_user_logged_in'] === 'show' && !is_user_logged_in())
		) {
			return '';
		}

		$show_on_all = $attrs['show_on_all'];
		$show = !!$show_on_all;

		$ignore = [ 'show_pages', 'hide_pages', 'show_on_all', 'id', 'classesList', 'inlineStyle', 'is_user_logged_in' ];
		foreach ( $attrs as $key => $value ) {
			if ( in_array( $key, $ignore ) ) continue;

			$show = function_exists($key) && call_user_func($key) ? $value : $show;
		}


		if ( !empty($attrs['hide_pages']) || !empty($attrs['show_pages']) ) {
			$pages		  = $show_on_all ? $attrs['hide_pages'] : $attrs['show_pages'];
			$pageID		 = get_the_ID();
			$pages_array	= explode( ',', $pages );
			$in_list		= in_array( $pageID, $pages_array );
			if ( $in_list && !empty($pages_array) ) {
				$show = $show_on_all ? !$in_list : $in_list;
			}
		}

		return $show ? $this->content($content, $attrs['id'], $attrs['classesList'] ) : '';
	} // End render_callback()

	/**
	 * { function_description }
	 *
	 * @param	  integer|string  $content	 The content
	 * @param	  string		  $id		  The identifier
	 * @param	  string		  $class_name  The class name
	 *
	 * @return	 <type>		  ( description_of_the_return_value )
	 */
	private function content($content = null, $id = '', $class_list = '' ) {
		return $content ? '<div id="'.$id.'" class="'.$class_list.' '.$id.'">'.$content.'</div>' : '';
	}

} // End HayyaBuild {} class
