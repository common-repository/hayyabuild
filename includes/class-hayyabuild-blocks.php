<?php
/**
 * The core plugin class.
*
* @since  1.0.0
* @package hayyabuild
* @subpackage hayyabuild/includes
* @author  zintaThemes <>
*/

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildBlocks' ) ) return;

class HayyaBuildBlocks
{

 /**
  * The single instance of HayyaBuild.
  * @var  object
  * @access private
  * @since  3.0.0
  */
 private static $_instance = false;

 /**
  * Is it a new version of gutenberg
  * @var  object
  * @access private
  * @since  3.0.0
  */
  private $is_new = true;

 /**
  * Define the core functionality of the plugin.
  *
  * Set the plugin name and the plugin version that can be used throughout the plugin.
  * Load the dependencies, define the locale, and set the hooks for the admin area and
  * the public-facing side of the site.
  *
  * @access  public
  * @since  1.0.0
  * @var   unown
  */
 public function __construct() {
  if ( self::$_instance ) {
   return self::$_instance;
  }

  global $wp_version;

  $this->is_new = version_compare( $wp_version, '5.7.999', '>' );

  $this->register_hayyabuild_block();

  $categories = $this->is_new ? 'block_categories_all' : 'block_categories';

  add_filter( $categories, [ $this, 'register_block_category' ], 10, 2);
  add_action('enqueue_block_editor_assets', [ $this, 'blocks_assets' ] );
  return self::$_instance = true;
 } // End __construct()

 /**
  * register hayyabuild block category
  */
 public function register_block_category( $categories, $post ) {
  return array_merge( $categories, [
   [
    'slug' => 'hayyabuild',
    'title' => esc_html('HayyaBuild', 'hayyabuild')
   ]
  ]);
 } // End register_block_category()

 /**
  * register HayyaBuild blocks }
  */
 public function register_hayyabuild_block() {
  if ( ! function_exists( 'register_block_type' ) ) {
   return;
  }

  $blocks = [ 'accordion', 'accordionpanel', 'alert', 'breadcrumb', 'button', 'buttons', 'card', 'contentbox', 'countdown', 'doublesided', 'doublesidedsides', 'featureslist', 'featureslistitem', 'gallerybox', 'godown', 'goup', 'heading', 'magicbox', 'menu', 'modalbox', 'openstreetmap', 'pagecontent', 'paragraph', 'pricingtable', 'pricingtableplan', 'progressbar', 'row', 'rowcolumn', 'search', 'separator', 'smenu', 'social', 'spacer', 'stickybox', 'tabs', 'tabstab', 'testimonial' ];

  $blocks[] = 'hayyabuildcontent';
  $blocks[] = 'templateparts';

  foreach ( $blocks as $block_name ) {
    $path = HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . $block_name . DIRECTORY_SEPARATOR;
    $block_json_file = $path . 'block.json';
    $block_php_file = $path . 'index.php';

    if ( ! file_exists( $block_json_file ) ) {
      continue;
    }

    if ( file_exists( $block_php_file ) ) {
      register_block_type_from_metadata(
        $block_json_file,
        [
          'render_callback' => function ( $attributes = [], $content = '' ) use ( $block_name, $block_php_file ) {
              $class = '';
              if (file_exists( $block_php_file )) {
                require_once $block_php_file;
                $class = 'HayyaBuild' . ucwords( $block_name );
              }
              return class_exists($class) ? ( new $class() )->render_callback( $attributes, $content ) : null;
          }
        ]
      );
    } else {
      register_block_type( 'hayyabuild/' . $block_name );
    }
  }
 } // End register_hayyabuild_block()

 /**
  * Enqueue blocks assets
  *
  * @access  public
  * @since  1.0.0
  * @var   unown
  *
  */
 public function blocks_assets() {

  $type = get_post_type();
  wp_register_script( 'leaflet', HAYYABUILD_URL . 'public/assets/vendor/leaflet/leaflet.js', [], HAYYABUILD_VERSION );

  wp_enqueue_script( 'hayyabuild-blocks',
    HAYYABUILD_URL . 'build/assets/index.js',
    [ 'leaflet', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ],
    HAYYABUILD_VERSION
  );

  $dep  = [ 'fontawesome' ];

  wp_enqueue_style(
    'fontawesome',
    HAYYABUILD_URL . 'public/assets/vendor/fontawesome/css/all.min.css',
    [],
    HAYYABUILD_VERSION
  );

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

  wp_enqueue_style(
    'hayyabuild-editor',
    HAYYABUILD_URL . 'build/assets/style-index.css',
    $dep,
    HAYYABUILD_VERSION
  );

  wp_enqueue_style(
    'hayyabuild',
    HAYYABUILD_URL . 'admin/assets/css/editor.min.css',
    [ 'hayyabuild-editor' ],
    HAYYABUILD_VERSION
  );

  if (is_rtl()) {
    wp_enqueue_style(
      'hayyabuild-rtl',
      HAYYABUILD_URL . 'admin/assets/css/rtl.min.css',
      [ 'hayyabuild' ],
      HAYYABUILD_VERSION
    );
  }

  wp_localize_script( 'hayyabuild-blocks', 'HayyaBuild', $this->js_data() );

  if ( function_exists( 'wp_set_script_translations' ) ) {
    wp_set_script_translations( 'hayyabuild-blocks', 'hayyabuild' );
  }
 }

 /**
  * set ajax dtat
  *
  * @return     array  ( description_of_the_return_value )
  */
 private function js_data() {
  global $wp_version;
  $id = get_the_ID();
  $style = (array) get_post_meta( $id, '_hayyabuild_inlinestyle', true );

  return [ 'id' => $id, 'inlineStyle' => is_array( $style ) ? $style : [], 'ajaxurl'   => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'hayyabuild-nonce' ), 'is_new' => $this->is_new, 'is_page' => isset($_GET['post']) ];
 }

} // End HayyaBuild {} class
