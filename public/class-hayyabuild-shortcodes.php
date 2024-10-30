<?php
/**
 *
 * HayyaBuild Shortcode class.
 *
 * @since		 2.2.0
 * @package	 hayyabuild
 * @subpackage	hayyabuild/includes
 * @author		zintaThemes <>
 *
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildShortcode' ) ) return;

class HayyaBuildShortcode extends HayyaBuildPublic {

	/**
	 *
	 * Initialize the class and set its properties.
	 * @since	 1.3.0
	 */
	public function __construct() {
		foreach ( $this->getSortCode() as $shortcode ) {
			HayyaBuild::get_loader()->add_shortcode( $shortcode, 'HayyaBuildShortcode', $shortcode );
		}
	}

	/**
	 *
	 * Return HayyaBuild content
	 */
	public static function hayyabuild( $atts ) {
		if ( ! is_array( $atts ) || ! isset( $atts['id'] ) || empty( $atts['id'] ) ) return false;

		$id = $atts['id'];
		$post = get_post( $id );

		if ( ! is_object( $post ) || 'publish' !== $post->post_status || 'hayyabuild' !== $post->post_type ) {
			return '';
		}

		$meta 				= get_post_meta( $post->ID, '_hayyabuild_settings', true );
		$csscode 			= get_post_meta( $post->ID, '_hayyabuild_css', true );
		$inline_style = get_post_meta( $post->ID, '_hayyabuild_inlinestyle', true );
		$style 				= '';

		if ( ! is_array( $meta ) || empty( $meta ) || isset( $meta['pages'] ) || ! empty( $meta['pages'] ) ) {
			return '';
		}

		$style = self::inline_style( $inline_style );

		extract( $meta );

		$video = $class = '';

		$csscode = isset( $csscode ) && ! empty( $csscode ) ? $csscode : '';
		$style = '#hayyabuild-content-' . $id  . '{ ' . HayyaBuildHooks::hb_style($meta) . '}' . $csscode . $style;


		if ( isset( $background_type ) ) {
			$background_effect = isset( $background_effect ) ? $background_effect : [];
			$class .= parent::background_effect( $background_type, $background_effect );

			if ( $background_type === 'background_video' ) {
				$class .= ' hayya-has-video-background';
				$fixed_video = isset( $fixed_video ) ? $fixed_video : '';
				$video = parent::background_video( $type, '', $background_video, $fixed_video, $background_image );
			}
		}

		if ( isset( $scroll_effect ) && ! empty( $scroll_effect ) ) {
			$class .= ' hayya-scrolleffects';
			foreach ( $scroll_effect as $value ) {
				if ( $value ) {
					$class .= " ${value}";
				}
			}
		}

		$content = $video . $post->post_content;

		if ( ! empty( $class )) $class = ' class="hayyabuild-content '.$class.'"';
		$csseditor = isset($csseditor['csseditor']) ? $csseditor['csseditor'] : '';

		return self::render_hayyabuild( $content, $id, $class, $style );
	}

	/**
	 * inline style
	 *
	 * @param		object	$inline_style	The inline style
	 *
	 * @return	 string	style
	 */
	private static function inline_style( $inline_style ) {
		if ( empty( $inline_style ) ) return '';
		$style = '';
		$inline_style = is_object( $inline_style ) ? $inline_style : json_decode( $inline_style );
		if ( empty( $inline_style ) || ! is_object( $inline_style ) ) return '';

		foreach ( $inline_style as $key => $value ) {
			if ( isset( $value->element ) && ! empty( $value->element ) && isset( $value->style ) && ! empty( $value->style ) ) {
				$style .= $value->element . '{' . $value->style . '}';
			}
		}
		return $style;
	}

	/**
	 *
	 * @param		string	$post_content	The post content
	 * @param		string	$id			The identifier
	 * @param		string	$class		 The class
	 * @param		string	$style		 The style
	 *
	 * @return	 string	( description_of_the_return_value )
	 */
	private static function render_hayyabuild( $post_content, $id, $class, $style ) {
		$inline_style = $style ? '<style class="hayyabuild_inline_style">' . $style . '</style>' : '';
		$content = '<div id="hayyabuild-container-content-' . $id . '" class="hayyabuild-container-content"><div id="hayyabuild-content-' . $id . '"'.$class.'>' . $post_content . '</div></div>';

		has_block( 'hayyabuild/openstreetmap', $content ) && wp_enqueue_script( 'leaflet-markercluster' );

		return $inline_style . do_shortcode(
			do_blocks($content),
			true
		);
	}

	/**
	 *
	 * @access		public
	 * @since		 1.0.0
	 * @var		 unown
	 */
	public static function hayyabuild_latestposts( $atts ) {

		$category = isset($atts['category']) ? $atts['category'] : 0;
		$numberposts = isset($atts['numberposts']) ? $atts['numberposts'] : 5;

		$args = array(
			'numberposts' 			=> $numberposts,
			'offset' 						=> 0,
			'category' 					=> $category,
			'orderby' 					=> 'post_date',
			'order' 						=> 'DESC',
			'include' 					=> '',
			'exclude' 					=> '',
			'meta_key' 					=> '',
			'meta_value' 				=>'',
			'post_type' 				=> 'post',
			'post_status' 			=> 'publish, future',
			'suppress_filters' 	=> true
		);

		$recent_posts = wp_get_recent_posts( $args, ARRAY_A );
		$output = '';

		if( $recent_posts ) {
			foreach ( $recent_posts as $post ) {
				$youtube_image = get_post_meta($post['ID'], 'youtube-image', true);

				$youtube = '';

				if ( has_post_thumbnail($post['ID']) ) {
					$thumbnail = get_the_post_thumbnail( $post['ID'], [ '300', '300' ], ['class' => 'hayyabuild-post-thumbnail__news'] );
				} elseif ( $youtube_image ) {
					$youtube = ' hayyabuild-post-thumbnail--youtube';
					$thumbnail = '<img src="https://img.youtube.com/vi/' . esc_attr($youtube_image) . '/hqdefault.jpg" alt="' . esc_attr($post['post_title']) .'" class="hayyabuild-youtube-image attachment-thumbnail"/>';
				}

				$output .= '<div class="hayyabuild-latest-posts__items"><div class="hayyabuild-post-thumbnail'.$youtube.'">';
				$output .= '<a href="'. get_the_permalink($post['ID']) .'" title="' . esc_attr($post['post_title']) .'">';
				$output .= $thumbnail;
				$output .= '</a></div><a class="hayyabuild-latest-posts__items__link" href="' . esc_url( get_permalink($post['ID']) ) .'" title="' . esc_attr($post['post_title']) .'">'. esc_html($post['post_title']) .'</a></div>';
			}
		}

		return '<div class="hayyabuild-latest-posts">' . $output . '</div>';
	}

	/**
	 * Return the page title
	 */
	public static function hayyabuild_pagetitle( $atts, $content = null ) {
		return get_the_title();
	}

	/**
	 * Return the site title
	 */
	public static function hayyabuild_sitetitle( $atts, $content = null ) {
		return wp_title();
	}

	/**
	 * Return current blog title
	 */
	public static function hayyabuild_blogtitle( $atts, $content = null ) {
		return get_bloginfo( 'name' );
	}

	/**
	 * Return current blog description
	 */
	public static function hayyabuild_blogdesc( $atts, $content = null ) {
		return get_bloginfo( 'description' );
	}

	/**
	 * Return current blog description
	 */
	public static function hayyabuild_adminemail( $atts, $content = null ) {
		return get_bloginfo( 'admin_email' );
	}

	/**
	 * Return site URL
	 */
	public static function hayyabuild_siteurl( $atts, $content = null ) {
		return site_url();
	}

	/**
	 * Return site URL
	 */
	public static function hayyabuild_homeurl( $atts, $content = null ) {
		return home_url();
	}

	/**
	 * User name
	 * @return unknown
	 */
	public static function hayyabuild_username() {
		$current_user = wp_get_current_user();
		return $current_user->user_firstname.' '.$current_user->user_lastname;
	}

	/**
	 * Return site URL
	 */
	public static function hayyabuild_date( $atts, $content = null ) {
		$date = date( "Y" );
		if ( ! empty( $atts ) ) {
		 foreach ( $atts as $key => $value ) if ( $key == 'format' && !empty($value) ) $date = date( $value );
		}
		return $date;
	}

	/**
	 * Initialize the class and set its properties.
	 * @since	 3.0.0
	 * @return string[]
	 */
	private function getSortCode() {
	 return array( 'hayyabuild', 'hayyabuild_pagetitle', 'hayyabuild_latestposts', 'hayyabuild_sitetitle', 'hayyabuild_blogtitle', 'hayyabuild_blogdesc', 'hayyabuild_adminemail', 'hayyabuild_siteurl', 'hayyabuild_homeurl', 'hayyabuild_username', 'hayyabuild_date' );
	}

} // end of class HayyaBuildShortcode
