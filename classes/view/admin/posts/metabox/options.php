<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var \WP_Post $post
 */
$post = $this->arguments[0];
?>

<label>
    <input type="checkbox" <?php checked( (bool) get_post_meta( $post->ID, 'snip_hide_schemas', true ) ); ?> value="1"
           name="snip_hide_schemas">
	<?php _e( 'Hide all schemas on this post', 'rich-snippets-schema' ); ?>
</label>
