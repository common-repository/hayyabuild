<?php
/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package	HayyaBuild
 * @subpackage hayyabuild/includes
 * @author	 ZintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildLoader' ) ) return;

class HayyaBuildLoader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since	1.0.0
	 * @access	 protected
	 * @var		array	$actions	The actions registered with WordPress to fire when the plugin loads.
	 */
	 protected $actions = [];

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since	1.0.0
	 * @access	 protected
	 * @var		array	$filters	The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters = [];

	/**
	 * The array of shortcodes registered with WordPress.
	 *
	 * @since	1.0.0
	 * @access	 protected
	 * @var		array	$shortcode	The shortcodes registered with WordPress to fire when the plugin loads.
	 */
	protected $shortcode = [];

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since	1.0.0
	 */
	public function __construct() {
		$this->load_libs();
		$this->add_action('init', $this, 'register_hooks');
		$this->add_action('plugins_loaded', 'HayyaBuild', 'update');
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since			 1.0.0
	 * @param			 string				 $hook			 The name of the WordPress action that is being registered.
	 * @param			 object				 $component		A reference to the instance of the object on which the action is defined.
	 * @param			 string				 $callback		 The name of the function definition on the $component.
	 * @param			 int		Optional	$priority		 The priority at which the function should be fired.
	 * @param			 int		Optional	$accepted_args	The number of arguments that should be passed to the $callback.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since		1.0.0
	 * @param		 string				 $hook			 The name of the WordPress filter that is being registered.
	 * @param		 object				 $component		A reference to the instance of the object on which the filter is defined.
	 * @param		 string				 $callback		 The name of the function definition on the $component.
	 * @param		 int		Optional	$priority		 The priority at which the function should be fired.
	 * @param		 int		Optional	$accepted_args	The number of arguments that should be passed to the $callback.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new shortcode to the collection to be registered with WordPress.
	 *
	 * @since			 1.0.0
	 * @param			 string				 $shortcode		 The key of the WordPress shortcode that is being registered.
	 * @param			 object				 $component		 A reference to the instance of the object on which the shortcode is defined.
	 * @param			 string				 $callback		The name of the function definition on the $component.
	 */
	public function add_shortcode($hook, $component, $callback, $priority = null, $accepted_args = null ) {
		$this->shortcode = $this->add( $this->shortcode, $hook, $component, $callback );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since			1.0.0
	 * @access		 private
	 * @param			array				$hooks			The collection of hooks that is being registered (that is, actions or filters).
	 * @param			string				 $hook			 The name of the WordPress filter that is being registered.
	 * @param			object				 $component		A reference to the instance of the object on which the filter is defined.
	 * @param			string				 $callback		 The name of the function definition on the $component.
	 * @param			int		Optional	$priority		 The priority at which the function should be fired.
	 * @param			int		Optional	$accepted_args	The number of arguments that should be passed to the $callback.
	 * @return		 type									 The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$hooks[] = [ 'hook' => $hook, 'component' => $component, 'callback' => $callback, 'priority' => $priority, 'accepted_args' => $accepted_args ];
		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since	1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) add_filter( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		foreach ( $this->actions as $hook ) add_action( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		foreach ( $this->shortcode as $hook ) add_shortcode($hook['hook'], [ $hook['component'], $hook['callback'] ]);
		return true;
	}

	/**
	 * HayyaBuild blocks category
	 * 
	 */
	public function register_hooks() {
		class_exists('HayyaBuildBlocks') && new HayyaBuildBlocks();

		$labels = [
			'name'			=> esc_attr_x('HayyaBuild', 'WordPress Layout Builder', 'hayyabuild'),
			'singular_name' => esc_html('HayyaBuild' , 'hayyabuild'),
			'add_new' => esc_html('New Content' , 'hayyabuild'),
		];

		$rewrite = [
			'slug'				=> '',
			'with_front'	=> false,
			'pages'			 => false,
			'feeds'			 => false,
		];

		register_post_type(
			'hayyabuild',
			[
				'labels'			 => $labels,
				'description'		=> esc_html('HayyaBuild allows you to build unlimited headers, pages and footers for your WordPress website without the needs for writing any code..', 'hayyabuild'),
				'public'			 => true,
				'exclude_from_search'=> true,
				'administrator'		=> 'manage_options',
				'has_archive'		=> false,
				'hierarchical'		 => false,
				'taxonomies'		 => [ 'hayyabuild' ],
				'show_in_nav_menus'	=> false,
				'publicly_queryable' => false,
				'show_in_menu'		 => true,
				'show_in_admin_bar'	=> false,
				// 'show_ui' => false,
				// 'rewrite'						=> $rewrite,
				'menu_icon'			=> HAYYABUILD_URL.'admin/assets/images/menu_icon.png',
				'show_in_rest'		 => true,
				'supports'			 => [ 'editor', 'custom-fields' ],
			]
		);

	} // End register_hooks()

	/**
	 *
	 * Loads libs.
	 * 
	 */
	private function load_libs() {
		$libs = [
			'includes'	=> [ 'activator', 'deactivator', 'i18n', 'update', 'helper', 'blocks', 'exception' ],
			'admin'	=> [ 'admin', 'adminhooks', 'view' ],
			'public' => [ 'public', 'hooks', 'shortcodes', 'view' ],
		];

		foreach ( array_keys($libs) as $lib ) {

			if ( 'includes' === $lib ||
				 ( 'public' !== $lib && is_admin() ) ||
				 ( 'public' === $lib && ! is_admin() )
			) {
				foreach ( $libs[$lib] as $name ) {
					$file = HAYYABUILD_PATH . $lib . DIRECTORY_SEPARATOR . 'class-hayyabuild-' . $name . '.php';
					file_exists( $file ) && require_once( $file );
				}
			}

		}

		return true;
	}

}
