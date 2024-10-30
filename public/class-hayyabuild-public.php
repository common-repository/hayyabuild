<?php
/**
 * Public output class
 *
 *
 * @package	hayyabuild
 * @subpackage hayyabuild/public
 * @author	 zintaThemes
 *
 *
 */

if ( ! defined('ABSPATH') || class_exists('HayyaBuildPublic') ) return;

class HayyaBuildPublic extends HayyaBuild
{

	/**
	 * The single instance of HayyaBuild.
	 * @var	 object
	 * @access	private
	 * @since	 3.0.0
	 */
	private static $_instance = false;

	/**
	 * setting array for curent element.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string	$settings	
	 */
	protected static $settings = [];

	/**
	 * setting array for curent element.
	 *
	 * @since	1.0.0
	 * @access	protected
	 * @var		string	$map	
	 */
	protected static $map = [];

	/**
	 *
	 * @since	1.0.0
	 * @access	public
	 * @var		string	$output	
	 */
	public static $output = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since		1.0.0
	 * @param			string	$plugin_name		The name of the plugin.
	 * @param			string	$version	The version of this plugin.
	 */
	public function __construct( $type ) {
		if ( ($type === 'footer' || $type === 'header') && self::$_instance && has_filter('hayyabuild_output') ) {
			return self::$output = !!apply_filters( 'hayyabuild_output', $type );
		}
		if ( ! self::$_instance ) {
			$this->start_hooks();
		}
	}

	/**
	 * Starts hooks.
	 *
	 * @return	 boolean	run this for one time
	 */
	private function start_hooks() {
		$hooks = new HayyaBuildHooks();
		$shortcode = new HayyaBuildShortcode();
		HayyaBuild::get_loader()->add_action('template_redirect', $this, 'get_hayyabuild');
		HayyaBuild::get_loader()->add_filter('hayyabuild_output', $this, 'output');
		HayyaBuild::get_loader()->add_filter('the_content', 'HayyaBuildPublic', 'pages_content');
		return self::$_instance = true;
	}

	/**
	 *
	 * @package		HayyaBuild
	 * @access		public
	 * @since		3.0.0
	 */
	public function get_hayyabuild() {
		$this->get_settings();
		$this->load_templates();
	} // End get_hayyabuild()

	/**
	 * Get settings.
	 *
	 * @package		HayyaBuild
	 * @access		 public
	 * @since			1.0.0
	 */
	public function get_settings() {
		if ( empty( self::$map ) ) {
			self::$map = $this->get_map();
		}

		$itemSettings = [];

		if ( is_array(self::$map) && !empty(self::$map) ) {
			foreach( self::$map as $type => $id ) {
				$setting = [];
				$post = get_post( $id );

				if ( is_object( $post ) && 'publish' === $post->post_status && 'hayyabuild' === $post->post_type ) {
					$setting = get_post_meta( $post->ID, '_hayyabuild_settings', true );
					$setting['csscode'] = get_post_meta( $post->ID, '_hayyabuild_css', true );
					$setting['post_inlineStyle'] = get_post_meta( $post->ID, '_hayyabuild_inlinestyle', true );
					$setting['post_content'] = $post->post_content;
				}
				$itemSettings[ $type ] = $setting;
			}

			self::$settings = $itemSettings;
		}

		return self::$settings;

	} // End	get_settings()

	/**
	 *
	 * @package		HayyaBuild
	 * @access		public
	 * @since		1.0.0
	 */
	public function get_map() {
		$hayyabuild_map = $type = [];
		$option_map = get_option('hayyabuild_map');

		if ( ! is_array( $option_map ) || empty( $option_map ) ) {
			return $option_map;
		}

		foreach ( $option_map as $id => $value ) {
			if (
				isset($value['type']) &&
				$value['pages'] &&
				(
					! in_array( $value['type'], $type ) ||
					! in_array( 'all', $value['pages'] )
				) &&
				(
					is_array( $value['pages'] ) &&
					(
						in_array( $this->page_now(), $value['pages'] ) ||
						in_array( 'all', $value['pages'] )
					)
				)
			) {
				$hayyabuild_map[$value['type']] = $id;
				$type[] = $value['type'];
			}
		}

		if ( HayyaBuildHelper::_current_user() && 'preview' === HayyaBuildHelper::_get('hayyabuild') ) {
			$hayyabuild_map = $this->preview( $hayyabuild_map );
		}

		return $hayyabuild_map;
	} // End	get_map()

	/**
	 * get page name
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public function page_now() {
		$page = 'all';
		if ( is_home() || is_category() ) {
			$page = get_option( 'page_for_posts' );
		} else if ( function_exists('is_shop') && is_shop() ) {
			$page = get_option('woocommerce_shop_page_id');
		} else if ( is_404() ) {
			$page = '404page';
		} else if ( is_archive() && ! is_tag() ) {
			$page = 'archive';
		} else if ( is_search() ) {
			$page = 'search';
		} else if ( is_single() ) {
			$page = 'single';
		} else if ( is_tag() ) {
			$page = 'tag';
		} else if ( is_author() ) {
			$page = 'author';
		} else if ( get_the_ID() ) {
			$page = get_the_ID();
		}
		return $page;
	}

	/**
	 * preview
	 *
	 * @param      <type>  $map      The map
	 * @param      <type>  $header   The header
	 * @param      <type>  $content  The content
	 * @param      <type>  $footer   The footer
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function preview( $map ) {
		$header 	= HayyaBuildHelper::_get('header');
		$content 	= HayyaBuildHelper::_get('content');
		$footer 	= HayyaBuildHelper::_get('footer');
		$list 		= HayyaBuildHelper::_content_type();

		foreach ( $list as $key ) {
			if ( $$key && 'hayyabuild' === get_post_type($$key) ) {
				$setting = get_post_meta( $$key, '_hayyabuild_settings', true );
				if ( $setting['type'] && $key === $setting['type'] ) {
					$map[$key] 	= $$key;
				}
			}
		}
		return $map;
	}

	/**
	 * HayyaBuild output
	 *
	 * @param		string	 $type	 The type
	 *
	 * @return	 boolean	true
	 *
	 * @access	 private
	 * @since		1.0.0
	 */
	public function output( $type = null ) {

		if ( ! $type || ! isset( self::$settings[$type] ) ) {
			return false;
		}
		$content = $id = $style = $class = $attributes = $settings = '';
		extract( self::$settings[$type] );

		if ( ! isset($post_content)) {
			return false;
		}

		if ( !empty($attributes) ) {
			$attributes = ' ' . $attributes;
		}

		$content = $post_content;
		$id = 'hayyabuild-'.$type;
		if ( isset($background_type) ) {
			$background_effect = isset($background_effect) ? $background_effect : [];
			$class .= self::background_effect($background_type, $background_effect);
			if ( $background_type === 'background_video' && $background_video ) {
				$fixed_video = isset($fixed_video) ? $fixed_video : '';
				$content = HayyaBuildView::video_background( $type, $content, $background_video, $fixed_video, $background_image );
			}
		}

		if (isset($scroll_effect) && ! empty($scroll_effect)) {
			$class .= ' hayya-scrolleffects';
			foreach ($scroll_effect as $value) {
				if ($value) $class .= " $value";
			}
		}

		echo self::parse_output(
			HayyaBuildView::content( $content, $type, $id, $class, $attributes )
		);

		return true;
	} // End output()

	/**
	 * setup background
	 *
	 * @param		string	$background_type	The background type
	 * @param		array	 $background_effect	The background effect
	 * 
	 * @return	 string	class name
	 * 
	 * @since		5.0
	 * @access	 private
	 */
	public static function background_effect( $background_type = null,	$background_effect = [] ) {
		$class = '';
		if (( 'background_video' === $background_type || 'background_image' === $background_type ) && !empty($background_effect) ) {
			foreach ( $background_effect as $effect ) {
				if ( ! empty( $effect ) ) {
					if ( $effect === 'parallax-background' ) {
						$class .= ' hayya-scrolleffects hb-parallax-up';
					} else if ( $effect === 'fixed-background' ) {
						$class .= ' hayyabuild-fixed-background';
					} else {
						$class .= ' ' . $effect;
					}
				}
			}
		}
		return $class;
	}

	/**
	 * Pages Content.
	 *
	 * @package		HayyaBuild
	 * @access		public
	 * @since		3.0.0
	 */
	public static function pages_content( $page_content = null ) {
		if ( is_home() || is_archive() || is_search() || is_single() || is_tag() || is_date() ) {
			return $page_content;
		}
		if ( ! empty( self::$settings ) && isset( self::$settings['content'] ) && isset( self::$settings['content']['post_content'] ) ) {
			$page_content = self::$settings['content']['post_content'];
		}
		return do_shortcode( do_blocks( self::parse_output($page_content) ));
	} // End pages_content()

	/**
	 * parse hayyabuild output
	 *
	 * @param		string	$content	The content
	 *
	 * @return	 string	( description_of_the_return_value )
	 */
	public static function parse_output( $content = null ) {
		return $content ? do_shortcode( do_blocks( $content ) ) : '';
	}

	/**
	 *	reterned page contetn
	 *	@method	page_output
	 *	@param	 string		 $content	[description]
	 *	@return	string					 [description]
	 *
	 *	@package		HayyaBuild
	 *	@access		public
	 *	@since		3.0.0
	 */
	public static function page_output($content, $do_shortcode = false, $id = null) {
		$prefix = (null === $id) ? '' : '_' . $id;
		if ($do_shortcode) $content = do_shortcode($content);
		return '<div id="hayyabuild-container-content-' . $id . '" class="hayyabuild-container-content"><div id="hayyabuild-content-' . $id . '" class="hayyabuild-content">' . $content . '</div></div>';
	}

	/**
	 * 
	 * Loads templates.
	 * 
	 */
	private function load_templates() {
		$template = '';
		if ( is_404() ) {
			$template = '404';
		}
		$template && add_filter( "{$template}_template", function () use ( $template ) {
			return HAYYABUILD_PATH . 'public' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . '.php';
		});
	}

	/**
	 * 
	 * pages templates
	 * 
	 */
	public static function page_templates() {
		if ( isset(self::$settings) && isset(self::$settings['content']) && isset(self::$settings['content']['post_content']) ) {
			echo self::$settings['content']['post_content'];
		}
	}

} // End HayyaBuildPublic {} class
