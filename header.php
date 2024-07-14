<!doctype html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php wp_title(''); ?></title>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>

		<?php /*
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png"> 
		<meta name="theme-color" content="#121212">

		*/?>

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>

	</head>

	<body class="govuk-template__body js-enabled govuk-frontend-supported govuk-body <?= gdstheme_body_class('string') ?>" itemscope itemtype="http://schema.org/WebPage">
		
	<header class="govuk-header" data-module="govuk-header">
		<div class="govuk-header__container govuk-width-container">
			<div class="govuk-header__logo">
				<a href="#" class="govuk-header__link govuk-header__link--homepage"> </a>
			</div>
			<div class="govuk-header__content">
				<a href="<?php echo home_url(); ?>" class="govuk-header__link govuk-header__service-name"><?php bloginfo('name'); ?></a>
				<nav aria-label="Menu" class="govuk-header__navigation">
					<button type="button" class="govuk-header__menu-button govuk-js-header-toggle" aria-controls="navigation" hidden>
					Menu
					</button>
						<?php 
						function gdstheme_menu_classes($classes, $item, $args) {
							if($args->theme_location == 'main-nav') {
								$classes[] = 'govuk-header__navigation-item';
							}
							return $classes;
						}
						add_filter('nav_menu_css_class','gdstheme_menu_classes',1,3);

						function gdstheme_add_link_atts($atts) {
							$atts['class'] = "govuk-header__link";
							return $atts;
						}
						add_filter( 'nav_menu_link_attributes', 'gdstheme_add_link_atts');

						function gdstheme_special_nav_class($classes, $item){
							if( in_array('current-menu-item', $classes) ){
								$classes[] = 'govuk-header__navigation-item--active ';
							}
							return $classes;
						}
						add_filter('gdstheme_special_nav_class' , 'special_nav_class' , 10 , 2);
						wp_nav_menu(
							array(
								'container' => false,                           		// remove nav container
								//'items_wrap' => '<ul id="%1$s" class="%2$s govuk-header__navigation-list">%3$s</ul>',
								'container_class' => '',   // class of container (should you choose to use it)
								'menu' => __( 'The Main Menu', 'gdstheme' ),  			// nav name
								'menu_class' => 'govuk-header__navigation-list',               	// adding custom nav class
								'theme_location' => 'main-nav',                 		// where it's located in the theme
								'before' => '',                                 		//	 before the menu
								'after' => '',                                 			// after the menu
								'link_before' => '',                            		// before each link
								'link_after' => '',                             		// after each link
								'depth' => 0,                                   		// limit the depth of the nav
								'fallback_cb' => '',                             		// fallback function (if there is one)
							)
						);
						
						?>
					</ul>
				</nav>
			</div>
		</div>
	</header>