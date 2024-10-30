<?php
/**
 *
 * The admin-list functionality of the plugin.
 *
 * @since			1.0.0
 * @package		hayyabuild
 * @subpackage	 hayyabuild/admin
 * @author		 zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) || class_exists( 'HayyaBuildHelp' ) ) return;

class HayyaBuildHelp extends HayyaBuildAdmin {

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
		return $this->help_view();
	}

	/**
	 *
	 * @access		public
	 * @since		1.0.0
	 * @var			 unown
	 */
	protected function help_view() {
		?>
		<div id="hayyabuild" class="wrap">
			<div class="hb-main_settings">
				<div class="view_title">
					<h3><?php esc_html_e( 'HayyaBuild', 'hayyabuild' );?> - <?php esc_html_e( 'Version', 'hayyabuild' );?> - <?php esc_html_e( HAYYABUILD_VERSION );?></h3>
				</div>
				<hr>
				<div class="accordion-container">
					<ul class="outer-border">
						<li class="control-section accordion-section  open">
							<h3 class="accordion-section-title" tabindex="0">
								<i class="fa fa-exclamation-circle"></i> <?php esc_html_e('Help', 'hayyabuild');?>
							</h3>
							<div class="accordion-section-content">
								<div class="valign hayyabuild_help">
									<section>
										<h3><?php esc_html_e('Getting Started with HayyaBuild', 'hayyabuild');?></h3>
										<div>
											<p>
												<?php esc_html_e('You can use HayyaBuild to build posts, pages, headers, footers, shortcodes or any other posts type that use Gutenberg blocks.', 'hayyabuild');?>
											</p>
											<div class="hayya_note">
												<?php esc_html_e('To build Headers or Footers and your theme doesn`t support HayyaBuild by default just contact us on our support system.', 'hayyabuild');?>
												<div>
													<a class="button-size-2" href="https://clients.zintahost.com/?help-page" target="_blank">
														<?php esc_html_e( 'Get Support Now!' , 'hayyabuild' );?>
													</a>
												</div>
											</div>
											<p>
												<?php esc_html_e( 'Or you can try Hayya Theme. Hayya theme is free multipurpose WordPress theme. It`s mobile friendly, simple, straightforward and high quality theme. Gutenberg editor compatible, easy access to all Google Fonts, Color Schemes, light-weight for faster page loading.' , 'hayyabuild' );?><br/>
												<a href="https://clients.zintahost.com/?help-page" target="_blank">
													<?php esc_html_e( 'Get Hayya Theme Now!' , 'hayyabuild' );?>
												</a>
											</p>
											<hr/>
										</div>
										<h3><?php esc_html_e('HayyaBuild How To`s', 'hayyabuild' );?></h3>
										<div>
											<?php esc_html_e('To create a new header, page layout, shortcode or footer, simply log in to your WordPress dashboard and navigate your way to the:', 'hayyabuild');?>
											<ul class="hayyabuild-features">
												<li><?php esc_html_e('To build new header go to "HayyaBuild" → "New Header".', 'hayyabuild');?></li>
												<li><?php esc_html_e('To build new footer go to "HayyaBuild" → "New Footer".', 'hayyabuild');?></li>
												<li><?php esc_html_e('To build new Content go to "HayyaBuild" → "New Content". You can select one page or group of pages or all pages but you have to add "Page Content" block to view the original content where you want.', 'hayyabuild');?></li>
												<li><?php esc_html_e('To create a new shortcode go to "HayyaBuild" → "New Content" then keep the Pages List option empty, now you can add this shortcode to any place in website.', 'hayyabuild');?></li>
												<li><?php esc_html_e('After you finished building HayyaBuild item, choose the pages you want to view the item on from "Pages List".', 'hayyabuild');?></li>
											</ul>

											<hr/>

											<h3><?php esc_html_e('Blocks Options and inline style', 'hayyabuild');?></h3>
											<p>
												<?php esc_html_e('All HayyaBuild blocks comes with "HayyaBuild Options" and "HayyaBuild Inline Style". With these options you can control a wide range of features. like:', 'hayyabuild');?>
											</p>
											<ul class="hayyabuild-features">
												<li><?php esc_html_e('Visibility Options - based on screen size.', 'hayyabuild');?></li>
												<li><?php esc_html_e('Scroll Effects - When visitors scroll down your web page, the action can be designed to trigger a variety of animation options such as fade effects, parallax, and more.', 'hayyabuild');?></li>
												<li><?php esc_html_e('Other Options - some helper options.', 'hayyabuild');?></li>
												<li><?php esc_html_e('Inline Style - Margins, Borders, Paddings, Font Size, Text Color...etc.', 'hayyabuild');?></li>
											</ul>
											<div class="hayya_note">
												<?php
													// esc_html_e('In HayyaBuild lite version some blocks doesn`t comes with all features. You have to get HayyaBuild Pro version to access all features.', 'hayyabuild'); // TODO: reactivate this
												?>
											</div>
										</div>
									</section>
								</div>
							</div>
						</li>

						<li class="control-section accordion-section open">
							<h3 class="accordion-section-title" tabindex="0">
								<i class="fa fa-mail-bulk"></i> <?php esc_html_e( 'Contact Us', 'hayyabuild');?>
							</h3>
							<div class="accordion-section-content">
								<div class="wp-block-hayyabuild-row has-2-3_1-3-columns settings valign">
									<div class="wp-block-hayyabuild-column">
										<section id="top">
											<div>
												<p>
													<strong>
														<?php esc_html_e('First of all, thank you for using HayyaBuild.', 'hayyabuild');?>
													</strong>
												</p>
												<?php HayyaBuildView::write_review( true );?>
											</div>
											<div>
												<div>
													<?php esc_html_e('Need help? You want to report a bug?', 'hayyabuild');?>
													<br />
													<?php esc_html_e('You can find us on:', 'hayyabuild');?>
												</div>
											</div>
										</section>
									</div>
									<div class="wp-block-hayyabuild-column hb-right">
										<img src="<?php echo esc_url( site_url().'/wp-content/plugins/hayyabuild/admin/assets/images/logo.png?v='.HAYYABUILD_VERSION ); ?>" />
									</div>
								</div>
								<div class="wp-block-hayyabuild-button hb-center">
									<a target="_blank" class="button-size-2" href="https://zintathemes.com/?help_page=1"><?php esc_html_e('Our Website', 'hayyabuild');?></a>
									<a target="_blank" class="button-size-2" href="https://hayyabuild.zintathemes.com/?help_page=1"><?php esc_html_e('Plugin Website', 'hayyabuild');?></a>
									<?php HayyaBuildView::social_links();?>

									<hr/>
									<h3><?php esc_html_e('Join our mailing list', 'hayyabuild');?></h3>
									<p>
										<?php esc_html_e('We would like to send you special offers, security updates and information by email. To join our mailing list, simply add your email in the box below then press enter.', 'hayyabuild');?>
										<?php esc_html_e('We Don`t Send Spam Emails and of course, you can cancel your subscription at any time; simply click on the “Unsubscribe” link in the footer of the last newsletter issue.', 'hayyabuild');?>
									</p>

									<!-- Begin Mailchimp Signup Form -->
									<div id="mc_embed_signup">
									<form action="https://zintathemes.us18.list-manage.com/subscribe/post?u=978b50f5e80e3b9cadce0b9dd&amp;id=082668c0b0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate="">
									    <div id="mc_embed_signup_scroll">
									  
									<div class="mc-field-group">
									  <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="Email Address">
									</div>
									  <div id="mce-responses" class="clear">
									    <div class="response" id="mce-error-response" style="display:none"></div>
									    <div class="response" id="mce-success-response" style="display:none"></div>
									  </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
									    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_978b50f5e80e3b9cadce0b9dd_082668c0b0" tabindex="-1" value=""></div>
									    <div class="clear" style="display: none;visibility: hidden;"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
									    </div>
									</form>
									</div>
									<script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';fnames[5]='BIRTHDAY';ftypes[5]='birthday';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
									<!--End mc_embed_signup-->

								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div><?php
	}
} // End Class
