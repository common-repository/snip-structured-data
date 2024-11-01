<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Setup_Wizard.
 *
 * Helps users to install SNIP.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.22.0
 */
class Admin_Setup_Wizard_Controller {


	/**
	 * Magic method for setting upt the class.
	 *
	 * @since 2.22.0
	 */
	public function __construct() {

		add_filter( 'load_script_translation_file', [ $this, 'correct_translation_file_paths' ], 10, 3 );

		$this->enqueue_scripts();
		$this->wizard_page();
	}


	/**
	 * Enqueues plugin page styles and scripts.
	 *
	 * @since 2.22.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'install' );
		wp_enqueue_style( 'wp-components' );

		wp_register_script(
			'snip-schema-property',
			plugins_url( 'js/build/schema-property.js', rich_snippets()->get_plugin_file() ),
			[
				'react',
				'react-dom',
				'wp-data',
				'wp-i18n',
			],
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/build/schema-property.js' )
		);

		wp_enqueue_script(
			'wpb-rs-confetti',
			plugins_url( 'js/confetti.browser.min.js', rich_snippets()->get_plugin_file() ),
			[],
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/confetti.browser.min.js' )
		);

		wp_enqueue_script(
			'snip-setupwizard',
			plugins_url( 'js/build/admin-setupwizard.js', rich_snippets()->get_plugin_file() ),
			[
				'wp-i18n',
				'react',
				'react-dom',
				'wp-api-fetch',
				'lodash',
				'wp-data',
				'wp-sanitize',
				'wp-notices',
				'wp-components',
				'wpb-rs-confetti',
				'snip-schema-property'
			],
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/build/admin-setupwizard.js' )
		);


		$o = call_user_func( function () {
			$o                    = new \stdClass();
			$o->imagePath         = esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) );
			$o->wpAdminUrl        = esc_url( trailingslashit( admin_url() ) );
			$o->globalSnippetsUrl = esc_url( admin_url( 'edit.php?post_type=wpb-rs-global' ) );
			$o->wpSiteUrl         = esc_url( trailingslashit( site_url() ) );
			$o->wpSiteUrlCleaned  = str_replace( [ 'http://', 'https://' ], '', site_url() );
			$o->isPro             = rich_snippets() instanceof \wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro;
			$o->avatar            = get_avatar_data( 'b91b96c4ece6410bf6871ce516830513@md5.gravatar.com', [ 'size' => 500 ] );
			$o->userName          = Helper_Model::instance()->get_current_user_firstname();
			$o->userEMail         = Helper_Model::instance()->get_current_user_email();
			$o->userLanguage      = strstr( get_user_locale( wp_get_current_user() ), '_', true );
			$o->isPluginActive    = $o->isPro && \wpbuddy\rich_snippets\pro\Helper_Model::instance()->magic();

			return $o;
		} );


		wp_add_inline_script(
			'snip-setupwizard',
			sprintf( 'var SetupWizardConfig = %s;', json_encode( $o ) ),
			'before'
		);

		wp_set_script_translations(
			'snip-setupwizard',
			'rich-snippets-schema',
			plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'languages/json'
		);
	}


	/**
	 * Prepare uninstall page.
	 *
	 * @since 2.22.0
	 */
	public function wizard_page() {

		$this->page_header();
		?>

		<?php
		$this->page_footer();

		exit;
	}


	/**
	 * Prints the page header
	 *
	 * @since 2.22.0
	 */
	private function page_header() {
		set_current_screen();
		?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width"/>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <title><?php esc_html_e( 'SNIP Setup Wizard', 'rich-snippets-schema' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'snip-setupwizard' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
        </head>
        <body class="wpb-rs-setupwizard wp-core-ui">
        <h1 class="logo">
            <a href="https://rich-snippets.io"><img
                        src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/snip.svg"
                        alt="SNIP"/></a>
            <span class="logo--wizard">🧙‍♂️</span>
        </h1>
        <div id="setupwizard"></div>
		<?php
	}


	/**
	 * Prints the page footer.
	 *
	 * @since 2.22.0
	 */
	private function page_footer() {
		?>
        </body>
        </html>
		<?php
	}


	/**
	 * Corrects the translation file paths.
	 *
	 * @param string $file
	 * @param string $handle
	 * @param string $domain
	 *
	 * @return string
	 * @since 2.22.0
	 *
	 */
	public function correct_translation_file_paths( $file, $handle, $domain ) {

		if ( 'rich-snippets-schema' !== $domain ) {
			return $file;
		}

		if ( false === stripos( $handle, 'setupwizard' ) ) {
			return $file;
		}

		$file = str_replace( $domain, 'setupwizard', $file );

		$undefined_folder = strstr( $handle, '/', true );
		$real_file_name   = str_replace( [ $undefined_folder . '/', '_script' ], '', $handle );

		$file = str_replace( [ $undefined_folder . '/', '_script' ], '', $file );
		$file = str_replace( $real_file_name, md5( $real_file_name ), $file );

		return $file;
	}

}
