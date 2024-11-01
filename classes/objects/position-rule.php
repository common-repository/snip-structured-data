<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Position_Rule.
 *
 * Exists for backwards compatibility.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Position_Rule {

	/**
	 * The param.
	 *
	 * @since 2.0.0
	 *
	 * @var null|string
	 */
	public $param = null;


	/**
	 * The operator.
	 *
	 * @since 2.0.0
	 *
	 * @var null|string
	 */
	public $operator = null;


	/**
	 * The value.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed
	 */
	public $value = null;


	/**
	 * Checks if the rule matches with the current page.
	 *
	 * @return bool
	 */
	public function match(): bool {

		/**
		 * Position Rule match filter (bail early).
		 *
		 * Allows to bail early for a single rule in a ruleset.
		 *
		 * @hook  wpbuddy/rich_snippets/rule/match/bail_early
		 *
		 * @param {null|bool} $bail_early If and how to bail early.
		 * @param {Position_Rule} $position_rule The rule object.
		 *
		 * @returns {null|bool} NULL if default behaviour should be turned ON. Otherwise true or false.
		 *
		 * @since 2.0.0
		 */
		$bail_early = apply_filters( 'wpbuddy/rich_snippets/rule/match/bail_early', null, $this );

		if ( is_bool( $bail_early ) ) {
			return $bail_early;
		}

		$ret = false;

		$method_name = sprintf( 'match_%s', $this->param );

		if ( method_exists( $this, $method_name ) ) {
			$ret = $this->{$method_name}();
		}

		/**
		 * Position Rule match filter by method name.
		 *
		 * Allows to filter the result of a rule match by method name.
		 *
		 * @hook  wpbuddy/rich_snippets/rule/{$method_name}
		 *
		 * @param {bool} $match_result The return value.
		 * @param {Position_Rule} $position_rule The current rule object.
		 *
		 * @returns {bool} The match result.
		 *
		 * @since 2.0.0
		 */
		$ret = boolval( apply_filters( 'wpbuddy/rich_snippets/rule/' . $method_name, $ret, $this ) );

		/**
		 * Position Rule match filter
		 *
		 * Allows to filter the match result of a Rule.
		 *
		 * @hook  wpbuddy/rich_snippets/rule/match
		 *
		 * @param {bool} $match_result The match result.
		 * @param {Position_Rule} $position_rule The current rule object.
		 *
		 * @returns {bool} The modified match result.
		 *
		 * @since 2.0.0
		 */
		return boolval( apply_filters( 'wpbuddy/rich_snippets/rule/match', $ret, $this ) );
	}


	/**
	 * Returns the current main query.
	 *
	 * @return \WP_Query
	 * @since 2.0.0
	 *
	 */
	public function get_query() {

		# are we in the main query?
		if ( is_main_query() ) {
			global $wp_query;

			/**
			 * Position Rule query filter.
			 *
			 * Allows to filter the query that is used to compare a post.
			 *
			 * @hook  wpbuddy/rich_snippets/rule/query
			 *
			 * @param {WP_Query} $wp_query The query.
			 * @param {Position_Rule} $position_rule The current rule object.
			 *
			 * @returns {WP_Query} The modified query.
			 *
			 * @since 2.27.0
			 */
			return apply_filters( 'wpbuddy/rich_snippets/rule/query', $wp_query, $this );
		}

		global $wp_the_query;

		return apply_filters( 'wpbuddy/rich_snippets/rule/query', $wp_the_query, $this );
	}


	/**
	 * Compares a value with the internal value of the Rule.
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function compare( $value ): bool {

		# non-scalar types are not supported.
		if ( ! is_scalar( $value ) ) {
			return false;
		}

		switch ( $this->operator ) {
			case '!=':
				return $this->value !== $value;
				break;
			case '==':
			default:
				return $this->value === $value;
		}
	}


	/**
	 * Checks if the post ID matches.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	private function match_post(): bool {

		$query = $this->get_query();

		if ( ! $query->is_singular() ) {
			return false;
		}

		$current_post = $query->post;

		if ( ! is_a( $current_post, '\WP_Post' ) ) {
			return false;
		}

		# allow identical comparison
		$this->value = absint( $this->value );

		return $this->compare( $current_post->ID );
	}


	/**
	 * Checks if the current post matches a post type.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	private function match_post_type(): bool {

		$query = $this->get_query();

		if ( ! $query->is_singular() ) {
			return false;
		}

		$current_post = $query->post;

		if ( ! is_a( $current_post, '\WP_Post' ) ) {
			return false;
		}

		return $this->compare( $current_post->post_type );
	}


	/**
	 * Checks if the current page is a term.
	 *
	 * @param string $taxonomy
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function match_term( string $taxonomy ) {

		$query = $this->get_query();

		# categories are only allowed in 'post' post types
		if ( $query->is_singular() ) {
			$post_type = get_post_type( $query->get_queried_object_id() );
			if ( ! is_object_in_taxonomy( $post_type, $taxonomy ) ) {
				return false;
			}

			$current_post = $query->post;

			if ( ! is_a( $current_post, '\WP_Post' ) ) {
				return false;
			}

			$comp = has_term( $this->value, $taxonomy, $current_post ) ? "{$taxonomy}:{$this->value}" : '';

			$this->value = "{$taxonomy}:{$this->value}";

			return $this->compare( $comp );

		} elseif ( $query->is_category() || $query->is_tag() || $query->is_tax() ) {
			return $this->compare( $query->queried_object_id );
		}

		return false;
	}


	/**
	 * Checks if the post has a specific category.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	private function match_post_category(): bool {

		return $this->match_term( 'category' );

	}


}