<?php
/**
 * Public scripts output class
 *
 *
 * @package	hayyabuild
 * @subpackage hayyabuild/public
 * @author	 zintaThemes
 *
 *
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildHooks' ) ) return;

/**
 * 
 */
class HayyaBuildHooks extends HayyaBuildPublic
{

	/**
	 * IDs
	 *
	 * @var        array
	 */
	public static $ids = [];

	/**
	* Initialize the class and set its properties.
	*
	* @since		3.0.0
	* @param			string	$plugin_name		 The name of the plugin.
	* @param			string	$version	The version of this plugin.
	*/
	public function __construct() {
		HayyaBuild::get_loader()->add_action('wp_head', $this, 'head_code');
		HayyaBuild::get_loader()->add_action('wp_enqueue_scripts', $this, 'scripts_start', 11);
		return true;
	} // End __construct()

	/**
	*
	* @since		3.0.0
	*/
	public function scripts_start() {
		$openstreetmap	= false;

		if ( is_array(parent::$map) && !empty(parent::$map) ) {
			$ids 		= array_values( parent::$map );
			$ids[] 	= get_the_ID();

			foreach ( $ids as $id ) {
				$openstreetmap = $openstreetmap || has_block( 'hayyabuild/openstreetmap', $id );
			}
		}

		$this->enqueue_styles();

		$this->enqueue_scripts(
			$openstreetmap
		);

	}

	/**
	* This function is provided for demonstration purposes only.
	*
	* An instance of this class should be passed to the run() function
	* defined in Plugin_Name_Loader as all of the hooks are defined
	* in that particular class.
	*
	* The Plugin_Name_Loader will then create the relationship
	* between the defined hooks and the functions defined in this
	* class.
	*
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since	1.0.0
	*/
	public function enqueue_styles() {
		wp_register_style(
			'fontawesome',
			HAYYABUILD_URL.'public/assets/vendor/fontawesome/css/all.min.css',
			[],
			HAYYABUILD_VERSION,
			'all'
		);

		wp_register_style(
			'hayyabuild',
			HAYYABUILD_URL.'public/assets/css/style.min.css',
			[ 'fontawesome' ],
			HAYYABUILD_VERSION,
			'all'
		);

		wp_enqueue_style('hayyabuild');
	} // End enqueue_styles()

	/**
	* This function is provided for demonstration purposes only.
	*
	* An instance of this class should be passed to the run() function
	* defined in Plugin_Name_Loader as all of the hooks are defined
	* in that particular class.
	*
	* The Plugin_Name_Loader will then create the relationship
	* between the defined hooks and the functions defined in this
	* class.
	*
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since	1.0.0
	*/
	public function enqueue_scripts( $openstreetmap = false ) {

		$dep = [];

		wp_register_script(
			'leaflet',
			HAYYABUILD_URL . 'public/assets/vendor/leaflet/leaflet.js',
			[],
			HAYYABUILD_VERSION,
			true
		);

		wp_register_script(
			'leaflet-markercluster',
			HAYYABUILD_URL . 'public/assets/vendor/Leaflet.markercluster/leaflet.markercluster.js',
			[ 'leaflet' ],
			HAYYABUILD_VERSION,
			true
		);

		if ( $openstreetmap ) {
			$dep[] = 'leaflet-markercluster';
		}

		wp_register_script(
			'hayyabuild',
			HAYYABUILD_URL . 'public/assets/js/hayyabuild.min.js',
			$dep,
			HAYYABUILD_VERSION,
			true
		);

		wp_enqueue_script('hayyabuild');
	} // End enqueue_scripts()

	/**
	 *
	 * Create header script.
	 *
	 * @access	 public
	 * @since	 1.0.0
	 */
	public function head_code() {

		$csseditor = $cssstyle = '';
		$post = get_post();

		if ( $post ) {
			$post_inlineStyle = get_post_meta( $post->ID, '_hayyabuild_inlinestyle', true );
			$cssstyle .= self::get_inline_style( $post_inlineStyle, $post->ID );
		}
		

		if ( is_array( parent::$settings ) && !empty( parent::$settings ) ) {
			foreach ( parent::$settings as	$item => $settings ) {
				$settings['csscode'] = ( isset($settings['csscode']) ) ? $settings['csscode'] : '';
				$style_settings		= self::hb_style($settings);
				$cssstyle			 .= "#hayyabuild-$item { $style_settings }";
				$cssstyle			 .= $settings['csscode'];

				if ( isset($settings['post_inlineStyle']) && ! empty($settings['post_inlineStyle']) ) {
					$cssstyle .= self::get_inline_style( $settings['post_inlineStyle'] );
				}
			}
			$csseditor = get_option('hayyabuild_settings');
			$csseditor = isset($csseditor['csseditor']) ? $csseditor['csseditor'] : '';

		}

		if ( ( isset($cssstyle) && ! empty($cssstyle) ) || ( isset($csseditor) && ! empty($csseditor) ) ) {
			echo '<style class="hayyabuild_inline_style">' . $csseditor . "\n" . $this->minify_script($cssstyle) . "\n" . '</style>' . "\n";
		}
	} // End head_code()

	/**
	 * Gets the inline style.
	 *
	 * @param		string	$value	The value
	 */
	public static function get_inline_style( $inline_style, $id = '' ) {
		if ( empty( $inline_style ) ) {
			return '';
		}
		$inlineStyle = (array) $inline_style;

		$style = '';
		if ( is_array($inlineStyle) ) {
			foreach ($inlineStyle as $key => $value) {
				$value = (array) $value;
				if ( isset($value['element']) && isset($value['style']) && ! empty($value['element']) && ! empty($value['style']) && is_string($value['element']) && is_string($value['style']) ) {
					$style .= $value['element'] . ' { ' . stripslashes($value['style']) . " }";
				}
			}
		}
		return $style;
	}

	/**
	 * Minify code
	 *
	 * @param		string	$script	The code
	 *
	 * @return	 sring	( description_of_the_return_value )
	 */
	private function minify_script( $code ) {
		return preg_replace(
			array( '/ {2,}/', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s' ),
			array( ' ', '' ),
			$code
		);
	}

	/**
	 * HTML output for a public
	 *
	 * @param		 string		 $output
	 */
	public static function hb_style($settings) {
		extract($settings);
		
		$style = '';

		if ( isset( $background_color ) && $background_color && $background_type !== 'background_transparent' ) {
			if ( strpos($background_color, 'gradient') !== false ) {
				$style .= 'background-image: '.$background_color.';';
			} else {
				$style .= 'background-color: '.$background_color.';';
			}
		}

		if ( isset( $background_type ) && $background_type === 'background_image') {
			$style .= 'background-image: url(\''.$background_image.'\');';
			if ( $background_repeat !== 'repeat' ) $style .= 'background-repeat: '.$background_repeat.';';
			if ( !empty( $background_effect ) && in_array('bgfixed', $background_effect)) $style .= 'background-attachment: fixed;';
			if ( $background_size !== 'auto' ) $style .= 'background-size: '.$background_size.';';

		}
		// if ( isset( $text_color ) && $text_color ) $style .= 'color: '.$text_color.';';
		if ( isset( $margin_top ) && $margin_top ) $style .= 'margin-top: '.$margin_top.'px;';
		if ( isset( $margin_bottom ) && $margin_bottom ) $style .= 'margin-bottom: '.$margin_bottom.'px;';
		if ( isset( $margin_left ) && $margin_left ) $style .= 'margin-left: '.$margin_left.'px;';
		if ( isset( $margin_right ) && $margin_right ) $style .= 'margin-right: '.$margin_right.'px;';

		if ( isset( $padding_top ) && $padding_top) $style .= 'padding-top: '.$padding_top.'px;';
		if ( isset( $padding_bottom ) && $padding_bottom ) $style .= 'padding-bottom: '.$padding_bottom.'px;';
		if ( isset( $padding_left ) && $padding_left ) $style .= 'padding-left: '.$padding_left.'px;';
		if ( isset( $padding_right ) && $padding_right ) $style .= 'padding-right: '.$padding_right.'px;';
		if ( !empty( $height) ) {
			if ($height_m_unit === 'percent') {
				$height_m_unit = 'vh';
			} else $height_m_unit = $height_m_unit;
			$style .= 'min-height: '.$height.$height_m_unit.';';
		}

		if ( isset( $text_color ) && $text_color ) $style .= 'color: '.$text_color.';';

		if ( isset( $border_top_width ) && $border_top_width ) $style .= 'border-top: '.$border_top_width.'px solid #FFF;';
		if ( isset( $border_bottom_width ) && $border_bottom_width ) $style .= 'border-bottom: '.$border_bottom_width.'px solid #FFF;';
		if ( isset( $border_left_width ) && $border_left_width ) $style .= 'border-left: '.$border_left_width.'px solid #FFF;';
		if ( isset( $border_right_width ) && $border_right_width ) $style .= 'border-right: '.$border_right_width.'px solid #FFF;';

		if (  isset( $border_color ) && $border_color ) {
			if ( strpos($border_color, 'gradient') !== false ) {
				$style .= 'border-image-source: '. $border_color . ';border-image-slice: 1;';
			} else {
				$style .= 'border-color: '. $border_color . ';';
			}
		}

		return $style;
	} // End hb_style()

} // End HayyaBuildHooks class
