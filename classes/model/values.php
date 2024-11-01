<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Values.
 *
 * Prepares and fills registered values.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Values_Model {

	/**
	 * Magic method for setting up the class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$methods = $this->get_methods();

		foreach ( $methods as $id => $function_or_method ) {

			if ( is_string( $function_or_method )
			     && method_exists( $this, $function_or_method )
			     && is_callable( [ $this, $function_or_method ] )
			) {
				add_filter(
					'wpbuddy/rich_snippets/rich_snippet/value/' . $id,
					[ $this, $function_or_method ],
					10,
					4
				);
			} else if ( is_array( $function_or_method )
			            && is_callable( $function_or_method )
			) {
				add_filter( 'wpbuddy/rich_snippets/rich_snippet/value/' . $id, $function_or_method, 10, 4 );
			} else if ( is_callable( $function_or_method ) ) {
				add_filter( 'wpbuddy/rich_snippets/rich_snippet/value/' . $id, $function_or_method, 10, 4 );
			} else if ( ! is_array( $function_or_method ) ) {
				add_filter( 'wpbuddy/rich_snippets/rich_snippet/value/' . $id, [ $this, $function_or_method ], 10, 4 );
			}

		}

		add_filter( 'wpbuddy/rich_snippets/rich_snippet/value', array( $this, 'prepare_descendants' ), 10, 3 );

		/**
		 * Rich Snippet Values Init.
		 *
		 * Allows third party plugins to perform any actions after the Values_Model object has been initialized.
		 *
		 * @hook  wpbuddy/rich_snippets/rich_snippet/values/init
		 *
		 * @param {Values_Model} $values_model
		 *
		 * @since 2.0.0
		 */
		do_action_ref_array( 'wpbuddy/rich_snippets/rich_snippet/values/init', array( &$this ) );
	}

	/**
	 * Returns all possible methods.
	 *
	 * @return array
	 * @since 2.19.5
	 */
	protected function get_methods() {
		$methods = Fields_Model::get_internal_values_methods();

		return array_merge( $methods, Fields_Model::get_reference_values_ids() );
	}

	/**
	 * Fetches a call to function that doesn't exist.
	 *
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 */
	public function __call( $name, $args ) {

		return $args[0];
	}


	/**
	 * Returns the current post URL.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) get_permalink( $meta_info['current_post_id'] );
	}


	/**
	 * Returns the current post thumbnail URL.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_thumbnail_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) Helper_Model::instance()->get_thumbnail_meta(
			'url',
			(int) $meta_info['current_post_id']
		);
	}


	/**
	 * Returns the current post thumbnail width.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return int
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_thumbnail_width( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): int {

		if ( $overwritten ) {
			return (int) $val;
		}

		return (int) Helper_Model::instance()->get_thumbnail_meta(
			'width',
			(int) $meta_info['current_post_id']
		);

	}


	/**
	 * Returns the current post thumbnail height.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return int
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_thumbnail_height( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): int {

		if ( $overwritten ) {
			return (int) $val;
		}

		return (int) Helper_Model::instance()->get_thumbnail_meta(
			'height',
			(int) $meta_info['current_post_id']
		);
	}


	/**
	 * Returns the current post title.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_title( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return strip_tags( get_the_title( $meta_info['current_post_id'] ) );
	}


	/**
	 * Returns the current post excerpt.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_excerpt( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		$post = get_post( $meta_info['current_post_id'] );

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		if ( post_password_required( $meta_info['current_post_id'] ) ) {
			return '';
		}

		if ( ! empty( $post->post_excerpt ) ) {
			return strip_tags( $post->post_excerpt );
		}

		return strip_tags( get_the_excerpt( $meta_info['current_post_id'] ) );
	}


	/**
	 * Returns the current post date.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string A date value in ISO 8601 date format.
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_date( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return ( string) $val;
		}

		return (string) get_the_date( 'c', $meta_info['current_post_id'] );
	}


	/**
	 * Returns the current post modified date.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string A date value in ISO 8601 date format.
	 *
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_modified_date( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) get_the_modified_date( 'c', $meta_info['current_post_id'] );
	}


	/**
	 * Returns the current post author name.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_author_name( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) Helper_Model::instance()->get_author_meta_by_post_id(
			'display_name',
			$meta_info['current_post_id']
		);

	}


	/**
	 * Returns the current post author url.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_author_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		$author_url = (string) Helper_Model::instance()->get_author_meta_by_post_id(
			'user_url',
			(int) $meta_info['current_post_id']
		);

		if ( ! empty( $author_url ) ) {
			return $author_url;
		}

		return (string) get_author_posts_url(
			Helper_Model::instance()->get_author_id( (int) $meta_info['current_post_id'] )
		);
	}


	/**
	 * Returns the blog title.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function blog_title( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) get_bloginfo( 'name' );
	}


	/**
	 * Returns the blog description.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function blog_description( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) get_bloginfo( 'description' );
	}


	/**
	 * Returns the blog URL.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function blog_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) site_url();
	}


	/**
	 * Returns the home URL.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.15.3
	 */
	public function home_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		return (string) home_url();
	}


	/**
	 * Returns the site icon image URL.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function site_icon_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		if ( ! has_site_icon() ) {
			return '';
		}

		return (string) Helper_Model::instance()->get_media_meta(
			'url',
			(int) get_option( 'site_icon' )
		);
	}


	/**
	 * Returns the site icon width.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function site_icon_width( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		if ( ! has_site_icon() ) {
			return '';
		}

		return (string) Helper_Model::instance()->get_media_meta(
			'width',
			(int) get_option( 'site_icon' )
		);
	}


	/**
	 * Returns the site icon height.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.0.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function site_icon_height( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		if ( ! has_site_icon() ) {
			return '';
		}

		return (string) Helper_Model::instance()->get_media_meta(
			'height',
			(int) get_option( 'site_icon' )
		);
	}


	/**
	 * Returns the ID to the current post content.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @deprecated 2.2.0 Return post content instead.
	 *
	 * @since      2.0.0
	 * @since      2.14.25 New parameter $overwritten
	 */
	public function current_post_content_id( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		return self::current_post_content( $val, $rich_snippet, $meta_info, $overwritten );
	}


	/**
	 * Prepares descendants for output.
	 *
	 * @param mixed $var
	 * @param string $name
	 *
	 * @return mixed
	 * @since 2.0.0
	 */
	public function prepare_descendants( $var, $name ) {

		# for overwritten values
		if ( 0 === stripos( $var, 'descendant-' ) ) {
			return str_replace( 'descendant-', '', $var );
		}

		# for non-overwritten (and back-compat) values:
		if ( 0 === stripos( $name, 'descendant-' ) ) {
			return str_replace( 'descendant-', '', $name );
		}

		return $var;
	}


	/**
	 * Returns the ID to a reference.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return \stdClass
	 * @since 2.2.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function textfield_id( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): \stdClass {

		if ( $overwritten && $val instanceof \stdClass ) {
			return $val;
		}

		$obj          = new \stdClass();
		$obj->{'@id'} = Helper_Model::instance()->sanitize_html_id( $val );

		return $obj;
	}


	/**
	 * Returns the current post ID.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.6.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_post_id( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		return (string) $meta_info['current_post_id'];
	}


	/**
	 * Returns the name of the current category.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.7.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_category( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		if ( empty( $meta_info['current_post_id'] ) && is_category() ) {
			$category = get_queried_object();
			if ( ! $category instanceof \WP_Term ) {
				return '';
			} else {
				return esc_html( $category->name );
			}
		}

		$primary_category_id = Helper_Model::instance()->get_primary_category( $meta_info['current_post_id'] );

		if ( empty( $primary_category_id ) ) {
			return '';
		}

		$category_name = get_the_category_by_ID( $primary_category_id );

		if ( is_wp_error( $category_name ) ) {
			return '';
		}

		return $category_name;
	}


	/**
	 * Returns the URL of the current category.
	 *
	 * @param                                     $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.7.0
	 * @since 2.14.25 New parameter $overwritten
	 */
	public function current_category_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		if ( empty( $meta_info['current_post_id'] ) && is_category() ) {
			$category = get_queried_object();
			if ( ! $category instanceof \WP_Term ) {
				return '';
			} else {
				return esc_url( get_category_link( $category ) );
			}
		}

		$primary_category_id = Helper_Model::instance()->get_primary_category( $meta_info['current_post_id'] );

		if ( empty( $primary_category_id ) ) {
			return '';
		}

		$category_url = get_term_link( $primary_category_id );

		if ( is_wp_error( $category_url ) ) {
			return '';
		}

		return esc_url_raw( $category_url );
	}

}
