<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="rss-widget">
	<?php
    ob_start();
	@wp_widget_rss_output(
		'https://rich-snippets.io/category/news/feed/',
		array(
			'show_author'  => 0,
			'show_date'    => true,
			'show_summary' => false,
			'items'        => 3,
		)
	);
	echo preg_replace_callback( '#href=[\'"]+(.*)[\'"]#mU', function ( $matches ) {
		return sprintf( 'href="%s"', Helper_Model::instance()->get_campaignify( $matches[1], 'snippet-metabox-news' ) );
	}, ob_get_clean() );
	?>
</div>
<p>
	<a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/category/news/', 'global-snippets-metabox' ) ); ?>"
	   class="button" target="_blank"><?php _e( 'More news', 'rich-snippets-schema' ); ?></a>
</p>
