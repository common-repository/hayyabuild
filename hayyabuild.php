<?php
/**
 * Plugin Name:     HayyaBuild
 * Plugin URI:      https://hayyabuild.zintathemes.com
 * Description:     WordPress  headers, Pages and Footers builder plugin, It's comes with the most advanced Gutenberg blocks.
 * Author:          ZintaThemes
 * Author URI:      https://zintathemes.com/
 * Version:         3.0.2
 * License:         GPL2+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @link
 * @since             1.0.0
 * @package           HayyaBuild
 * @category          *
 * @author            ZintaThemes
 */

! defined('ABSPATH') && die('This file cannot be accessed directly');

// Define HayyaBuild constants
defined('HAYYABUILD_VERSION') || define('HAYYABUILD_VERSION', '3.0.2' );
defined('HAYYABUILD_PATH')     || define('HAYYABUILD_PATH', plugin_dir_path(__FILE__) );
defined('HAYYABUILD_URL')     || define('HAYYABUILD_URL', plugin_dir_url(__FILE__) );

/**
 *
 * This class describes a hayya build start.
 *
 * @since             1.0.0
 * @package           HayyaBuild
 */
final class HayyaBuildStart
{

  /**
   * The single instance of HayyaBuild.
   * @var         object
   * @access      private
   * @since         3.0.0
   */
  private static $_instance = false;

  /**
   * Constructor function.
   * @access      public
   * @since       3.0.0
   */
  public function __construct() {
    self::$_instance = true;
    require HAYYABUILD_PATH . 'includes' . DIRECTORY_SEPARATOR . 'class-hayyabuild.php';
    register_activation_hook( __FILE__, [ 'HayyaBuild', 'hayyabuild_activate' ] );
    register_deactivation_hook( __FILE__, [ 'HayyaBuild', 'hayyabuild_deactivate' ] );
  } // End __construct()

  /**
   * Begins execution of the plugin.
   *
   * @access      public
   * @since       3.0.0
   * @param       $type       string
   */
  public static function hayya_starter( $name = null, $type = null ) {
    ! self::$_instance && new self();
    return HayyaBuild::run(
      null === $type && ( 'header' === $name || 'footer' === $name ) ? $name : $type
    );
  } // End hayya_starter()
} // End HayyaBuildStart {} Class

/**
 * Begins execution of the plugin.
 *
 * @since       5.0.0
 * @param       $type       string
 */
function hayyabuild( $type = null ) {
  return HayyaBuildStart::hayya_starter(
    basename(
      debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 )[0]['file'], '.php'
    ),
    $type
  );
} // End hayyabuild()

// Run HayyaBuild plugin
hayyabuild();
