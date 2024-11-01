<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Settings.
 *
 * Admin settings actions.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Admin_Settings_Controller {


	/**
	 * Admin_Settings_Controller constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		# add scripts and styles to settings menu
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		# setup settings
		$this->register_settings();

		# setup meta boxes
		$this->add_metaboxes();

		add_action( 'wpbuddy/rich_snippets/settings/setting/after/wpb_rs/setting/frontend_json_creator', [
			$this,
			'creator_warning'
		] );

		add_action( 'wpbuddy/rich_snippets/settings/setting/after/wpb_rs/setting/news_email', [
			$this,
			'news_email_warning'
		] );

		add_action( 'update_option_wpb_rs/setting/news_email', [ $this, 'newsletter_sign_up' ], 10, 2 );

		$this->snip_email_signed_up();

		/**
		 * Admin Settings Init Action.
		 *
		 * Allows plugins to hook into the admin settings controller after init.
		 *
		 * @hook  wpbuddy/rich_snippets/backend/settings/init
		 *
		 * @param {Admin_Settings_Controller} $admin_settings_controller
		 *
		 * @since 2.0.0
		 */
		do_action_ref_array( 'wpbuddy/rich_snippets/backend/settings/init', array( $this ) );
	}


	/**
	 * Enqueues scripts and styles for the settings page.
	 *
	 * @since 2.0.0
	 */
	public function scripts() {

		wp_enqueue_style(
			'wpb-rs-admin-settings',
			plugins_url( 'css/admin-settings.css', rich_snippets()->get_plugin_file() ),
			[],
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-settings.css' )
		);

		wp_enqueue_script(
			'wpb-rs-admin-settings',
			plugins_url( 'js/admin-settings.js', rich_snippets()->get_plugin_file() ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/admin-settings.js' ),
			true
		);

		$args = call_user_func( function () {

			$o           = new \stdClass();
			$o->nonce    = wp_create_nonce( 'wp_rest' );
			$o->rest_url = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );

			$o->translations = new \stdClass();

			$o->translations->schema_updates_success = sprintf(
				_x( 'All done', 'Message when snippets have been updated.', 'rich-snippets-schema' ),
				'<span class="dashicons dashicons-smiley"></span>'
			);

			return $o;
		} );

		wp_add_inline_script( 'wpb-rs-admin-settings', "var WPB_RS_SETTINGS = " . \json_encode( $args ) . ";", 'before' );
	}


	/**
	 * Register all settings.
	 *
	 * @since 2.0.0
	 */
	public function register_settings() {

		$settings = self::get_settings();

		foreach ( $settings as $section ) {
			$section_id = sprintf( 'wpbrs_section_%s', $section->id );
			add_settings_section(
				$section_id,
				$section->title,
				'',
				Admin_Controller::instance()->menu_settings_hook
			);

			foreach ( $section->get_settings() as $s ) {
				$settings_id = empty( $s->name ) ? $s->id : $s->get_option_name();
				add_settings_field(
					$settings_id,
					$s->title,
					array( $s, 'render' ),
					Admin_Controller::instance()->menu_settings_hook,
					$section_id,
					array(
						'page_hook' => Admin_Controller::instance()->menu_settings_hook,
						'label_for' => $s->get_option_name(),
						'section'   => $section,
						'setting'   => $s,
					)
				);

				if ( empty( $s->name ) ) {
					continue;
				}

				register_setting(
					'rich-snippets-settings',
					$settings_id,
					array( 'sanitize_callback' => $s->sanitize_callback )
				);
			}
		}

	}


	/**
	 * Generate settings and their sections.
	 *
	 * @return \wpbuddy\rich_snippets\Settings_Section[]
	 * @since 2.0.0
	 *
	 */
	public static function get_settings() {

		$string_to_bool_fcn = function ( $val ) {
			return Helper_Model::instance()->string_to_bool( $val ) ? 1 : 0;
		};

		$settings = [];

		/**
		 * Frontend settings
		 */
		$frontend = new Settings_Section( array(
			'title' => _x( 'Frontend', 'settings section title', 'rich-snippets-schema' ),
		) );

		$frontend->add_setting( array(
			'label'             => __( 'Try to remove "hentry" CSS class from posts.', 'rich-snippets-schema' ),
			'title'             => __( 'Posts', 'rich-snippets-schema' ),
			'type'              => 'checkbox',
			'name'              => 'remove_hentry',
			'default'           => false,
			'sanitize_callback' => $string_to_bool_fcn,
			'autoload'          => true,
			'description'       => sprintf( '<a href="https://rich-snippets.io/hentry-css-class/" target="_blank">%s</a>', __( 'Click here for more information.', 'rich-snippets-schema' ) ),
		) );

		$frontend->add_setting( array(
			'label'             => __( 'Try to remove "vcard" CSS class from comments.', 'rich-snippets-schema' ),
			'title'             => __( 'Comments', 'rich-snippets-schema' ),
			'type'              => 'checkbox',
			'name'              => 'remove_vcard',
			'default'           => false,
			'sanitize_callback' => $string_to_bool_fcn,
			'autoload'          => true,
			'description'       => sprintf( '<a href="https://rich-snippets.io/hentry-css-class/" target="_blank">%s</a>', __( 'Click here for more information.', 'rich-snippets-schema' ) ),
		) );

		$frontend->add_setting( array(
			'label'             => __( 'Move snippet output to footer', 'rich-snippets-schema' ),
			'title'             => __( 'Snippet Output', 'rich-snippets-schema' ),
			'type'              => 'checkbox',
			'name'              => 'snippets_in_footer',
			'default'           => false,
			'sanitize_callback' => $string_to_bool_fcn,
			'autoload'          => true,
			'description'       => __( 'If you\'re using a lot of snippet on one page it\'s probably a good idea to move the output to the footer.', 'rich-snippets-schema' ),
		) );

		if ( Helper_Model::instance()->is_yoast_seo_active() ) {
			$frontend->add_setting( array(
				'label'             => __( 'Remove Yoast schema', 'rich-snippets-schema' ),
				'title'             => __( 'Yoast SEO', 'rich-snippets-schema' ),
				'type'              => 'checkbox',
				'name'              => 'remove_yoast_schema',
				'default'           => false,
				'sanitize_callback' => $string_to_bool_fcn,
				'autoload'          => true,
				'description'       => __( 'Yoast SEO adds some schema.org syntax as well. If you don\'t want to use it, the plugin can try to remove it for you so that you can write your own Structured Data.', 'rich-snippets-schema' ),
			) );
		}

		$frontend->add_setting( array(
			'label'             => __( 'Remove (old) Microdata schemas from any third party plugins and/or the theme. (Pro version only)', 'rich-snippets-schema' ),
			'title'             => __( '(Old) Microdata', 'rich-snippets-schema' ),
			'type'              => 'checkbox',
			'disabled'          => true,
			'name'              => 'remove_microdata_schema',
			'default'           => false,
			'sanitize_callback' => $string_to_bool_fcn,
			'autoload'          => true,
			'description'       => __( 'SNIP can crawl your site and remove all schemas that have been set up using the old Microdata syntax.', 'rich-snippets-schema' ),
		) );

		$frontend->add_setting( array(
			'label'             => sprintf(
				__( 'Turn caching on %s', 'rich-snippets-schema' ),
				Helper_Model::instance()->is_cache_plugin_active() ? '<strong> !!! ' . __( 'Cache plugin detected! You should turn this option OFF.', 'rich-snippets-schema' ) . '</strong>' : ''
			),
			'title'             => __( 'Frontend Caching', 'rich-snippets-schema' ),
			'type'              => 'checkbox',
			'name'              => 'frontend_caching',
			'default'           => false,
			'sanitize_callback' => $string_to_bool_fcn,
			'autoload'          => true,
			'description'       => __( 'Check the checkbox if you want the plugin to store generated structured data into the database. This avoids creating structured data over and over again and should result in a quicker page load.', 'rich-snippets-schema' ),
		) );

		if ( get_option( 'wpb_rs/setting/frontend_caching', false ) ) {
			$frontend->add_setting( array(
				'label'             => _x( 'hours', 'the caching time in "hours"', 'rich-snippets-schema' ),
				'title'             => __( 'Frontend Caching Time', 'rich-snippets-schema' ),
				'type'              => 'number',
				'name'              => 'frontend_caching_time',
				'default'           => 6,
				'sanitize_callback' => 'absint',
				'autoload'          => true,
				'class'             => [ 'small-text' ],
			) );
		}

		if ( ! get_option( 'wpb_rs/setting/frontend_caching', false ) ) {
			$frontend->add_setting( array(
				'label'             => __( 'Pretty Print JSON-LD Code', 'rich-snippets-schema' ),
				'title'             => __( 'Pretty Print', 'rich-snippets-schema' ),
				'type'              => 'checkbox',
				'name'              => 'frontend_json_prettyprint',
				'default'           => false,
				'sanitize_callback' => $string_to_bool_fcn,
				'autoload'          => true,
				'description'       => __( 'If the JSON-LD code should be printed in a pretty format (even more easy for humans to read). Only works if caching is turned off.', 'rich-snippets-schema' ),
			) );
		}

		$frontend->add_setting( array(
			'label'             => __( 'Add "creator" property', 'rich-snippets-schema' ),
			'title'             => __( 'Creator', 'rich-snippets-schema' ),
			'type'              => 'checkbox',
			'name'              => 'frontend_json_creator',
			'default'           => false,
			'sanitize_callback' => $string_to_bool_fcn,
			'autoload'          => true,
			'description'       => sprintf(
				__( 'This will add a "creator" property to your structured data to help you identify structured data generated by SNIP and the ones created by other plugins or services. <a href="%s">Click here for more information</a>.', 'rich-snippets-schema' ),
				Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/duplicate-structured-data/#how-to-detect-where-the-schemas-come-from', 'snip-settings' )
			),
		) );

		$settings['frontend'] = $frontend;
		unset( $frontend );


		/**
		 * Backend
		 */
		$backend = new Settings_Section( array(
			'title' => _x( 'Backend', 'settings section title', 'rich-snippets-schema' ),
		) );

		$post_types = get_post_types( array(
			'public' => true,
		), 'objects' );

		/**
		 * Settings post type filter.
		 *
		 * Allows to add new post types in the settings area.
		 *
		 * @hook  wpbuddy/rich_snippets/settings/allowed_post_types
		 *
		 * @param {object[]} $post_types The post type object array.
		 *
		 * @returns {object[]} The post type objects.
		 *
		 * @since 2.0.0
		 */
		$post_types = apply_filters( 'wpbuddy/rich_snippets/settings/allowed_post_types', $post_types );

		$backend->add_setting( array(
			'title'             => __( 'Post Types', 'rich-snippets-schema' ),
			'type'              => 'select',
			'name'              => 'post_types',
			'multiple'          => true,
			'default'           => array( 'post', 'page' ),
			'options'           => wp_list_pluck( $post_types, 'label', 'name' ),
			'sanitize_callback' => array( Helper_Model::instance(), 'sanitize_text_in_array' ),
			'autoload'          => true,
			'description'       => __( 'Please select the post types where you want to work with the Structured Data Generator.', 'rich-snippets-schema' ),
		) );

		$backend->add_setting( array(
			'title'             => __( 'E-Mail News to', 'rich-snippets-schema' ),
			'type'              => 'text',
			'name'              => 'news_email',
			'autoload'          => false,
			'description'       => sprintf(
				__( 'Never miss any news regarding Structured Data. Please read my <a target="_blank" href="%s">Privacy Policy</a> before you sign up to my list. You probably know the German thoroughness. That\'s why I never share your email address with third parties. I promise!', 'rich-snippets-schema' ),
				Helper_Model::instance()->get_campaignify( 'https://wp-buddy.com/imprint/#privacy', 'snip-settings' )
			),
			'class'             => [ 'regular-text' ],
			'placeholder'       => __( 'your@email.address', 'rich-snippets-schema' ),
			'sanitize_callback' => 'sanitize_email',
		) );

		$settings['backend'] = $backend;
		unset( $backend );


		/**
		 * Actions
		 */
		$actions = new Settings_Section( array(
			'title' => _x( 'Actions', 'settings section title', 'rich-snippets-schema' ),
		) );

		$actions->add_setting( array(
			'title'       => __( 'Cache', 'rich-snippets-schema' ),
			'label'       => __( 'Clear cache', 'rich-snippets-schema' ),
			'type'        => 'button',
			'href'        => '#',
			'class'       => array( 'wpb-rs-clear-cache' ),
			'description' => __( 'The plugin uses WordPress\' internal caching mechanism to speed things up. If you experience weired behaviour, hit the above button to clear all caches.', 'rich-snippets-schema' ),
		) );

		$settings['actions'] = $actions;
		unset( $actions );

		/**
		 * Settings filter.
		 *
		 * Allows to hook into the settings.
		 *
		 * @hook  wpbuddy/rich_snippets/settings
		 *
		 * @param {Settings_Section[]} $settings
		 *
		 * @returns {Settings_Section[]} The post type objects.
		 *
		 * @since 2.19.0
		 */
		return apply_filters( 'wpbuddy/rich_snippets/settings', $settings );
	}


	/**
	 * Add metaboxes for the settings page.
	 *
	 * @since 2.0.0
	 */
	public function add_metaboxes() {

		add_meta_box(
			'settings-general',
			__( 'Settings', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_settings_metabox_general' ),
			'rich-snippets-settings',
			'normal'
		);

		add_meta_box(
			'settings-help',
			__( 'Help', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_snippets_metabox_help' ),
			'rich-snippets-settings',
			'side'
		);

		add_meta_box(
			'settings-news',
			_x( 'News', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_snippets_metabox_news' ),
			'rich-snippets-settings',
			'side',
			'low'
		);
	}


	/**
	 * Prepare settings.
	 *
	 * This function is called during activation to make sure settings are autoloaded correctly.
	 *
	 * @see   Rich_Snippets_Plugin::on_activation()
	 *
	 * @since 2.0.0
	 */
	public static function prepare_settings() {

		$settings = self::get_settings();

		foreach ( $settings as $section ) {
			foreach ( $section->get_settings() as $s ) {
				if ( empty( $s->name ) ) {
					continue;
				}

				$name      = 'wpb_rs/setting/' . $s->name;
				$pre_value = get_option( $name, null );

				# do not overwrite existing values
				if ( ! is_null( $pre_value ) ) {
					continue;
				}

				add_option( $name, $s->default, '', $s->autoload );
			}
		}
	}


	/**
	 * Prints a warning if the creator schemas is active.
	 *
	 * @param Settings_Setting $setting
	 *
	 * @since 2.19.13
	 */
	public function creator_warning( $setting ) {
		if ( ! (bool) $setting->value ) {
			return;
		}

		printf(
			'<div class="notice notice-warning notice-alt inline"><p>%s</p></div>',
			__( 'The creator property should be deactivated on LIVE sites. It is for debugging only.', 'rich-snippets-schema' )
		);
	}


	/**
	 * Prints a warning if the news email is empty.
	 *
	 * @param Settings_Setting $setting
	 *
	 * @since 2.21.0
	 */
	public function news_email_warning( $setting ) {
		if ( ! (bool) get_option( 'wpb_rs/newsletter-signed-up', false ) ) {
			printf(
				'<div class="notice notice-warning notice-alt inline"><p>%s</p></div>',
				sprintf(
					__( 'Your email address is needed to keep you up-to-date. <a href="%s">I\'m already signed up!</a>', 'rich-snippets-schema' ),
					add_query_arg(
						[
							'_wpnonce'             => wp_create_nonce( 'snip-email-signed-up' ),
							'snip_email_signed_up' => 1
						],
						admin_url( 'admin.php?page=rich-snippets-settings' )
					)
				)
			);

			return;
		}

	}

	/**
	 * Tells WP that the user has already signed up for the newsletter.
	 *
	 * @since 2.21.0
	 */
	public function snip_email_signed_up() {
		$should_sign_up = boolval( filter_input( INPUT_GET, 'snip_email_signed_up', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) );

		if ( ! $should_sign_up ) {
			return;
		}

		if ( false === check_admin_referer( 'snip-email-signed-up' ) ) {
			wp_die( __( 'It seems that you\'re not allowed to do this.', 'rich-snippets-schema' ) );
		}

		update_option( 'wpb_rs/newsletter-signed-up', true, true );

		wp_redirect( admin_url( 'admin.php?page=rich-snippets-settings' ) );
	}

	/**
	 * Signs a user up to the newsletter.
	 *
	 * @param string $old_value
	 * @param string $new_value
	 *
	 * @since 2.21.0
	 */
	public function newsletter_sign_up( $old_value, $new_value ) {
		if ( $old_value === $new_value ) {
			return;
		}

		$new_value = sanitize_email( $new_value );

		if ( empty( $new_value ) ) {
			return;
		}

		$nonce = call_user_func( function () {
			$response = WPBuddy_Model::request( '/wpbuddy/rich_snippets_manager/v1/newsletter/create-session', [], false, true );

			if ( is_wp_error( $response ) ) {
				return '';
			}

			return sanitize_text_field( $response->nonce );
		} );

		if ( empty( $nonce ) ) {
			return;
		}

		WPBuddy_Model::request(
			'/wpbuddy/rich_snippets_manager/v1/newsletter',
			[
				'blocking' => false,
				'method'   => 'POST',
				'body'     => [
					'email'    => $new_value,
					'nl_nonce' => $nonce
				],
			],
		);

		update_option( 'wpb_rs/newsletter-signed-up', true, true );
	}
}
