<?php
/**
 * Futurio Storefront admin notify
 *
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Futurio_Storefront_Notify_Admin' ) ) :

	/**
	 * The Futurio Storefront admin notify
	 */
	class Futurio_Storefront_Notify_Admin {

		/**
		 * Setup class.
		 *
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 99 );
			add_action( 'wp_ajax_futurio_storefront_dismiss_notice', array( $this, 'dismiss_nux' ) );
			add_action( 'admin_menu', array( $this, 'add_menu' ), 5 );
		}

		/**
		 * Enqueue scripts.
		 *
		 */
		public function enqueue_scripts() {
			global $wp_customize;

			if ( isset( $wp_customize ) || futurio_storefront_is_extra_activated() ) {
				return;
			}

			wp_enqueue_style( 'futurio-admin', get_template_directory_uri() . '/css/admin/admin.css', '', '1' );

			wp_enqueue_script( 'futurio-admin', get_template_directory_uri() . '/js/admin/admin.js', array( 'jquery', 'updates' ), '1', 'all' );

			$futurio_storefront_notify = array(
				'nonce' => wp_create_nonce( 'futurio_storefront_notice_dismiss' )
			);

			wp_localize_script( 'futurio-admin', 'futurioNUX', $futurio_storefront_notify );
		}

		/**
		 * Output admin notices.
		 *
		 */
		public function admin_notices() {
			global $pagenow;

			if ( ( 'themes.php' === $pagenow ) && isset( $_GET[ 'page' ] ) && ( 'futurio-storefront' === $_GET[ 'page' ] ) || true === (bool) get_option( 'futurio_storefront_notify_dismissed' ) || futurio_storefront_is_extra_activated() ) {
				return;
			}
			?>

			<div class="notice notice-info futurio-notice-nux is-dismissible">
				<div class="futurio-row">
					<div class="futurio-col">
						<div class="notice-content">
							<?php if ( !futurio_storefront_is_extra_activated() && current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' ) ) : ?>
								<h2>
									<?php
									/* translators: %s: Theme name */
									printf( esc_html__( 'Thank you for installing %s.', 'futurio-storefront' ), '<strong>Futurio Storefront</strong>' );
									?>
								</h2>
								<p class="futurio-description">
									<?php
									/* translators: %s: Plugin name string */
									printf( esc_html__( 'To take full advantage of all the features this theme has to offer, please install and activate the %s plugin.', 'futurio-storefront' ), '<strong>Futurio Extra</strong>' );
									?>
								</p>
								<p>
									<?php Futurio_Storefront_Plugin_Install::install_plugin_button( 'futurio-extra', 'futurio-extra.php', 'Futurio Extra', array( 'sf-nux-button' ), __( 'Activated', 'futurio-storefront' ), __( 'Activate', 'futurio-storefront' ), __( 'Install', 'futurio-storefront' ) ); ?>
									<a href="<?php echo esc_url( admin_url( 'themes.php?page=futurio-storefront' ) ); ?>" class="button button-primary futurio-get-started" style="text-decoration: none;">
										<?php
										/* translators: %s: Theme name */
										printf( esc_html__( 'Get started with %s', 'futurio-storefront' ), 'Futurio' );
										?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		public function add_menu() {
			if ( isset( $wp_customize ) || futurio_storefront_is_extra_activated() ) {
				return;
			}
			add_theme_page(
			'Futurio', 'Futurio', 'edit_theme_options', 'futurio-storefront', array( $this, 'admin_page' )
			);
		}

		public function admin_page() {


			if ( futurio_storefront_is_extra_activated() ) {
				return;
			}
			?>

			<div class="notice notice-info sf-notice-nux">
				<span class="sf-icon">
					<?php echo '<img src="' . esc_url( get_template_directory_uri() ) . '/img/futurio-logo.png" width="250" />'; ?>
				</span>

				<div class="notice-content">
					<?php if ( !futurio_storefront_is_extra_activated() && current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' ) ) : ?>
						<h2>
							<?php
							/* translators: %s: Theme name */
							printf( esc_html__( 'Thank you for installing %s.', 'futurio-storefront' ), '<strong>Futurio Storefront</strong>' );
							?>
						</h2>
						<p>
							<?php
							/* translators: %s: Plugin name string */
							printf( esc_html__( 'To take full advantage of all the features this theme has to offer, please install and activate the %s plugin.', 'futurio-storefront' ), '<strong>Futurio Extra</strong>' );
							?>
						</p>
						<p><?php Futurio_Storefront_Plugin_Install::install_plugin_button( 'futurio-extra', 'futurio-extra.php', 'Futurio Extra', array( 'sf-nux-button' ), __( 'Activated', 'futurio-storefront' ), __( 'Activate', 'futurio-storefront' ), __( 'Install', 'futurio-storefront' ) ); ?></p>
					<?php endif; ?>


				</div>
			</div>
			<?php
		}

		/**
		 * AJAX dismiss notice.
		 *
		 * @since 2.2.0
		 */
		public function dismiss_nux() {
			$nonce = !empty( $_POST[ 'nonce' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : false;

			if ( !$nonce || !wp_verify_nonce( $nonce, 'futurio_storefront_notice_dismiss' ) || !current_user_can( 'manage_options' ) ) {
				die();
			}

			update_option( 'futurio_storefront_notify_dismissed', true );
		}

	}

	endif;

return new Futurio_Storefront_Notify_Admin();
