<?php
/**
 * The core plugin class.
 *
 * This is used to define HayyaBuild metaboxes
 *
 *
 * @since		5.0.0
 * @package	hayyabuild
 * @subpackage hayyabuild/metabox
 * @author	 zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildMetaBox' ) ) return;

/**
 *
 */
class HayyaBuildMetaBox extends HayyaBuildAdmin
{

	/**
	 * Constructs a new instance.
	 */
	function __construct() {}

	/**
	 * { function_description }
	 */
	public static function settings() {

		global $post;

		if ( 'auto-draft' !== $post->post_status ) {
			$settings = get_post_meta($post->ID, '_hayyabuild_settings');
		} else {
			 $settings = 'a:23:{s:5:"title";s:0:"";s:15:"background_type";s:22:"background_transparent";s:16:"background_image";s:0:"";s:17:"background_repeat";s:6:"repeat";s:15:"background_size";s:4:"auto";s:16:"background_video";s:0:"";s:16:"background_color";s:0:"";s:10:"text_color";s:0:"";s:6:"height";s:0:"";s:13:"height_m_unit";s:2:"px";s:12:"border_color";s:0:"";s:10:"margin_top";s:0:"";s:13:"margin_bottom";s:0:"";s:11:"margin_left";s:0:"";s:12:"margin_right";s:0:"";s:16:"border_top_width";s:0:"";s:19:"border_bottom_width";s:0:"";s:17:"border_left_width";s:0:"";s:18:"border_right_width";s:0:"";s:11:"padding_top";s:0:"";s:14:"padding_bottom";s:0:"";s:12:"padding_left";s:0:"";s:13:"padding_right";s:0:"";}';
			 $settings = unserialize($settings);
		}

		$settings = isset($settings[0]) ? $settings[0] : $settings;

		$type = HayyaBuildHelper::_get('type');

		if ( empty( $type ) ) {
			$type = isset( $settings['type'] ) ? $settings['type'] : 'content';
		}

		if ( 'header' != $type && 'footer' != $type && 'content' != $type ) {
			$type = 'content';
		}

		$pages = isset( $settings['pages'] ) && ! empty( $settings['pages'] ) ? (array) $settings['pages'] : [];

		?>
		<div id="hayyabuild-settings" class="settings">

			<input type="hidden" name="settings[type]" value="<?php esc_attr_e( $type ); ?>" />

			<div class="components-base-control">
				<div class="components-base-control__field">
					<label for="hayyabuild-settings__title" class="components-base-control__label">
						<?php
						printf(
							esc_html__( '%s Title', 'hayyabuild' ),
							ucfirst( esc_html($type) )
						);
						?>
					</label>
					<input id="hayyabuild-settings__title" class="components-select-control__input" type="text" name="settings[title]" placeholder="<?php esc_attr_e( 'Title', 'hayyabuild' );?>" value="<?php echo ( isset( $settings['title']) ) ? esc_attr( $settings['title'] ) : '';?>"/>
				</div>
			</div>

			<?php self::pages( $type, $pages );?>

			<div class="accordion-container">
				<ul class="outer-border">


					<li class="control-section accordion-section">
						<h3 class="accordion-section-title" tabindex="0">
							<?php esc_html_e( 'Background Settings', 'hayyabuild' );?>
							<span class="screen-reader-text"><?php esc_html_e( 'Press return or enter to open this section', 'hayyabuild' ); ?></span>
						</h3>
						<div class="accordion-section-content">

								<div class="components-base-control">

									<div class="components-base-control__field">
										<label for="background_type_input" class="components-base-control__label">
											<?php esc_html_e( 'Background type', 'hayyabuild' );?>
										</label>
										<select id="background_type_input" name="settings[background_type]" class="components-text-control__input">
											<?php
											$background_types = array( 'background_transparent' => esc_attr__( 'Transparent', 'hayyabuild' ), 'background_image' => esc_attr__( 'Image', 'hayyabuild' ), 'background_video' => esc_attr__( 'Video', 'hayyabuild' ), 'background_color' => esc_attr__( 'Color', 'hayyabuild' ) );
											foreach ($background_types as $key => $value) {
												$selected = ( $key == HayyaBuildHelper::_empty($settings['background_type']) ) ? 'selected' : '';
												echo '<option value="' . esc_attr($key) . '" '.esc_attr($selected).'>'.esc_html($value).'</option>';
											}
											?>
										</select>
									</div>

									<div class="background_div" id="background_image">

										<div class="components-base-control__field">
											<label for="background_image_input" class="components-base-control__label">
												<?php esc_html_e( 'Background image', 'hayyabuild' );?>
											</label>
											<input id="background_image_input" type="text" name="settings[background_image]" placeholder="<?php esc_attr_e( 'Image URL', 'hayyabuild' );?>" value="<?php echo ( isset( $settings['background_image']) ) ? esc_url($settings['background_image']) : '';?>"/>
											<a id="background_image_button" class="button media-button button-primary button-large" href="#">
												<?php esc_html_e( 'Select', 'hayyabuild' );?>
												<i class="fa fa-camera"></i>
											</a>
										</div>

										<div class="components-base-control__field">
											<label for="background_repeat_input" class="components-base-control__label">
												<?php esc_html_e( 'Background repeat', 'hayyabuild' );?>
											</label>
											<select id="background_repeat_input" name="settings[background_repeat]" class="components-text-control__input">
												<?php
												$background_repeat = array('repeat' => esc_attr__( 'Repeat', 'hayyabuild' ), 'repeat-x' => esc_attr__( 'Repeat X', 'hayyabuild' ), 'repeat-y' => esc_attr__( 'Repeat Y', 'hayyabuild' ), 'no-repeat' => esc_attr__( 'No repeat', 'hayyabuild' ) );

												foreach ($background_repeat as $key => $value) {
													$selected = ( $key == HayyaBuildHelper::_empty($settings['background_repeat']) ) ? 'selected': '';
													echo '<option value="' . $key . '" ' . esc_attr($selected) . '>' . esc_html($value) . '</option>';
												}
												?>
											</select>
										</div>

										<div class="components-base-control__field">
											<label for="background_size_input" class="components-base-control__label">
												<?php esc_html_e( 'Background size', 'hayyabuild' );?>
											</label>
											<select id="background_size_input" name="settings[background_size]" class="components-select-control__input">
												<?php
												$background_size = array('auto' => esc_attr__( 'Auto', 'hayyabuild' ), 'length' => esc_attr__( 'Length', 'hayyabuild' ), 'cover' => esc_attr__( 'Cover', 'hayyabuild' ), 'contain' => esc_attr__( 'Contain', 'hayyabuild' ), 'initial' => esc_attr__( 'Initial', 'hayyabuild' ), '100% 100%' => '100% 100%' );

												foreach ($background_size as $key => $value) {
													$selected = ( $key == $settings['background_size'] ) ? 'selected': '';
													echo '<option value="' . $key . '" ' . esc_attr($selected) . '>'.esc_html($value).'</option>';
												}
												?>
											</select>
										</div>

										<div class="components-base-control__field">
											<label for="background_effect_input" class="components-base-control__label">
												<?php esc_html_e( 'Background Effects', 'hayyabuild' ); ?>
											</label>
											<select id="background_effect_input" name="settings[background_effect][]" class="components-select-control__input">
												<option value=""><?php esc_html_e( 'Disable', 'hayyabuild' );?></option>
												<?php
												$background_effect = array(
														'fixed-background' => esc_attr__( 'Fixed', 'hayyabuild' ),
														'parallax-background' => esc_attr__( 'Parallax', 'hayyabuild' ),
														// 'bgzoom' => esc_attr__( 'Zoom Effect', 'hayyabuild' )
												);
												foreach ($background_effect as $key => $value) {
													$selected = '';
													if ( isset($settings['background_effect']) && is_array($settings['background_effect']) ) {
														$selected = ( in_array( $key, $settings['background_effect'] ) ) ? ' selected' : '';
													}
													echo '<option value="'.$key.'" '.esc_attr($selected).'>'.esc_html($value).'</option>';
												}
												?>
											</select>
										</div>
									</div>

									<div class="background_div" id="background_video">
										<div class="components-base-control__field">
											<label for="background_video_input" class="components-base-control__label">
												<?php esc_html_e( 'Background Video', 'hayyabuild' );?>
											</label>
											<input id="background_video_input" type="text" name="settings[background_video]" placeholder="<?php esc_attr_e( 'Video URL', 'hayyabuild' );?>" value="<?php echo (isset($settings['background_video'])) ? esc_url($settings['background_video']) : '';?>"/>
											<a id="background_video_button" class="button media-button button-primary button-large" href="#">
												<?php esc_html_e( 'Select', 'hayyabuild' );?>
												<i class="fa fa-video-camera"></i>
											</a>
										</div>
									</div>

							</div>

						</div><!-- .accordion-section-content -->
					</li><!-- .accordion-section -->

					<li class="control-section accordion-section">
						<h3 class="accordion-section-title" tabindex="0">
							<?php esc_html_e( 'Layout Settings', 'hayyabuild' );?>
							<span class="screen-reader-text"><?php esc_html_e( 'Press return or enter to open this section', 'hayyabuild' ); ?></span>
						</h3>
						<div class="accordion-section-content">
							<div class="components-base-control">

								<div class="components-base-control__field">
									<label for="hayyabuild_height" class="components-base-control__label">
										<?php esc_html_e( 'Height', 'hayyabuild' );?>
									</label>
									<input id="hayyabuild_height" name="settings[height]" type="number" value="<?php echo (isset($settings['height'])) ? filter_var($settings['height'], FILTER_SANITIZE_NUMBER_INT) : '';?>"/>
									<select id="hayyabuild_height_m_unit" name="settings[height_m_unit]">
										<?php
										$height_fit = array( 'px' => esc_attr__( 'px', 'hayyabuild' ), 'percent' => '%', 'VH' => 'vh' );
										foreach ($height_fit as $key => $value) {
											$selected = ( $key == $settings['height_m_unit'] ) ? 'selected': '';
											echo '<option value="' . $key . '" ' . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
										}
										?>
									</select>
								</div>

								<div class="components-base-control__field">
									<?php self::spaces( (array) $settings ); ?>
								</div>

							</div>
						</div>
					</li>

				</ul>
	
				<div class="hayyabuild_color_settings">
					<input id="hayyabuild_text_color" name="settings[text_color]" type="hidden" value="<?php echo (isset($settings['text_color'])) ? $settings['text_color'] : '';?>"/>
					<input id="hayyabuild_background_color" name="settings[background_color]" type="hidden" value="<?php echo (isset($settings['background_color'])) ? $settings['background_color'] : '';?>"/>
					<input id="hayyabuild_border_color" name="settings[border_color]" type="hidden" value="<?php echo (isset($settings['border_color'])) ? $settings['border_color'] : '';?>"/>
					<div id="hayyabuild_color_settings_container"></div>
				</div>

			</div>

		</div>
		<?php
	}

	/**
	 *
	 */
	private static function spaces( array $settings = [] ) {
		?>
		<div class="hayyabuild-layout">
			<div class="margin">
				<label><?php esc_html_e('Margin', 'hayyabuild');?></label>
				<input name="settings[margin_top]" placeholder="---" value="<?php echo (isset($settings['margin_top'])) ? filter_var($settings['margin_top'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_top">
				<input name="settings[margin_bottom]" placeholder="---" value="<?php echo (isset($settings['margin_bottom'])) ? filter_var($settings['margin_bottom'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_bottom">
				<input name="settings[margin_left]" placeholder="---" value="<?php echo (isset($settings['margin_left'])) ? filter_var($settings['margin_left'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_left">
				<input name="settings[margin_right]" placeholder="---" value="<?php echo (isset($settings['margin_right'])) ? filter_var($settings['margin_right'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_right">
			</div>
			<div class="border">
				<label><?php esc_html_e('Border', 'hayyabuild');?></label>
				<input data-side="Top" name="settings[border_top_width]" placeholder="---" value="<?php echo (isset($settings['border_top_width'])) ? filter_var($settings['border_top_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_color_setting border_top_width">
				<input data-side="Bottom" name="settings[border_bottom_width]" placeholder="---" value="<?php echo (isset($settings['border_bottom_width'])) ? filter_var($settings['border_bottom_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_color_setting border_bottom_width">
				<input data-side="Left" name="settings[border_left_width]" placeholder="---" value="<?php echo (isset($settings['border_left_width'])) ? filter_var($settings['border_left_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_color_setting border_left_width">
				<input data-side="Right" name="settings[border_right_width]" placeholder="---" value="<?php echo (isset($settings['border_right_width'])) ? filter_var($settings['border_right_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_color_setting border_right_width">
			</div>
			<div class="padding">
				<label><?php esc_html_e('Padding', 'hayyabuild');?></label>
				<input name="settings[padding_top]" placeholder="---" value="<?php echo (isset($settings['padding_top'])) ? filter_var($settings['padding_top'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_top">
				<input name="settings[padding_bottom]" placeholder="---" value="<?php echo (isset($settings['padding_bottom'])) ? filter_var($settings['padding_bottom'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_bottom">
				<input name="settings[padding_left]" placeholder="---" value="<?php echo (isset($settings['padding_left'])) ? filter_var($settings['padding_left'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_left">
				<input name="settings[padding_right]" placeholder="---" value="<?php echo (isset($settings['padding_right'])) ? filter_var($settings['padding_right'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_right">
			</div>
		</div>
		<?php
	}
	/**
	 *
	 */
	private static function pages( $type = 'content', array $pages = [] ) {
		?>
		<div class="components-base-control">
			<div class="components-base-control__field">
				<label for="hayyabuild-settings__pages" class="components-base-control__label">
					<?php esc_html_e( 'Pages List', 'hayyabuild' );?>
				</label>

				<select id="hayyabuild-settings__pages" name="settings[pages][]" data-placeholder="Select Pages" class="chosen-select" multiple>
					<?php
					$all_pages = get_pages();
					$selected = '';
					if ( isset($pages) && is_array($pages) ) {
						$selected = ( $pages && in_array( 'all', $pages ) ) ? ' selected' : '';
					}
					echo '<option value="all"'.esc_attr($selected).'>'.esc_html__( 'All pages', 'hayyabuild' ).'</option>';?>

					<optgroup label="<?php esc_attr_e( 'Pages List', 'hayyabuild' )?>">
					<?php
					foreach ( $all_pages as $page ) {
						$selected = '';
						if ( isset($pages) && is_array($pages) ) {
							$selected = ( $page->ID && in_array( $page->ID, $pages ) ) ? ' selected' : '';
						}
						echo '<option value="' .esc_attr($page->ID). '"'.esc_attr($selected).'>'.esc_html($page->post_title) .'</option>';
					}
					?>
					</optgroup>

					<optgroup label="<?php esc_attr_e( 'Other Pages', 'hayyabuild' )?>">
						<?php
						$selected = $pages && in_array( '404page', $pages ) ? ' selected' : '';
						echo '<option value="404page"'.esc_attr($selected).'>'.esc_attr__( '404 Error Page', 'hayyabuild' ) . '</option>';

						if ( 'header' === $type || 'footer' === $type ) {
							echo '<option value="archive"'.esc_attr( in_array( 'archive', $pages ) ? ' selected' : '' ).'>'.esc_attr__( 'Archive Page', 'hayyabuild' ).'</option>';
							echo '<option value="search"'.esc_attr( in_array( 'search', $pages ) ? ' selected' : '' ).'>'.esc_attr__( 'Search Page', 'hayyabuild' ).'</option>';
							echo '<option value="single"'.esc_attr( in_array( 'single', $pages ) ? ' selected' : '' ).'>'.esc_attr__( 'Single Page', 'hayyabuild' ).'</option>';
							echo '<option value="tag"'.esc_attr( in_array( 'tag', $pages ) ? ' selected' : '' ).'>'.esc_attr__( 'Tag Page', 'hayyabuild' ).'</option>';
							echo '<option value="author"'.esc_attr( in_array( 'author', $pages ) ? ' selected' : '' ).'>'.esc_attr__( 'Author Page', 'hayyabuild' ).'</option>';
						}
						?>
					</optgroup>

				</select>
			</div>
		</div>
		<?php
	}

	/**
	 *
	 */
	public static function css() {
		global $post;
		$hayyabuild_css = $post->ID ? get_post_meta($post->ID, '_hayyabuild_css', true) : '';
		esc_html_e('Just write CSS code, "without syle tag".', 'hayyabuild'); ?>
		<textarea id="code_editor_page_css" rows="5" name="hayyabuild_css" class="widefat textarea"><?php echo wp_unslash( $hayyabuild_css ); ?></textarea><?php
	}
}
