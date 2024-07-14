<?php get_header(); ?>
<div class="govuk-width-container">
	<?php if(!is_front_page()): ?>
	<a href="/" class="govuk-back-link">Back</a>
	<?php endif; ?>
	<main class="govuk-main-wrapper ">
		<div class="govuk-grid-row govuk-width-container">
			<div class="govuk-grid-column-full-width">
				<div class="govuk-error-summary" data-module="govuk-error-summary">
					<div role="alert">
						<h2 class="govuk-error-summary__title">There is a problem</h2>
						<div class="govuk-error-summary__body">
							<ul class="govuk-list govuk-error-summary__list">
								<li>
									<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404" target="_blank">
										<?php _e( 'Sorry 404 - Article Not Found', 'gdstheme' ); ?></br>
										<?php _e( 'The article you were looking for was not found, but maybe try looking again!', 'gdstheme' ); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			<?php get_search_form(); ?>
		</div>
	</main>
</div>

<?php get_footer(); ?>
