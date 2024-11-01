<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Post_Controller.
 *
 * Helps to manage things on the edit screen.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.28.0
 */
class Admin_Post_Controller {


	/**
	 * Magic method for setting upt the class.
	 *
	 * @since 2.28.0
	 */
	public function __construct() {

		$post_types = (array) get_option( 'wpb_rs/setting/post_types', array( 'post', 'page' ) );

		foreach ( $post_types as $post_type ) {
			$p = get_post_types( [ 'name' => $post_type ], 'objects' );
			$p = $p[ $post_type ];

			add_filter( sprintf( 'manage_edit-%s_sortable_columns', $post_type ), [ $this, 'sortable_columns' ] );
			add_filter( sprintf( 'manage_edit-%s_columns', $post_type ), [ $this, 'columns' ] );
			add_action(
				sprintf(
					'manage_%s_custom_column',
					str_replace( 'edit_', '', $p->cap->edit_posts )
				),
				[
					$this,
					'column'
				], 10, 2 );
		}

		add_action( 'pre_get_posts', [ $this, 'filter_query_orderby' ], 100 );

		add_filter( 'posts_clauses_request', [ $this, 'filter_query_number' ], 100, 2 );

	}

	/**
	 *
	 * @param array $columns
	 *
	 * @return array
	 * @since 2.28.0
	 */
	public function sortable_columns( $columns ) {
		$columns['schema_count'] = 'schema_count';

		return $columns;
	}


	/**
	 *
	 * @param array $columns
	 *
	 * @return array
	 * @since 2.28.0
	 */
	public function columns( $columns ) {
		$columns['schema_count'] = __( 'Schema count', 'rich-snippets-schema' );

		return $columns;
	}


	/**
	 *
	 * @param string $colname
	 * @param int $post_id
	 *
	 * @return array
	 * @since 2.28.0
	 */
	public function column( $colname, $post_id ) {
		if ( 'schema_count' === $colname ) {
			echo count( Snippets_Model::get_snippets( $post_id ) );
		}
	}


	/**
	 * Correctly adds the orderBy statement.
	 *
	 * @param \WP_Query $query
	 *
	 * @since 2.28.1
	 */
	public function filter_query_orderby( $query ) {

		if ( ! isset( $query->query_vars['orderby'] ) ) {
			return;
		}

		if ( 'schema_count' !== $query->query_vars['orderby'] ) {
			return;
		}

		if ( ! isset( $query->query_vars['meta_query'] ) ) {
			$query->query_vars['meta_query'] = [];
		}

		$query->query_vars['meta_query'][] = [
			'relation' => 'OR',
			[
				'key'     => '_wpb_rs_schema',
				'compare' => 'EXISTS'
			],
			[
				'key'     => '_wpb_rs_schema',
				'compare' => 'NOT EXISTS'
			]
		];

		$this->meta_query_counter( count( $query->query_vars['meta_query'] ) );
	}

	/**
	 * An internal value for the meta query counter.
	 *
	 * @param null|int $ctn
	 *
	 * @return int
	 *
	 * @since 2.28.1
	 */
	private function meta_query_counter( $ctn = null ) {
		static $counter;

		if ( ! isset( $counter ) && ! is_null( $ctn ) ) {
			$counter = $ctn;
		}

		return $counter;
	}

	/**
	 * Correctly adds the orderBy statement.
	 *
	 * @param string[] $clauses
	 * @param \WP_Query $query
	 *
	 * @return string[]
	 *
	 * @since 2.28.1
	 */
	public function filter_query_number( $clauses, $query ) {

		if ( ! isset( $query->query_vars['orderby'] ) ) {
			return $clauses;
		}

		if ( 'schema_count' !== $query->query_vars['orderby'] ) {
			return $clauses;
		}

		$counter = $this->meta_query_counter();

		$clauses['fields']  .= ', CAST(SUBSTR(REGEXP_SUBSTR (mt' . $counter . '.meta_value, \'^a:([0-9]+)\'), 3) AS UNSIGNED INTEGER) as schema_count';
		$clauses['orderby'] = 'schema_count ' . $query->query_vars['order'];

		return $clauses;
	}
}
