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

class HayyaBuildHayyabuildcontent extends HayyaBuildBlocks
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
		add_action( 'admin_enqueue_scripts', [ $this, 'hayyabuildcontent_ajax'] );

		return self::$_instance = true;
	} // End __construct()

	/**
	 *
	 * ajax menu
	 *
	 */
	public function hayyabuildcontent_ajax() {
    $posts = get_posts(['post_type' => 'hayyabuild']);
    $list = [];

    foreach ( $posts as $post ) {
      $settings = get_post_meta( $post->ID, '_hayyabuild_settings', true );

      if ( !isset($settings['pages']) || empty($settings['pages']) ) {
        $list[] = [ 'id' => $post->ID, 'name' => $post->post_title ];
      }

    }
		wp_localize_script( 'hayyabuild-blocks', 'hayyabuild_content_list', $list );
	}

	/**
	 * render_callback
	 *
	 * @param	  array   $attributes  The attributes
	 *
	 * @return	 string  ( description_of_the_return_value )
	 */
	public function render_callback( $atts ) {
		return isset( $atts['contentID'] ) && !empty( $atts['contentID']) && class_exists('HayyaBuildShortcode') ? HayyaBuildShortcode::hayyabuild( [ 'id' => $atts['contentID'] ] ) : '';
	} // End render_callback()

} // End HayyaBuild {} class
