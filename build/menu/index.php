<?php
/**
 * The menu block class.
*
* @since	  5.0.0
* @package	hayyabuild
* @subpackage hayyabuild/includes/blocks
* @author	 zintaThemes <>
*/

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildMenu' )) return;

class HayyaBuildMenu extends HayyaBuildBlocks
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
		add_action( 'admin_enqueue_scripts', [ $this, 'menu_ajax'] );

		return self::$_instance = true;
	} // End __construct()

	/**
	 *
	 * ajax menu
	 *
	 */
	public function menu_ajax() {
		$menu_list = get_terms ( 'nav_menu', [ 'hide_empty' => true, 'fields' => 'all' ] );
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

		// $depth = $editor ? 1 : 4;
		$depth = 4;

		$menu_align = $align ? ' hayyabuild-menu--' . $align : '';

		$menu_icon = $menuIcon ? '<li class="hayyabuild-menu-icon"><a href="/"><i class="' . esc_attr($menuIcon) . '"></i></a></li>' : '';

		$class = ! empty($classesList) ? ' '.$classesList : '';

		$menu_container = isset($container) && $container ? ' container' : '';

		$menu_before = '<div class="wp-block-hayyabuild-menu' . esc_attr($menu_align . $class . ' ' . $id) . '"><div class="hb-toggle-menu"><div class="hb-toggle-menu__lines"></div></div><ul class="hb_menuul' . esc_attr($menu_container) . '">' . $menu_icon;
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
