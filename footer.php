			<footer class="govuk-footer">
				<div class="govuk-width-container">
					<div class="govuk-footer__meta">
						<div class="govuk-footer__meta-item govuk-footer__meta-item--grow">
							<?php get_sidebar(); ?>
							<h2 class="govuk-visually-hidden"><!-- widget --></h2>
							<ul class="govuk-footer__inline-list">
								<?php wp_nav_menu(array(
									'container' => '',                           // enter '' to remove nav container (just make sure .footer-links in _base.scss isn't wrapping)
									'container_class' => 'govuk-footer__inline-list-item',         // class of container (should you choose to use it)
									'menu' => __( 'Footer Links', 'gdstheme' ),   // nav name
									'menu_class' => 'govuk-footer__link',            // adding custom nav class
									'theme_location' => 'footer-links',             // where it's located in the theme
									'before' => '',                                 // before the menu
									'after' => '',                                  // after the menu
									'link_before' => '',                            // before each link
									'link_after' => '',                             // after each link
									'depth' => 0,                                   // limit the depth of the nav
									'fallback_cb' => 'gdstheme_footer_links_fallback'  // fallback function
									)); 
								?>
							</ul>
							<span class="govuk-footer__licence-description"><!-- widget --></span>
						</div>
						<div class="govuk-footer__meta-item">
							<a class="govuk-footer__link govuk-footer__copyright-logo" href="">© <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?></a>
						</div>
					</div>
				</div>
			</footer>
			
		<?php wp_footer(); ?>

	</body>

</html> 
