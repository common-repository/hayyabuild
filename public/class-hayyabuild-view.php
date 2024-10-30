<?php
/**
 *
 * The admin-view functionality of the plugin.
 *
 * @since				1.0.0
 * @package			hayyabuild
 * @subpackage	 hayyabuild/admin
 * @author			 zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildView' ) ) return;

class HayyaBuildView extends HayyaBuildPublic {

	/**
	 * Define the view for forntend.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @access		public
	 * @since		1.0.0
	 * @var			unown
	 */
	public function __construct() {
		return;
	}


	/**
	 *
	 * @param unknown $message
	 */
	public static function video_background( $type, $content, $background_video = null, $fixed_video = null, $background_image = null ) {
		return <<<HTML
		<div class="hayya-video-background">
			<video autoplay loop muted poster="{$background_image}">
				<source src="{$background_video}" type="video/mp4">
			</video>
		</div>
		{$content}
HTML;
	}

	/**
	 * content output
	 *
	 * @param      string  $content     The content
	 * @param      string  $type        The type
	 * @param      string  $id          The identifier
	 * @param      string  $class       The class
	 * @param      string  $attributes  The attributes
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public static function content( $content = '', $type = '', $id = '', $class = '', $attributes = '' ) {
		if ( 'header' === $type || 'footer' === $type ) {
			$content = '<div class="container">' . $content . '</div>';
		}
		$content = '<div id="' . $id . '" class="' . $class . '" ' . $attributes . '>' . $content . '</div>';
		$contetn = '<div class="hayyabuild-container-.' . $type . '">' . $content . '</div>';
		return '<div id="hayyabuild-before-'.$type.'"></div>'.$content.'<div id="hayyabuild-after-'.$type.'"></div>';
	}

} // End Class
