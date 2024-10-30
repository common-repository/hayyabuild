<?php
/**
 *
 * The admin-specific functionality of the plugin.
 *
 * @since				1.0.0
 * @package			hayyabuild
 * @subpackage	 hayyabuild/includes
 * @author			 zintaThemes <>
 */

if (! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildAdmin' )) return;

class HayyaBuildAdmin extends HayyaBuild
{
	/**
	 * The single instance of HayyaBuild.
	 * @var		 object
	 * @access	private
	 * @since		 3.0.0
	 */
	private static $_meta_instance = false;

	/**
	 * The single instance of HayyaBuild.
	 * @var		 object
	 * @access	private
	 * @since		 3.0.0
	 */
	private static $_notice_instance = false;

	/**
	 * The ID of this plugin.
	 *
	 * @since			 1.0.0
	 * @access	 private
	 * @var				string		 $plugin_name	 name of this plugin.
	 */
	private $plugin_name	 = null;

	/**
		* The version of this plugin.
		*
		* @since			 1.0.0
		* @access	 private
		* @var				string		 $version		 The current version of this plugin.
		*/
	private $version		 = null;

	/**
		* Element ID.
		*
		* @since			 1.0.0
		* @access	 private
		* @var				Intger		 $id				 Element ID.
		*/
	private $id			 = null;

	/**
		* The elements list.
		*
		* @since			 1.0.0
		* @access	 private
		* @var				string		 $version		 The current version of this plugin.
		*/
	protected static $modules = array();

	/**
		*
		* @since		 1.0.0
		* @access		protected
		* @var			 string		$type		Elements type.
		*/
	protected static $type = null;

	/**
	 *
	 */
	protected static $page = null;

	/**
		* Initialize the class and set its properties.
		*
		* @since			 1.0.0
		* @param			 string		 $plugin_name	 The name of this plugin.
		* @param			 string		 $version		 The version of this plugin.
		*/
	public function __construct() {
		class_exists('HayyaBuildAdminHooks') && new HayyaBuildAdminHooks();

		if ( 'hayyabuild' === HayyaBuildHelper::_get( 'post_type' ) ) {
			HayyaBuild::get_loader()->add_action( 'admin_init', $this, 'call_actions' );
			HayyaBuildHelper::_get( 'page' ) === 'hayyabuild_templates' && HayyaBuild::get_loader()->add_action( 'admin_init', $this, 'call_templates' );
		}
		HayyaBuild::get_loader()->add_action( 'wp_ajax_hayyabuild_inlinestyle', $this, 'save_inlinestyle' );
		HayyaBuild::get_loader()->add_action( 'admin_init', $this, 'notice', 50 );
		HayyaBuild::get_loader()->add_action( 'save_post', $this, 'save_hayyabuild_meta', 10, 2 );
		return true;
	} // End _construct()

	/**
		*
		* Load admin templates page
		*
		* @access			 private
		* @since			 1.0.0
		*/
	public function templates() {
		$file = HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-templates.php';
		$include = file_exists($file);
		$include && require_once( $file );
		HayyaBuildView::template_list_view(
			$this->templates_list(),
			$include
		);
	} // End templates()

	/**
		* Templates list.
		*
		*	@since	 1.3.0
		*/
	public function templates_list() {
		$json_file = HAYYABUILD_PATH . 'admin' . DIRECTORY_SEPARATOR . 'templates.json';
		$ext = pathinfo( $json_file, PATHINFO_EXTENSION );
		if ( file_exists($json_file) && is_file( $json_file ) && $ext === 'json' ) {

			ob_start();
			include $json_file;
			$content = ob_get_clean();

			if ( $content ) {
				$data = json_decode( $content, true );
				if ( is_array( $data ) && ! empty($data) ) {
					return $data;
				}
			}
		}
		return [];
	}

	/**
	 *
	 * Load admin help page
	 *
	 * @access			 private
	 * @since			 1.0.0
	 */
	public static function help() {
			require_once HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-help.php';
			class_exists( 'HayyaBuildHelp' ) && new HayyaBuildHelp();
	} // End hayya_help()

	/**
	 * call hayyabuild actions
	 */
	public function call_actions() {
		if ( '1' === HayyaBuildHelper::_get( 'export' ) ) {
			return $this->export();
		}

		$import = HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-import.php';

		if ( file_exists( $import ) && HayyaBuildHelper::_post( 'hayyabuild-import-submit' ) ) {
			require_once $import;
			return HayyaBuildEmport::import_save();
		}
	} // End call_actions()

	/**
	 * { function_description }
	 *
	 */
	public function notice() {
    if ( true === self::$_notice_instance	|| ! function_exists('curl_version') || ( isset($_COOKIE['hayyabuild_mesg_ver']) && $_COOKIE['hayyabuild_mesg_ver'] === '1' ) ) {
      return;
    }
		$this->important_notice();
		self::$_notice_instance = true;
	}

	/**
	 * call hayyabuild actions
	 */
	public function call_templates() {
		if ( HayyaBuildHelper::_post('tpl') ) {
			require_once HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-templates.php';
			return HayyaBuildTemplates::template_save();
		}
	} // End call_actions()

	/**
	 * Saves a meta.
	 *
	 * @param      <type>  $post_id  The post identifier
	 *
	 * @access		public
	 * @since		1.0.0
	 * @var			unown
	 *
	 */
	public function save_inlinestyle() {
		check_ajax_referer( 'hayyabuild-nonce', 'nonce' );

		$status = 'success';

		$post_id = (int) $_POST['id'];
		$style_array = (array) $this->sanitize_style( $_POST['style_array'] );

		if ( ! empty( $post_id ) ) {
			update_post_meta( $post_id, '_hayyabuild_inlinestyle', $style_array );
		} else {
			$status = 'failure';
		}

		wp_die( $status );
	}

	/**
	 * sanitize style object
	 *
	 * @param      <type>  $style  The style
	 */
	private function sanitize_style( $style ) {
		if ( empty( $style ) ) {
			return [];
		}

		$output = [];

		foreach ( $style as $key => $value ) {
			$block = sanitize_html_class($key); // unique ID for block
			$style = $value['style'];

			if ( ! empty( $value['element'] ) && ! empty( $style ) ) {
				$element 	= sanitize_text_field( $value['element'] ); // the element selector
				$output[ $block ] = [ 'element' => $element, 'style' => $style ];
			}
		}

		return $output;
	}

	/**
	 * Bedore saves a hayyabuild post type.
	 *
	 * @param			string	$post_id	The post identifier
	 *
	 * @return		 string	The post identifier
	 */
	public function save_hayyabuild_meta( $post_id, $post ) {

		if ( self::$_meta_instance ) {
			return $post_id;
		}

		self::$_meta_instance = true;

		if ( 'hayyabuild' === $post->post_type && 'trash' === $post->post_status ) {
			return $this->remove_from_map( $post_id );
		}

		// if ( isset( $_POST['hayyabuild_inlinestyle'] ) ) {
			// update_post_meta( $post_id, '_hayyabuild_inlinestyle', $this->sanitize_style($_POST['hayyabuild_inlinestyle']) );
		// }

		if ( $post->post_type === 'hayyabuild' && isset( $_POST['settings']) && isset( $_POST['hayyabuild_css'] ) ) {

			update_post_meta( $post_id, '_hayyabuild_css',
				HayyaBuildHelper::_post( 'hayyabuild_css' )
			);

			$settings = HayyaBuildHelper::_post( 'settings' );

			if ( ! $settings['type'] &&	HayyaBuildHelper::_get( 'type' ) ) {
				$settings['type'] = HayyaBuildHelper::_get( 'type' );
			}

			$type = $settings['type'] && ( 'header' === $settings['type'] || 'footer' === $settings['type'] ) ? $settings['type'] : 'content';

			if ( !isset( $settings['title'] ) || ! $settings['title'] ) {
				$settings['title'] = "HayyaBuild - {$type} - " . date("m.d.y");
			}

			// update HayyaBuild post title
			wp_update_post( [ 'ID' => $post_id, 'post_title' => $settings['title'] ] );

			update_post_meta($post_id, '_hayyabuild_settings', $settings);

		}

		! wp_is_post_autosave( $post_id ) && 'hayyabuild' === $post->post_type && $this->add_to_map( $post_id );

		return $post_id;
	} // End save_hayyabuild_meta()

	/**
	 * generate_map
	 *
	 * @param			<type>	 $post_id	The post identifier
	 *
	 * @return		 boolean	( description_of_the_return_value )
	 */
	public function add_to_map( $post_id ) {
		if ( ! $post_id ) return false;
		$settings = get_post_meta( $post_id, '_hayyabuild_settings', true );
		if ( empty($settings) || ! is_array($settings) || ! isset( $settings['type'] ) ) return $post_id;

		$type = $settings['type'];
		$pages = ! empty( $settings['pages'] ) ? $settings['pages'] : [];

		$map = (array) get_option('hayyabuild_map');
		foreach ($map as $key => $value) {
				if ( ! $value['type'] || get_post_type($key) !== 'hayyabuild' || wp_is_post_autosave($key) || empty( $value['pages'] ) ) {
					unset($map[$key]);
				}
		}
		$map[$post_id] = array( 'type' => $type, 'pages' => $pages );
		update_option('hayyabuild_map', $map);

		return $post_id;
	} // End generate_map()

	/**
	 * remove post from map
	 *
	 * @param			<type>	 $post_id	The post identifier
	 *
	 * @return		 boolean	( description_of_the_return_value )
	 */
	public function remove_from_map( $post_id ) {
		if ( ! $post_id ) return false;
		$map = (array) get_option('hayyabuild_map');
		if ( isset($map[$post_id]) ) unset( $map[$post_id] );
		update_option('hayyabuild_map', $map);

		return $post_id;
	} // End remove_from_map()

	/**
	 *
	 * @param unknown $param
	 */
	public function export() {

		$post_id = HayyaBuildHelper::_get('id');

		if ( ! $post_id || ! wp_verify_nonce( $_GET['_hbnonce'], 'export_url' ) ) return;

		$post = get_post($post_id);

		if ( ! $post ) {
			return HayyaBuildHelper::_notices(esc_html('ERROR: The post you requested was not found.', 'hayyabuild'), 'error');
		}

		if ( $post->post_type !== 'hayyabuild' ) {
			return HayyaBuildHelper::_notices(esc_html('ERROR: You can only export HayyaBuild posts.', 'hayyabuild'), 'error');
		}

		$hayyabuild_css = get_post_meta($post_id, '_hayyabuild_css', true);
		$inlinestyle = get_post_meta($post_id, '_hayyabuild_inlinestyle', true);
		$settings = get_post_meta($post_id, '_hayyabuild_settings', true);

		$name = str_replace(" ", "_", $post->post_title);
		$site_url = get_site_url();
		$json_name = 'HayyaBuild-' . $name . '-' . date("m-d-Y"); // Namming the filename will be generated.

		$post_content = str_replace($site_url, '<--site_url-->', $post->post_content);

		$style = [];
		if ( !empty($inlinestyle)) {
			foreach ( $inlinestyle as $key => $value ) {
				$style[$key]['element'] = $value->element;
				$style[$key]['style'] = str_replace($site_url, '<--site_url-->', $value->style);
			}
		}

		$inlinestyle = $style;// str_replace($site_url, '<--site_url-->', $inlinestyle);

		foreach ( $settings as $k => $v ) {
			if ( ! is_array($v) ) {
				$settings[$k] = str_replace($site_url, '<--site_url-->', $v);
			}
		}


		$data = [
			'content' => $post_content,
			'settings' => $settings,
			'inlinestyle' => $inlinestyle,
			'css' => $hayyabuild_css,
			'title' => $post->post_title,
			'post_type' => 'hayyabuild',
			'version' => HAYYABUILD_VERSION,
			'exported_in' => date("m.d.y - g:i a")
		];

		$json_file = json_encode($data); // Encode data into json data
		header("Content-Type: text/json; charset=utf8" . get_option('blog_charset'));
		header("Content-Disposition: attachment; filename=$json_name.json");
		echo $json_file;
		exit();
	} // End export()

	/**
	 * Alert Version functcion
	 *
	 * @method version_alert
	 * @param	[type]		$vars [description]
	 * @return [type]					[description]
	 */
	private function important_notice() {

    $setcookie = [
    	'expires' => time() + 3600,
    	'path' => '/',
    	'secure' => false,
    	'samesite' => false
    ];

    if( ! function_exists('get_plugin_data') ){
      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
		$data = get_plugin_data( HAYYABUILD_PATH . 'hayyabuild.php' );
		if ( !is_array($data) || empty( $data ) || !isset($data['AuthorURI']) || empty($data['AuthorURI']) ) {
			return;
		}
    $p = file_exists( HAYYABUILD_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-import.php' ) ? '1' : '0';

    $ch = curl_init();
    curl_setopt_array( $ch, [
       CURLOPT_URL => $data['AuthorURI'] . '/?zintathemes=mesg&item=hayyabuild&version=' . HAYYABUILD_VERSION . '&pro=' . $p,
       CURLOPT_TIMEOUT => 10,
       CURLOPT_RETURNTRANSFER => true,
       // CURLOPT_SSL_VERIFYPEER => false, // @TODO: remove this
       // CURLOPT_SSL_VERIFYHOST => false, // @TODO: remove this
    ]);
    $response = json_decode( curl_exec($ch), true );
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ( 200 === $httpcode && is_array($response) && ! empty($response) && ! empty($response['message']) ) {
      setcookie(
      	'hayyabuild_mesg_ver',
      	'0',
      	$setcookie['expires'],
      	$setcookie['path'],
      	'',
      	$setcookie['secure']
      );
      return self::msg(
        sprintf(
          $response['message'],
          parent::$version
        ),
        isset($response['type']) ? $response['type'] : 'info'
      );
    }
    setcookie(
    	'hayyabuild_mesg_ver',
    	'1',
    	$setcookie['expires'],
    	$setcookie['path'],
    	'',
    	$setcookie['secure']
    );
	}

} // end of class HayyaBuildAdmin
