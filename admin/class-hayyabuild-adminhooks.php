<?php
/**
 *
 * HayyaBuild Admin Scripts functionality of the plugin.
 *
 * @since		1.0.0
 * @package		hayyabuild
 * @subpackage	 hayyabuild/admin
 * @author		 zintaThemes <>
 */

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildAdminHooks' )) return;

class HayyaBuildAdminHooks extends HayyaBuildAdmin
{

	/**
	* Initialize the class and set its properties.
	*
	* @since	3.0.0
	* @param		string	$plugin_name		 The name of the plugin.
	* @param		string	$version	The version of this plugin.
	*/
	public function __construct() {
		HayyaBuild::get_loader()->add_action( 'admin_init', $this, 'posts_list' );
		HayyaBuild::get_loader()->add_action('admin_menu', $this, 'admin_menus');
		HayyaBuild::get_loader()->add_action('add_meta_boxes', $this, 'register_meta_boxes');
		HayyaBuild::get_loader()->add_action('plugins_loaded', $this, 'scripts_start');
		return true;
	} // End __construct()

	/**
	 *
	 * @access		public
	 * @since		 1.0.0
	 * @var		 unown
	 */
	// public function register_settings() {
	// 	register_setting( 'hayyabuild-settings-group', 'hayyabuild-options' );
	// }

	/**
	 *
	 * @since	3.0.0
	 */
	public function scripts_start() {
		add_action('admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action('admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	} // End scripts_start()

	/**
	 * Register the stylesheets for the admin area.
	 *
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
	 * @since	 1.0.0
	 */
	public static function enqueue_styles() {
		self::register_style();

		if ( isset($_GET['post_type']) && $_GET['post_type'] === 'hayyabuild' ) {
			wp_enqueue_style('hayyabuild');
		}
		
		// if ( isset($_GET['postType']) || isset($_GET['post_type']) && $_GET['post_type'] === 'hayyabuild' ) {
		// 	wp_enqueue_style('hayyabuild');
		// }
		// if (is_rtl()) wp_enqueue_style('hayyabuild-rtl');
	} // End enqueue_styles()

	/**
	 * Register the JavaScript for the admin area.
	 *
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
	 * @access	 public
	 * @since	 1.0.0
	 */
	public static function enqueue_scripts() {
		self::register_script();
		self::post_type();
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		wp_enqueue_code_editor( [ 'type' => 'text/html' ] );
		wp_enqueue_script( 'hayyabuild' );
	} // End enqueue_scripts()

	/**
	 * Posts a type.
	 *	@method post_type
	 *
	 *	@since	 5.4.0
	 *	@access public
	 */
	public static function post_type() {
		global $pagenow;
		if ('post-new.php' === $pagenow && isset($_GET['type']) && ( 'header' === $_GET['type'] || 'content' === $_GET['type'] || 'footer' === $_GET['type'] ) ) {
			$type = wp_unslash( $_GET['type'] );
		} elseif ( 'post.php' === $pagenow && isset($_GET['post']) ) {
			$post_id = wp_unslash( $_GET['post'] );
			$settings = get_post_meta( $post_id, '_hayyabuild_settings', true );
			$type = isset( $settings['type'] ) ? $settings['type'] : 'content';
		}
		if ( isset( $type ) && !empty( $type ) ) {
			wp_localize_script( 'hayyabuild-blocks', 'hayyabuild_type', [ $type ] );
		}
	}

	/**
	 * register hayyabuild metaboxes
	 */
	public function register_meta_boxes() {

		require_once HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-metabox.php';

		$post_types = [ 'post', 'page', 'event', 'hayyabuild' ];

		foreach( $post_types as $post_type ) {
			add_meta_box(
				'_hayyabuild_inlinestyle',
				esc_html__('HayyaBuild Inline Style', 'hayyabuild'),
				function () {
					echo '<input id="hayyabuild_inline_style" type="text" name="hayyabuild_inlinestyle" value=""/>';
				},
				$post_type
			);
		}

		add_meta_box(
			'_hayyabuild_settings',
			esc_html__('HayyaBuild Settings', 'hayyabuild'),
			function () {
				HayyaBuildMetaBox::settings();
			},
			'hayyabuild',
			'side'
		);

		add_meta_box(
			'_hayyabuild_css',
			esc_html__('HayyaBuild CSS', 'hayyabuild'),
			function () {
				HayyaBuildMetaBox::css();
			},
			'hayyabuild',
			'advanced'
		);

	}

	/**
	 * register CSS style
	 *
	 * @access	 public
	 * @since	 3.0.0
	 */
	public static function register_style() {

		global $wp_styles;
		$type = get_post_type();
		$dep 	= [ 'fontawesome' ];

		if ( 'hayyabuild_templates' === HayyaBuildHelper::_get('page') || 'hayyabuild' === $type ) {
			$dep[] = 'chosen';
			wp_register_style(
				'chosen',
				HAYYABUILD_URL.'admin/assets/vendor/chosen/chosen.css',
				[],
				HAYYABUILD_VERSION,
				'all'
			);
		}

    wp_register_style(
			'fontawesome',
			HAYYABUILD_URL . 'public/assets/vendor/fontawesome/css/all.min.css',
			[],
			HAYYABUILD_VERSION,
			'all'
		);

		wp_register_style(
			'hayyabuild',
			HAYYABUILD_URL . 'admin/assets/css/editor.min.css',
			$dep,
			HAYYABUILD_VERSION,
			'all'
		);

		if (is_rtl()) {
			wp_register_style(
				'hayyabuild-rtl',
				HAYYABUILD_URL . 'admin/assets/css/rtl.min.css',
				[ 'hayyabuild' ],
				HAYYABUILD_VERSION,
				'all'
			);
		}
	} // End register_style()

	/**
	 *
	 *
	 * @access	 public
	 * @since	 3.0.0
	 */
	public static function register_script() {
		$type = get_post_type();
		if ( 'hayyabuild_templates' === HayyaBuildHelper::_get('page') || 'hayyabuild' === $type ) {
			wp_enqueue_script(
				'chosen',
				HAYYABUILD_URL . 'admin/assets/vendor/chosen/chosen.jquery.min.js',
				[ 'accordion', 'wp-color-picker' ],
				HAYYABUILD_VERSION
			);
		}
		wp_register_script(
			'hayyabuild',
			HAYYABUILD_URL.'admin/assets/js/admin-script.min.js',
			[ 'accordion' ],
			HAYYABUILD_VERSION,
			true
		);
	}

	/**
	 *
	 * Post List
	 *
	 */
	public function posts_list() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
		add_action( 'load-edit.php', [ $this, 'load_edit' ] );
		add_filter( 'manage_hayyabuild_posts_columns', [ $this, 'posts_columns' ] );
		add_filter( 'manage_hayyabuild_posts_custom_column', [ $this, 'custom_column' ], 10, 2 );
		add_filter( 'post_row_actions', [ $this, 'row_action' ], 10, 2 );
		add_filter( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
		add_action( 'restrict_manage_posts', [ $this, 'restrict_manage_posts' ] );
	}

	/**
	 *
	 * Loads an edit.
	 *
	 */
	public function load_edit(){
		return;
		$screen = get_current_screen();
		if( 'edit-hayyabuild' === $screen->id ) {
			add_action( 'in_admin_footer', [ 'HayyaBuildView', 'write_review' ]);
		}
	}

	/**
	 * Add the custom columns to the book post type:
	 *
	 * @param      <type>  $columns  The columns
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function posts_columns( $columns ) {
		return [
			'cb' 		=> $columns['cb'],
			'title' => $columns['title'],
			'type' 	=> __( 'Type', 'hayyabuild' ),
			'pages' => __( 'Pages List', 'hayyabuild' ),
			'date' 	=> $columns['date'],
		];
	}

	/**
	 * Add the custom columns to the book post type:
	 *
	 * @param      string  $column_name  The column name
	 * @param      <type>  $post_id      The post identifier
	 */
	public function custom_column( $column_name, $post_id ) {
		$settings = get_post_meta( $post_id, '_hayyabuild_settings', true );
		$type = isset($settings['type']) && ! empty($settings['type']) ? $settings['type'] : 'content';

		if ( $column_name === 'type' ) {
			?>
			<a href="<?php echo esc_url('edit.php?post_type=hayyabuild&amp;type='.$type);?>">
			  <?php esc_html_e(strtolower($type));?>
			</a>
			<?php
		}
		if ( $column_name === 'pages' && ! empty($settings['pages']) ) {
			if ( $settings['pages'][0] === 'all' ) {
				esc_html_e( 'All', 'hayyabuild' );
			} else {
				$prefix = '';
				foreach ( $settings['pages'] as $id ) {
					echo $prefix;
					if ( is_numeric( $id ) ) {
						echo '<span class="hayyabuild-pages-list">' . esc_html( get_the_title($id) ) . '</span>';
					} else {
						switch ( $id ) {
							case '404page':
								esc_html_e( '404 Error Page', 'hayyabuild' );
								break;

							case 'archive':
								esc_html_e( 'Archive Page', 'hayyabuild' );
								break;

							case 'search':
								esc_html_e( 'Search Page', 'hayyabuild' );
								break;

							case 'single':
								esc_html_e( 'Single Page', 'hayyabuild' );
								break;

							case 'tag':
								esc_html_e( 'Tag Page', 'hayyabuild' );
								break;

							case 'author':
								esc_html_e( 'Author Page', 'hayyabuild' );
								break;
						}
					}
					$prefix = ', ';
				}
			}
		}
	}

	/**
	 *
	 * { item_description }
	 *
	 */
	public function admin_notices() {
		'edit-hayyabuild' === get_current_screen()->id && HayyaBuildView::admin_notices();
	}

	/**
	 * { function_description }
	 *
	 * @param      <type>  $actions  The actions
	 * @param      <type>  $post     The post
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function row_action( $actions, $post ) {
		if ( $post->post_type !== 'hayyabuild' ) {
      return $actions;
    }
		$settings = get_post_meta( $post->ID, '_hayyabuild_settings', true );
		$export = '<i class="fa fa-file-export"></i> <a href="' . wp_nonce_url( admin_url( 'edit.php?post_type=hayyabuild&amp;id='.$post->ID.'&amp;export=1' ) , 'export_url' ,'_hbnonce' ) . '">'.esc_html__('Export', 'hayyabuild').'</a>';

		if ( 'trash' !== $post->post_status ) {
			$tools = [
				'edit' => '<i class="fa fa-edit"></i> ' . $actions['edit'],
				'export' => $export,
				'trash' => '<i class="fa fa-trash-alt"></i> '.$actions['trash'],
			];
			if ( ( ! isset( $settings['pages'] ) || empty( $settings['pages'] ) ) && 'publish' === $post->post_status ) {
				$tools['shortcode'] = '<nobr><span>' . esc_html__('Shortcode', 'hayyabuild') . ': <code class="hayyabuild-copy-shortcode" data-title="'. esc_attr( 'Copy', 'hayyabuild' ) .'">[hayyabuild id="' . esc_attr( $post->ID ) . '"]</code></span></nobr>';
			}
		} else {
			$tools = $actions;
			$tools['export'] = $export;
		}
		return $tools;
	}

	/**
	 * { function_description }
	 *
	 * @param      <type>  $query  The query
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function pre_get_posts( $query ) {
		$screen = get_current_screen();
		$type = HayyaBuildHelper::_get('type');
		$length = strlen($type);
		$types = [ 'header', 'content', 'footer' ];
		$types = HayyaBuildHelper::_content_type();

		if( isset($screen->post_type) && $screen->post_type === 'hayyabuild' && $screen->base === 'edit' && ! empty( $type ) && in_array($type, $types)) {
			$query->set('meta_query', [
				[
					'key' => '_hayyabuild_settings',
					'value' => '"type";s:' . $length . ':"' . $type . '";',
					'compare' => 'LIKE',
				]
			]);
		}
		return $query;
	}

	/**
	 *
	 * { function_description }
	 *
	 */
	public function restrict_manage_posts() {
	  $screen = get_current_screen();
	  global $wp_query;

		if ( $screen->post_type === 'hayyabuild' ) {
			$types = [
				'header' 		=> __( 'Headers', 'hayyabuild' ),
				'content' 	=> __( 'Contents', 'hayyabuild' ),
				'footer' 		=> __( 'Footers', 'hayyabuild' )
			];
			$current_plugin = HayyaBuildHelper::_get('type');
			?>
			<select name="type" id="type">
				<option value="all" <?php selected( 'all', $current_plugin ); ?>><?php esc_html_e( 'All HayyaBuild', 'hayyabuild' ); ?></option>
				<?php foreach( $types as $type => $name ) { ?>
					<option value="<?php esc_attr_e( $type ); ?>" <?php selected( $type, $current_plugin ); ?>><?php esc_attr_e( $name ); ?></option>
				<?php } ?>
			</select><?php
		}
	}

	/**
	 *
	 * Create admin menus and pages.
	 *
	 * @access	 public
	 * @since	 1.0.0
	 */
	public function admin_menus() {
		$parent = new HayyaBuildAdmin();
		$file = HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-import.php';
		if ( function_exists( 'register_block_type' ) ) {
			remove_submenu_page( 'edit.php?post_type=hayyabuild', 'post-new.php?post_type=hayyabuild' );
			add_submenu_page( 'edit.php?post_type=hayyabuild', __('New Header', 'hayyabuild'), __('New Header', 'hayyabuild'), 'manage_options', 'post-new.php?post_type=hayyabuild&type=header' );
			add_submenu_page( 'edit.php?post_type=hayyabuild', __('New Footer', 'hayyabuild'), __('New Footer', 'hayyabuild'), 'manage_options', 'post-new.php?post_type=hayyabuild&type=footer' );
			add_submenu_page( 'edit.php?post_type=hayyabuild', __('New Content', 'hayyabuild'), __('New Content', 'hayyabuild'), 'manage_options', 'post-new.php?post_type=hayyabuild&type=content' );
			if ( file_exists( $file ) ) {
				add_submenu_page( 'edit.php?post_type=hayyabuild', __('HayyaBuild Templates', 'hayyabuild'), __('Templates', 'hayyabuild'), 'manage_options', 'hayyabuild_templates', [ $this, 'templates' ] );
			}
		}
		add_submenu_page( 'edit.php?post_type=hayyabuild', __('HayyaBuild Help', 'hayyabuild'), __('Help', 'hayyabuild'), 'manage_options', 'hayyabuild_help', [ 'HayyaBuildAdmin', 'help' ] );
	} // End admin_menus()
} // End Class
