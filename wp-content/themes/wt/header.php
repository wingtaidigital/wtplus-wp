<?php defined('ABSPATH') || exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<?php wp_head(); ?>
		
		<?php
		$description = get_option('blogdescription');

		if ($description)
		{
			?>

			<meta name="description" content="<?php echo sanitize_text_field($description); ?>">

			<?php
		}
		?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-WKPBWSW');</script>
        <!-- End Google Tag Manager -->
	</head>

	<body <?php body_class(); ?>>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WKPBWSW" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

        <!-- End Google Tag Manager (noscript) -->

		<!--[if lte IE 9]>
		<div class="callout alert text-center" style="z-index: 9999">Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</div>
		<![endif]-->

		<header -data-sticky-container>
			<?php /*<div class="callout text-center" style="margin-bottom: 0; padding: 0.5rem; border: 0; background-color: #efeff3; color: #ff4178">wt+ membership is undergoing system upgrade and is temporarily unavailable. Find out more <a href="<?php echo home_url('wt/'); ?>" style="text-decoration: underline">here</a>.</div>*/ ?>

			<div class="title-bar hide-for-large" data-responsive-toggle="main-menu" data-hide-for="large"-data-sticky -data-margin-top="0" -data-sticky-on="medium down">
				<button class="menu-icon" type="button" data-toggle></button>
				<a href="<?php echo home_url(); ?>" class="custom-logo-link title-bar-title" rel="home"><?php the_custom_logo(); ?></a>
				<button type="button" data-toggle="header-search" class="float-right wt-text-white" title="Search"><i class="icon icon-search wt-toggle" aria-hidden="true"></i></button>
			</div>

			<div id="main-menu" class="top-bar wt-gutter-half" -data-sticky -data-margin-top="0" -data-sticky-on="large">
				<div class="row collapse align-bottom">
					<a href="<?php echo home_url(); ?>" class="custom-logo-link column small-12 large-expand shrink show-for-large" rel="home">
						<?php the_custom_logo(); ?>
					</a>

					<?php
					@wp_nav_menu( array(
						'theme_location' => 'main',
						'container_class' => 'column small-12 large-expand',
//						'menu_id' => 'main-menu',
						'menu_class' => 'menu vertical large-horizontal',
						'items_wrap' => '<ul id="%1$s" class="%2$s" data-responsive-menu="drilldown large-dropdown" data-parent-link="true" data-closing-time="0">%3$s</ul>',
						'walker' => new WT_Walker_Nav_Menu()
					) );
					?>

					<div class="column small-12 large-expand shrink">
						<ul class="menu vertical large-horizontal">
							<?php
							if (wt_is_user_logged_in())
							{
								?>

								<li><a href="<?php echo home_url('my-account'); ?>">My Account</a></li>
								<li><a href="<?php echo add_query_arg('loggedout', '', home_url('login')); ?>">Logout</a></li>

								<?php
							}
							else
							{
								?>

								<li>
									<a data-open="login-modal">Log in</a>
									<section class="reveal" id="login-modal" data-reveal>
										<?php get_template_part('template-parts/form/form-login'); ?>

										<button class="close-button" data-close aria-label="Close modal" type="button">
											<span aria-hidden="true" class="wt-sans-serif">&times;</span>
										</button>
									</section>
								</li>

								<li><a href="<?php echo home_url('signup'); ?>" class="button hollow">Sign up for <span class="wt-case-sensitive">wt+</span></a></li>

								<?php
							}
							?>
							<li class="show-for-large"><a title="Search" data-toggle="header-search"><i class="icon icon-search wt-toggle" aria-hidden="true"></i></a></li>
						</ul>
					</div>
				</div>
			</div>

			<div id="header-search" class="hide" data-toggler=".hide">
				<?php get_search_form(); ?>
			</div>

            <?php /*<div class="callout text-center" style="margin-bottom: 0; padding: 0.5rem; border: 0; background-color: #ff4178; color: #fff">
                From 1 Sep 2019, in line with the new PDPA regulations, only your registered mobile or email will be used to verify your membership at our stores.
                <?php if (wt_is_user_logged_in()) { ?>
                    <a href="<?php echo home_url('my-account/profile/'); ?>" style="text-decoration: underline; color: #fff">UFrom 1 Sep 2019pdate your details now</a>.
                <?php } else { ?>
                    <a href="<?php echo home_url('login'); ?>" style="text-decoration: underline; color: #fff">Login to update your details now</a>.
                <?php } ?>
            </div>*/ ?>
		</header>

		<noscript>
			<div class="callout alert text-center">Please enable JavaScript.</div>
		</noscript>

		<main>
