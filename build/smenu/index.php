<?php
/**
 * simple menu block class.
*
* @since	  5.0.0
* @package	hayyabuild
* @subpackage hayyabuild/includes/blocks
* @author	 zintaThemes <>
*/

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildSmenu' )) return;

class HayyaBuildSmenu extends HayyaBuildBlocks
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
    add_action( 'admin_enqueue_scripts', array($this, 'menu_ajax') );

		return self::$_instance = true;
	} // End __construct()

	/**
	 *
	 * ajax menu
	 *
	 */
	public function menu_ajax() {
		$menu_list = get_terms ( 'nav_menu', array ( 'hide_empty' => true, 'fields' => 'all' ) );
		! empty($menu_list) && wp_localize_script( 'hayyabuild-blocks', 'hayyabuild_menu_list', $menu_list );
	}

	/**
	 * render_callback
	 *
	 * @param	  array   $attributes  The attributes
	 *
	 * @return	 string  ( description_of_the_return_value )
	 */
	public function render_callback( $attributes = [] ) {
		if ( empty( $attributes ) || ! is_array($attributes) ) return '';
		extract($attributes);

		$depth = 6;

		$class = ! empty($classesList) ? ' '.$classesList : '';

		$menu_align = $align ? ' hb-' . $align : '';
		$menu_vertical = isset($vertical) && $vertical ? ' hb_vertical' : ' hb_horizontal';

		$menu_before = '<div class="' . esc_attr( 'wp-block-hayyabuild-smenu ' . $menu_align . $class . ' ' . $id) . '"><ul class="' . esc_attr($menu_vertical) . '">';
		$wp_nav_menu = wp_nav_menu( array(
			'sort_column' => 'menu_order',
			'container' => false,
			'menu' => $menu,
			// 'menu_id' => $id,
			'menu_class' => false,
			'items_wrap' => '%3$s',
			'echo' => false,
			'depth'=> $depth
		) );
		$menu_after = '</ul></div>';
		return $wp_nav_menu ? $menu_before . $wp_nav_menu . $menu_after : '';
	} // End render_callback()

} // End HayyaBuild {} class
