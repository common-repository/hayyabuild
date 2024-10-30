<?php
/**
 * The pagecontent block class.
*
* @since	  5.0.0
* @package	hayyabuild
* @subpackage hayyabuild/includes/blocks
* @author	 zintaThemes <>
*/

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildPagecontent' )) return;

class HayyaBuildPagecontent extends HayyaBuildBlocks
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
	public function render_callback( $attributes, $content ) {
		if ( $attributes['editor'] && ! get_the_ID() ) {
			return '<div class="wp-block-hayyabuild-pagecontent"><h3 class="hb-center">' . esc_html('Page Content', 'hayyabuild') . '<br/><i class="dashicons dashicons-format-aside"></i></h3></div>';
		}
		$class = isset($attributes['classesList']) ? ' class="'.$attributes['classesList'].'"' : '';
		return '<div' . $class . '>' . $this->pagecontent() . '</div>';
	} // End render_callback()

	/**
	 * Return Page Content
	 *
	 * @param unknown $atts
	 * @param unknown $content
	 */
	private function pagecontent( $atts = null ) {
		if ( is_admin() || is_404() ) return '';
		$content = '';
		$id = get_the_ID();
		if ( ! empty($id) ) {
			$page = get_post( $id );
			if ( !empty($page) && isset($page->post_content) ) {
				$content = has_block($page->post_content) ? do_blocks($page->post_content) : $page->post_content;
			}
		}
		return $content;
	} // End hayyabuild_pagecontent()

} // End HayyaBuild {} class
