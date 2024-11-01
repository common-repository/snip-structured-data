<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$s = rich_snippets();

$plugin_data = Helper_Model::instance()->get_plugin_data();

$current_tab = htmlspecialchars( (string) filter_input( INPUT_GET, 'tab' ) );
$current_tab = empty( $current_tab ) ? 'intro' : $current_tab;
?>

<div class="wrap about-wrap full-width-layout wpb-rs-main-intro">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <p class="about-text">
		<?php _e( 'SNIP is the most flexible Structured Data and Rich Snippets Plugin on this planet.', 'rich-snippets-schema' ); ?>
    </p>
    <div class="wp-badge"><?php printf(
			__( '%sVersion %s', 'rich-snippets-schema' ),
			$s instanceof \wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro ? __( 'Pro', 'rich-snippets-schema' ) . ' ' : '',
			$plugin_data['Version'] ?? 'x'
		); ?></div>

	<?php

	$tabs = [
		'intro'        => __( 'Introduction', 'rich-snippets-schema' ),
		'activation'   => __( 'Activation', 'rich-snippets-schema' ) . Helper_Model::instance()->get_start_menu_counter_html(),
		'updates'      => __( 'Updates', 'rich-snippets-schema' ),
		'training'     => __( 'Training', 'rich-snippets-schema' ),
		'wheretostart' => __( 'Where to start?', 'rich-snippets-schema' ),
		'setupservice' => __( 'Setup Service', 'rich-snippets-schema' ),
		'news'         => __( 'What\'s new?', 'rich-snippets-schema' ),
	];

	foreach ( $tabs as $tab_key => $label ) {
		printf(
			'<input name="menu" type="radio" value="%1$s" id="wpb-rs-intro-tab-%1$s" %2$s/>',
			$tab_key,
			checked( $current_tab, $tab_key, false )
		);
	}
	?>
    <h2 class="nav-tab-wrapper wp-clearfix">
		<?php
		foreach ( $tabs as $tab_key => $label ) {
			$i = ( $i ?? 0 ) + 1;
			printf(
				'<label for="wpb-rs-intro-tab-%s" class="nav-tab">%s. %s</label>',
				$tab_key,
				$i,
				$label
			);
		}
		unset( $i );
		?>
    </h2>

    <div class="about-wrap-content wpb-rs-intro-tab-intro wpb-rs-intro-tab">
        <div class="wpb-rs-intro-right">
			<?php
			$avatar = get_avatar_data( 'b91b96c4ece6410bf6871ce516830513@md5.gravatar.com', [ 'size' => 500 ] );
			?>
            <img src="<?php echo esc_url( $avatar['url'] ); ?>" width="250" height="250"
                 alt="<?php esc_attr_x( 'WP-Buddy Head of Development', 'Image alt text', 'rich-snippets-schema' ); ?>"/>

            <p>
                <span class="dashicons dashicons-twitter"></span> <a
                        href="https://twitter.com/floriansimeth"><?php _e( 'Follow me on Twitter', 'rich-snippets-schema' ); ?></a><br/>
                <span class="dashicons dashicons-linkedin"></span> <a
                        href="https://www.linkedin.com/in/floriansimeth/"><?php _e( 'Follow me on LinkedIn', 'rich-snippets-schema' ); ?></a>
            </p>
        </div>
		<?php
		printf(
			'<p class="about-description">%s</p>',
			sprintf( __( 'Hey <strong>%s!</strong> Nice you\'re here!', 'rich-snippets-schema' ), Helper_Model::instance()->get_current_user_firstname() )
		);

		printf(
			'<p class="about-description">%s</p>',
			__( 'My name is Florian but you can call me "Flow".', 'rich-snippets-schema' )
		);

		printf(
			'<p class="about-description">%s</p>',
			__( 'I’m the one behind SNIP and do general web development for over 17 years now. And as you might expect: I’m really passionate about what I do (who else can truly say that?).', 'rich-snippets-schema' )
		);

		printf(
			'<p class="about-description">%s</p>',
			convert_smilies(
				sprintf(
					__( 'Hopefully you can feel my passion in the all new <strong>SNIP Plugin</strong> which is now in version %s! Yey! :-)', 'rich-snippets-schema' ),
					$plugin_data['Version'] ?? 'x'
				)
			)
		);

		if ( $s instanceof \wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro ) {
			printf(
				'<p class="about-description">%s</p>',
				sprintf(
					__( 'This plugin will skyrocket <strong>structured data</strong> on your site! If you have any questions and/or ideas to '
					    . 'make this plugin even better, feel free to <a href="%s">add a feature request.</a>', 'rich-snippets-schema' ),
					esc_url( admin_url( 'admin.php?page=rich-snippets-support' ) )
				)
			);
		} else {

			printf(
				'<p class="about-description">%s</p>',
				sprintf(
					__( 'Did you know? <strong>There is a pro version of this plugin available.</strong> It allows to '
					    . 'automate structured data generation to save you time! Check it out here: <a href="%s">SNIP Pro + Automation!</a>', 'rich-snippets-schema' ),
					Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io', 'lets-start-intro-tab' )
				)
			);
		}

		printf(
			'<p><label for="wpb-rs-intro-tab-activation" class="button button-primary button-hero">%s</label></p>',
			__( 'Activate the plugin', 'rich-snippets-schema' )
		);

		?>
    </div>

    <div class="about-wrap-content wpb-rs-intro-tab wpb-rs-intro-tab-activation">
		<?php
		$purchase_code = get_option( 'wpb_rs/purchase_code', '' );
		if ( $s instanceof \wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro ):
			?>
            <p class="about-description">
				<?php _e( 'Please enter your purchase code from CodeCanyon below.', 'rich-snippets-schema' ); ?>
            </p>
		<?php endif; ?>

        <p><?php
			if ( $s instanceof \wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro ) {
				printf(
					__( '<a href="%s" target="_blank">Where to find your purchase code</a>', 'rich-snippets-schema' ) . ' | ',
					Helper_Model::instance()->get_campaignify( 'https://wp-buddy.com/blog/where-to-find-your-envato-purchase-code/', 'lets-start-activation-tab' )
				);
			}

			printf(
				__( '<a href="%s" target="_blank">You cannot activate the plugin?</a>', 'rich-snippets-schema' ),
				Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/i-cannot-activate-the-plugin/', 'lets-start-activation-tab' )
			); ?>
        </p>

        <div class="wpb-rs-intro-tab-activation-messages"></div>

		<?php
		if ( method_exists( Helper_Model::instance(), 'magic' )
		) {
			if ( call_user_func( [ Helper_Model::instance(), 'magic' ] ) ) {
				printf(
					'<div class="notice notice-success inline"><p>%s</p></div>',
					isset( $plugin_data['Active'] ) ? $plugin_data['Active'] : ''
				);
			}
		} else {
			if ( get_option( 'wpb_rs/active', false ) ) {
				printf(
					'<div class="notice notice-success inline"><p>%s</p></div>',
					__( 'Your copy is active.', 'rich-snippets-schema' )
				);
			}
		}
		?>

        <form class="wpb-rs-intro-tab-activation-card">
			<?php if ( $s instanceof \wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro ): ?>
                <fieldset>
                    <label for="wpb-rs-intro-tab-activation-purchase-code">
						<?php
						_e( 'Purchase Code:', 'rich-snippets-schema' );
						?>
                    </label>
                    <p>
                        <input class="regular-text wpb-rs-main-cc-code code" type="text"
                               id="wpb-rs-intro-tab-activation-purchase-code"
                               placeholder="<?php esc_attr_e( 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx' ); ?>"
                               value="<?php echo esc_attr( $purchase_code ); ?>"/><br/>
                    </p>
                </fieldset>
			<?php endif; ?>
            <fieldset class="bottom">
                <p>
                    <input type="checkbox" id="wpb-rs-intro-tab-activation-privacy" class="wpb-rs-privacy-agree"
						<?php checked(
							( method_exists( Helper_Model::instance(), 'magic' )
							  && call_user_func( [
									Helper_Model::instance(),
									'magic'
								] ) ) || get_option( 'wpb_rs/active', false )
						); ?>
                           value="1"/>
                    <label for="wpb-rs-intro-tab-activation-privacy">
						<?php _e( 'I read and understood the <a href="https://rich-snippets.io/plugin-requirements/?pk_campaign=lets-start-activation-tab#privacy" target="_blank">privacy agreement</a> and I accept it.', 'rich-snippets-schema' ); ?>
                    </label>
                </p>
                <p><a class="button wpb-rs-activation-button button-hero button-primary"
                      href="#"><?php
						if ( ( method_exists( Helper_Model::instance(), 'magic' )
						       && call_user_func( [
									Helper_Model::instance(),
									'magic'
								] ) ) || get_option( 'wpb_rs/active', false ) ) {
							_e( 'Re-Activate', 'rich-snippets-schema' );
						} else {
							_e( 'Let\'s activate your copy', 'rich-snippets-schema' );
						}
						?></a></p>
            </fieldset>

        </form>
    </div>

    <div class="about-wrap-content wpb-rs-intro-tab wpb-rs-intro-tab-updates">
        <div class="headline-feature">
            <h2><?php _e( 'Stay up-to-date', 'rich-snippets-schema' ); ?></h2>
            <p class="lead-description"><?php
				_e( 'You get new plugin updates automatically right into your WordPress dashboard.', 'rich-snippets-schema' )
				?></p>

            <p class="lead-description"><?php
				_e( 'In order to learn more about what is new and what I am currently working on, please subscribe to the mailing list:', 'rich-snippets-schema' )
				?></p>
			<?php
			$user = wp_get_current_user();
			?>
            <form action="https://6c253b06.sibforms.com/serve/MUIEACYRLMxZbvPhwvU03i5muhgcunVYrUGkQBMW8CzL7tRMM9OuiZX7YrJv05DsAQbwoVpN92b1YQvsIUUogNB7dWg9hoHRr8Iqcgc8VgUSIfYmml3PEJYy1sVKgFHpMzmpQV9Yf86AAidsnZJioskgfqw5Iwami09l5ZNsZLYJHQ9mW1Ok-s7UgqAcNtqpmF5xfG0UOsCqiCjN"
                  method="post" target="_blank" class="newsletter">
                <input name="FIRSTNAME" type="text"
                       placeholder="<?php esc_attr_e( 'Your first name', 'rich-snippets-schema' ); ?>"
                       value="<?php echo esc_attr( Helper_Model::instance()->get_current_user_firstname() ); ?>"/>
                <input name="EMAIL" type="email" required="required"
                       value="<?php echo esc_attr( $user->user_email ); ?>"
                       placeholder="<?php esc_attr_e( 'Your E-Mail address', 'rich-snippets-schema' ); ?>"/>
                <input type="hidden" name="locale"
                       value="<?php echo esc_attr( strstr( get_locale(), '_', true ) ); ?>"/>
                <input type="hidden" name="email_address_check" value=""/>
                <input type="hidden" name="html_type" value="simple"/>
				<?php submit_button( __( 'Subscribe', 'rich-snippets-schema' ), 'primary', 'subscribe', false ) ?>
                <p>
                    <small>
						<?php _e( 'Learn more about your privacy in my <a href="https://wp-buddy.com/imprint/" target="_blank">privacy policy</a>.', 'rich-snippets-schema' ); ?>
                    </small>
                </p>
            </form>
            <p class="lead-description">
                <a class="button button-hero button-secondary"
                   href="<?php echo esc_url( admin_url( 'admin.php?page=rich-snippets-schema&tab=training' ) ) ?>"><?php _e( 'Next', 'rich-snippets-schema' ) ?></a>
            </p>
        </div>
    </div>

    <div class="about-wrap-content wpb-rs-intro-tab wpb-rs-intro-tab-training">

		<?php
		$course_url = _x( 'https://rich-snippets.io/structured-data-training-course/', 'URL to structured data training course', 'rich-snippets-schema' );
		$course_url = add_query_arg( [
			'pk_campaign' => 'lets-start-training-tab',
			'pk_source'   => Helper_Model::instance()->get_site_url_host()
		], $course_url );
		?>
        <div class="headline-feature" id="new_to_snip">
            <h2><?php _e( 'Are you new to structured data?', 'rich-snippets-schema' ); ?></h2>
            <p class="lead-description"><?php
				printf( __( 'Structured Data is part of technical SEO. So yes: this whole thing is a technical topic. So to get you started I have a <a href="%s" target="_blank">Structured Data training course</a> available for free that you can take! Start right here:', 'rich-snippets-schema' ), $course_url );
				?></p>
        </div>

        <div class="feature-section has-3-columns is-fullwidth">
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-1/lesson-1/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/1-what-is-structured-data-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'Structured Data in SEO', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'What is structured data and why is it important?', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-1/lesson-1/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo _x( 'Watch now', 'Watch a video', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-1/lesson-2/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/2-how-structured-data-works-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'How Structured Data works', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'What is schema.org and JSON+LD? And how do they work?', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-1/lesson-2/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo _x( 'Watch now', 'Watch a video', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-1/lesson-3/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/3-How-to-find-Schema-Types-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'Schema Types and Properties', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'How to find the right schema types and properties', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-1/lesson-3/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo _x( 'Watch now', 'Watch a video', 'rich-snippets-schema' ); ?></a>
            </div>
        </div>

        <div class="headline-feature" id="work_with_snip">
            <h2><?php _e( 'How to work with SNIP', 'rich-snippets-schema' ); ?></h2>
            <p class="lead-description"><?php _e( 'So now that you know more about Structured Data and Rich Snippets. Let\'s start working with SNIP!', 'rich-snippets-schema' ); ?></p>
        </div>

        <div class="feature-section has-3-columns is-fullwidth">
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-2/lesson-1/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/4-how-to-integrate-structured-data-into-your-site-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'Integrate Structured Data on a page', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'Learn how to integrate structured data to a single post, page or custom post type.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-2/lesson-1/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo _x( 'Watch now', 'Watch a video', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-2/lesson-2/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/5-global-snippets-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'Work with Global Snippets', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'Learn what global snippets are and how you can automate snippet generation.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-2/lesson-2/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo __( 'Watch now', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-2/lesson-3/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/6-overwrite-global-snippets-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'Overwrite Global Properties', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'Learn how you can overwrite properties from Global Snippets in each post or page.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-2/lesson-3/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo __( 'Watch now', 'rich-snippets-schema' ); ?></a>
            </div>
        </div>

        <div class="headline-feature" id="work_with_snip">
            <h2><?php _e( 'Popular Rich Snippets', 'rich-snippets-schema' ); ?></h2>
            <p class="lead-description"><?php _e( 'Let\'s explore which Rich Snippets can be created with SNIP.', 'rich-snippets-schema' ); ?></p>
        </div>

        <div class="feature-section has-3-columns is-fullwidth">
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-1/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/7-how-to-add-structured-data-for-articles-cover-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'The Article Snippet', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'This video is all about Structured Data for Articles, NewsArticles, TechArticles and so on.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-1/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo _x( 'Watch now', 'Watch a video', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-2/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/how-to-create-recipe-structured-data-cover-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'The Recipe Snippet', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'This video is all about the Structured Data that is needed for Recipes.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-2/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo __( 'Watch now', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-3/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/hot-to-create-structured-data-reviews-cover-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'The Review Snippet', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'In this video you’ll learn how to create Reviews using Structured Data.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-3/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo __( 'Watch now', 'rich-snippets-schema' ); ?></a>
            </div>
        </div>

        <div class="feature-section has-3-columns is-fullwidth">
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-4/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/event-snippet-video-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'The Event Snippet', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'In this video I’ll show you how you can create Structured Data for Events that produce a Rich Snippet in search results.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-4/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo _x( 'Watch now', 'Watch a video', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-5/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/product-snippet-preview-image-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'The Product Snippet', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'In this video I want to show you how you can create a Rich Snippet for Products using Structured Data.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-5/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo __( 'Watch now', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-6/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/local-business-video-preview-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'The LocalBusiness Snippet', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'In this video you’ll learn how you can create a knowledge graph card for your local business using Structured Data.', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-6/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo __( 'Watch now', 'rich-snippets-schema' ); ?></a>
            </div>
        </div>

        <div class="feature-section has-3-columns is-fullwidth">
            <div class="column">
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-7/', 'lets-start-activation-tab' ) ); ?>"><img
                            src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>img/training/breadcrumbs-in-search-results-300x169.jpg"
                            width="300" height="169"/></a>
                <h3><?php _e( 'The Event Snippet', 'rich-snippets-schema' ); ?></h3>
                <p><?php _e( 'In this video I’ll show you how you can create Structured Data for Breadcrumbs that produce a Rich Snippet (in this case breadcrumbs) in search results that are clickable, too!', 'rich-snippets-schema' ); ?></p>
                <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/lesson-7/', 'lets-start-activation-tab' ) ); ?>"
                   class="button button-primary button-hero"><?php echo _x( 'Watch now', 'Watch a video', 'rich-snippets-schema' ); ?></a>
            </div>
            <div class="column"></div>
            <div class="column"></div>
        </div>
    </div>

    <div class="about-wrap-content wpb-rs-intro-tab wpb-rs-intro-tab-wheretostart">

        <div class="headline-feature" id="wheretostart">
            <h2><?php _e( 'Now, create your first snip!', 'rich-snippets-schema' ); ?></h2>
            <p class="lead-description"><?php
				_e( 'Only one step is missing now: Create your first snip!', 'rich-snippets-schema' );
				?></p>

            <p class="lead-description">
                <img src="<?php echo esc_url( plugins_url( 'img/wheretostart-blogposts.gif', $s->get_plugin_file() ) ); ?>"
                     alt="<?php esc_attr_e( 'Animated GIF that shows where to add a snip.', 'rich-snippets-schema' ); ?>"/>
            </p>
        </div>

    </div>

    <div class="about-wrap-content wpb-rs-intro-tab wpb-rs-intro-tab-setupservice">
        <div class="headline-feature">
            <h2><?php _e( 'Setup Service', 'rich-snippets-schema' ) ?></h2>
			<?php
			printf(
				'<p class="about-description">%s</p>',
				__( 'For all busy people out there: Use our time saving setup service and let our SEO expert do the hard work for you!', 'rich-snippets-schema' )
			);
			?>
            <a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/setup-service/', 'lets-start-setupservice-tab' ) ); ?>"
               class="button button-primary button-hero"><?php echo _x( 'Find out more', 'about our setup service', 'rich-snippets-schema' ); ?></a>
        </div>

    </div>

    <div class="about-wrap-content wpb-rs-intro-tab wpb-rs-intro-tab-news">
        <div class="headline-feature">
            <h2><?php _e( 'Latest news', 'rich-snippets-schema' ) ?></h2>
			<?php

			ob_start();
			@wp_widget_rss_output(
				'https://rich-snippets.io/category/news/feed/',
				array(
					'show_author'  => 0,
					'show_date'    => true,
					'show_summary' => true,
					'items'        => 5,
				)
			);
			echo preg_replace_callback( '#href=[\'"]+(.*)[\'"]#mU', function ( $matches ) {
				return sprintf( 'href="%s"', Helper_Model::instance()->get_campaignify( $matches[1], 'intro-news' ) );
			}, ob_get_clean() );
			?>
        </div>
        <div class="headline-feature">
            <h2><?php _e( 'Free WordPress News (German language only)', 'rich-snippets-schema' ) ?></h2>
			<?php
			printf(
				'<p class="about-description">%s</p>',
				__( 'Signup to my monthly WordPress newsletter if you\'re interested in monthly news:',
					'rich-snippets-schema' )
			);
			?>
            <form class="newsletter"
                  action="https://6c253b06.sibforms.com/serve/MUIEACYRLMxZbvPhwvU03i5muhgcunVYrUGkQBMW8CzL7tRMM9OuiZX7YrJv05DsAQbwoVpN92b1YQvsIUUogNB7dWg9hoHRr8Iqcgc8VgUSIfYmml3PEJYy1sVKgFHpMzmpQV9Yf86AAidsnZJioskgfqw5Iwami09l5ZNsZLYJHQ9mW1Ok-s7UgqAcNtqpmF5xfG0UOsCqiCjN"
                  method="post"
                  target="_blank">
                <input class="newsletter-input" name="FIRSTNAME"
                       value="<?php echo esc_attr( Helper_Model::instance()->get_current_user_firstname() ); ?>"
                       type="text"
                       placeholder="<?php esc_attr_e( 'Your first name', 'rich-snippets-schema' ); ?>"/>
                <input class="newsletter-input newsletter-email" name="EMAIL"
                       value="<?php echo esc_attr( $user->user_email ); ?>"
                       placeholder="<?php esc_attr_e( 'Your E-Mail address', 'rich-snippets-schema' ); ?>"
                       required="required" type="email"/>
                <input type="hidden" name="locale"
                       value="<?php echo esc_attr( strstr( get_locale(), '_', true ) ); ?>"/>
                <input type="hidden" name="email_address_check" value=""/>
                <input type="hidden" name="html_type" value="simple"/>
				<?php submit_button( __( 'Subscribe', 'rich-snippets-schema' ), '', 'subscribe', false ) ?>
                <p>
					<?php
					printf(
						__( 'Learn more about your privacy in our <a href="%s" target="_blank">privacy policy</a>.', 'rich-snippets-schema' ),
						Helper_Model::instance()->get_campaignify( 'https://florian-simeth.de/impressum.php', 'lets-start-whatsnew-tab' )
					);
					?>
                </p>
            </form>
        </div>

    </div>

</div>
