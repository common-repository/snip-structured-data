<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Snippets_Controller.
 *
 * Starts up all the admin things needed to control snippets.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Admin_Snippets_Controller {

	/**
	 * The instance.
	 *
	 * @var Admin_Snippets_Controller
	 *
	 * @since 2.0.0
	 */
	protected static $instance = null;


	/**
	 * If this instance has been initialized already.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Admin_Snippets_Controller
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.0.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting up the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
	}


	/**
	 * The fields model.
	 *
	 * @since 2.0.0
	 * @var Fields_Model|null
	 */
	protected $fields = null;


	/**
	 * Init.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		if ( $this->initialized ) {
			return;
		}

		add_action( 'add_meta_boxes', array( self::$instance, 'add_meta_boxes' ), 11, 2 );

		add_action( 'admin_enqueue_scripts', array( self::$instance, 'enqueue_scripts_styles' ), 20 );

		add_filter( 'wpbuddy/rich_snippets/save_snippet/property/sanitize', 'sanitize_text_field' );

		add_action( 'post_submitbox_misc_actions', array( self::$instance, 'submitbox_js' ) );

		add_filter( 'post_row_actions', array( self::$instance, 'filter_row_actions' ), 10, 2 );

		add_filter( 'parent_file', [ self::$instance, 'highlight_menu' ] );

		add_filter( 'manage_wpb-rs-global_posts_columns', [ self::$instance, 'manage_posts_columns' ] );

		add_action( 'manage_wpb-rs-global_posts_custom_column', [ self::$instance, 'print_custom_columns' ], 10, 2 );

		add_action( 'save_post', array( self::$instance, 'save_metaboxes' ), 10, 1 );

		add_action( 'admin_notices', array( self::$instance, 'global_snippets_wizard_notice' ) );

		$this->initialized = true;
	}


	/**
	 *
	 * Creates metaboxes.
	 *
	 * @param string $post_type
	 * @param \WP_Post $post
	 *
	 * @since 2.0.0
	 */
	public function add_meta_boxes( $post_type, $post ) {

		# the main metabox
		add_meta_box(
			'wp-rs-mb-main',
			_x( 'Structured data', 'metabox title', 'rich-snippets-schema' ),
			array( self::$instance, 'render_snippets_meta_box' ),
			'wpb-rs-global',
			'advanced',
			'high'
		);

		add_meta_box(
			'wp-rs-mb-post',
			_x( 'Structured Data', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_posts_metabox' ),
			(array) get_option( 'wpb_rs/setting/post_types', array( 'post', 'page' ) ),
			'advanced',
			'low'
		);

		add_meta_box(
			'wp-rs-mb-options',
			_x( 'SNIP Options', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_posts_metabox_options' ),
			get_post_types( [ 'show_ui' => true ] ),
			'side',
			'low'
		);

		add_meta_box(
			'wp-rs-mb-help',
			_x( 'Help', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_snippets_metabox_help' ),
			'wpb-rs-global',
			'side',
			'low'
		);

		add_meta_box(
			'wp-rs-mb-news',
			_x( 'News', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_snippets_metabox_news' ),
			'wpb-rs-global',
			'side',
			'low'
		);

		/**
		 * Schema Metabox Action.
		 *
		 * Allows plugins to hook into the Snippets Controller after metaboxes have been set up.
		 *
		 * @hook  wpbuddy/rich_snippets/schemas/metaboxes
		 *
		 * @param {string} $post_type
		 * @param {WP_Post} $post
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/schemas/metaboxes', $post_type, $post );
	}


	/**
	 * Adds scripts and styles.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts_styles() {

		$this->get_scripts_controller()->enqueue_snippets_styles();
		$this->get_scripts_controller()->enqueue_snippets_scripts();

		$post_type = Helper_Model::instance()->get_current_admin_post_type();

		$post_types = (array) get_option( 'wpb_rs/setting/post_types', array( 'post', 'page' ) );

		if ( in_array( $post_type, $post_types ) ) {
			$this->get_scripts_controller()->enqueue_posts_forms_scripts();
		}

		$post_types[] = 'wpb-rs-global';

		if ( in_array( $post_type, $post_types ) ) {
			$this->get_scripts_controller()->enqueue_posts_scripts();
		}

		/**
		 * Schema Scripts Action.
		 *
		 * Allows plugins to hook into the Admin Snippets Controller after scripts and styles have been enqueued.
		 *
		 * @hook  wpbuddy/rich_snippets/schemas/scripts
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/schemas/scripts' );
	}


	/**
	 * Renders the meta box.
	 *
	 * @param \WP_Post $post
	 * @param array $metabox
	 *
	 * @since 2.0.0
	 *
	 */
	public function render_snippets_meta_box( $post, $metabox ) {

		$snippet = Snippets_Model::get_first_snippet( (int) $post->ID );

		View::admin_snippets_metabox_snippet( $snippet, $post );

	}


	/**
	 * Saves a schema from the builder to the database.
	 *
	 * @param int $post_id
	 *
	 * @since 2.0.0
	 *
	 */
	public function save_snippets( $post_id ) {

		if ( ! isset( $_POST['snippets'] ) ) {
			return;
		}

		if ( ! is_array( $_POST['snippets'] ) ) {
			return;
		}

		/**
		 * Save snippets
		 */

		$snippets = Snippets_Model::sanitize_and_generate_snippets( $_POST['snippets'] );

		Snippets_Model::update_snippets( $post_id, $snippets );
	}


	/**
	 * Returns the HTML code for the properties to use in the table.
	 *
	 * Uses Rich_Snippet:get_properties() if $prop_ids has no elements.
	 *
	 * @param Rich_Snippet $snippet
	 * @param array $property_ids
	 * @param \WP_Post $post
	 *
	 * @return string[]
	 * @since 2.0.0
	 * @since 2.2.0 Added $post param.
	 *
	 */
	public function get_property_table_elements( $snippet, $property_ids = array(), $post = null ) {

		$this->init_fields();

		$html_elements = array();

		if ( count( $property_ids ) > 0 ) {
			$props = array_map( function ( $val ) {

				return Schemas_Model::get_property_by_id( $val );
			}, $property_ids );
		} else {
			# load the properties from the snippet
			$props = $snippet->get_properties();
		}

		foreach ( $props as $prop ) {
			if ( ! $prop instanceof Schema_Property ) {
				continue;
			}

			ob_start();
			View::admin_snippets_properties_row( $prop, $snippet, $post );
			$html_elements[] = ob_get_clean();
		}

		return $html_elements;
	}


	/**
	 * Builds a property table.
	 *
	 * Uses Rich_Snippet:get_properties() if $prop_ids has no elements.
	 *
	 * @param Rich_Snippet $snippet
	 * @param array $prop_ids
	 * @param \WP_Post $post
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.2.0 Added $post parameter
	 *
	 */
	public function get_property_table( $snippet, $prop_ids = array(), $post = null ) {

		$props_rendered = $this->get_property_table_elements( $snippet, $prop_ids, $post );

		ob_start();
		View::admin_snippets_properties_table( $props_rendered, $snippet, $post );

		return ob_get_clean();

	}


	/**
	 * Initializes the fields.
	 *
	 * Prevents double-init.
	 *
	 * @since 2.0.0
	 */
	public function init_fields() {

		if ( ! $this->fields instanceof Fields_Model ) {
			$this->fields = new Fields_Model();
		}
	}

	/**
	 * Returns the current scripts controller.
	 *
	 * @since 2.19.0
	 */
	public function get_scripts_controller() {
		return Admin_Scripts_Controller::instance();
	}

	/**
	 * Adds JS to the submitbox to remove some fields.
	 *
	 * @since 2.0.0
	 */
	public function submitbox_js() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'wpb-rs-global' !== $screen->post_type ) {
			return;
		}


		?>
        <script type="text/javascript">
            jQuery('#visibility, .misc-pub-curtime').remove();
        </script>
		<?php
	}

	/**
	 * Filters the row actions.
	 *
	 * @param array $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function filter_row_actions( $actions, $post ) {

		if ( 'wpb-rs-global' !== $post->post_type ) {
			return $actions;
		}

		# Remove the quick edit option.
		# Note that the quickedit option has a weird name ('inline hide-if-no-js')
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return array_merge( [ 'ID: ' . $post->ID ], $actions );
	}


	/**
	 * Make sure the main menu is highlighted if we're on the "Global Snippets"
	 *
	 * @param string $parent_file
	 *
	 * @return string
	 *
	 * @since 2.14.0
	 */
	public function highlight_menu( $parent_file ) {


		$screen = get_current_screen();

		if ( $screen instanceof \WP_Screen && $screen->id === 'wpb-rs-global' ) {
			global $plugin_page;
			$plugin_page = 'edit.php?post_type=wpb-rs-global';
		}

		return $parent_file;
	}


	/**
	 * Manage wpb-rs-global post columns.
	 *
	 * @param array $columns
	 *
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public function manage_posts_columns( $columns ) {

		$columns = Helper_Model::instance()->integrate_into_array(
			$columns,
			2,
			[
				'snippet-ids' => __( 'Snippet IDs', 'rich-snippets-schema' )
			]
		);

		return $columns;
	}


	/**
	 * Print custom columns on wpb-rs-global post overview page.
	 *
	 * @param string $column_name
	 * @param int $post_id
	 *
	 * @since 2.8.0
	 */
	public function print_custom_columns( $column_name, $post_id ) {
		if ( 'snippet-ids' === $column_name || 'predefined' === $column_name ) {
			$snippets = Snippets_Model::get_snippets( $post_id );

			foreach ( $snippets as $snippet ) {
				if ( 'predefined' === $column_name ) {
					if ( false !== stripos( $snippet->id, 'snip-global-' ) ) {
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						echo '<span class="dashicons dashicons-no"></span>';
					}
					break;
				} else {
					echo $snippet->id;
				}
			}

		}

		if ( 'sync' === $column_name ) {
			$synced_id = intval( get_post_meta( $post_id, '_wpb_rs_sync_id', true ) );

			if ( $synced_id > 0 ) {
				echo '<span class="dashicons dashicons-yes wpb-rs-global-snippet-sync-yes"></span>';
			} else {
				echo '<span class="dashicons dashicons-no wpb-rs-global-snippet-sync-no"></span>';
			}
		}
	}


	/**
	 * Saves fields from the metaboxes.
	 *
	 * @param int $post_id
	 *
	 * @see   Admin_Snippets_Controller::save_snippets()
	 *
	 * @since 2.22.0
	 *
	 */
	public function save_metaboxes( $post_id ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['snip_hide_schemas'] ) ) {
			delete_post_meta( $post_id, 'snip_hide_schemas' );
		} else {
			update_post_meta( $post_id, 'snip_hide_schemas', Helper_Model::instance()->string_to_bool( $_POST['snip_hide_schemas'] ) );
		}

	}


	/**
	 * Print an admin notice to ask if we should install examples via the wizard.
	 *
	 * @since 2.22.0
	 */
	public function global_snippets_wizard_notice() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen instanceof \WP_Screen ) {
			return;
		}

		if ( 'edit-wpb-rs-global' !== $screen->id ) {
			return;
		}

		$user = wp_get_current_user();
		?>
        <div class="notice notice-info">
            <p><?php
				printf(
					__( 'Hey <strong>%s!</strong> You can use the Setup Wizard to install some schemas that I\'ve pre-build. Want to check it out?', 'rich-snippets-schema' ),
					$user->display_name
				);

				printf(
					' <a class="button" href="%s">%s</a>',
					admin_url( 'admin.php?page=rich-snippets-setupwizard' ),
					__( 'Awesome! Start the Wizard!', 'rich-snippets-schema' )
				);
				?>
            </p>
        </div>
		<?php
	}
}
