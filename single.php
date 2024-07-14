<?php get_header(); ?>
<div class="govuk-width-container">
<a href="/" class="govuk-back-link">Back</a>
	<main class="govuk-main-wrapper ">
		<div class="govuk-grid-row govuk-width-container">
			<div class="govuk-grid-column-full-width">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
					
					<h1 class="govuk-heading-l"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					<div class="govuk-inset-text">
					<?php printf( __
							( 'Posted', 'gdstheme' ).' %1$s %2$s',
							'<time class="updated entry-time" datetime="' . get_the_time('Y-m-d') . '" itemprop="datePublished">' . get_the_time(get_option('date_format')) . '</time>',
							'<span class="by">'.__( 'by', 'gdstheme').'</span> <span class="entry-author author" itemprop="author" itemscope itemptype="http://schema.org/Person">' . get_the_author_link( get_the_author_meta( 'ID' ) ) . '</span>'
						); ?>	
					</div>
					<?php if(has_post_thumbnail()):?>
						<div class="featured">
							<?php the_post_thumbnail('large'); ?>
						</div>
					<?php endif; ?>
					<section>
						<?php the_content(); ?>
					</section>

				</article>
				<h2 class="govuk-heading-m">Comments:</h2>
				<?php
				$args = array(
					'post_id' => get_the_ID(),
					'orderby' => array(
						'comment_date' => 'ASC'
					),
				);

				// The comment Query
				$comments_query = new WP_Comment_Query();
				$comments       = $comments_query->query( $args );

				// Comment Loop
				if ( $comments ) {
					foreach ( $comments as $comment ) {
						gdstheme_comments($comment);
					}
				} else {
					gdstheme_error('No comments found.');
				}
				
				?>

				<div id="respond" class="comment-respond">
					<h3 id="reply-title" class="comment-reply-title">Leave a Reply 
						<small>
							<a rel="nofollow" id="cancel-comment-reply-link" href="/socialchaff-an-exercise-in-obfuscation-by-hiding-in-plain-sight/#respond" style="display:none;">Cancel reply</a>
						</small>
					</h3>
					<form action="/wp-comments-post.php" method="post" id="commentform" class="comment-form">
						<div class="govuk-form-group">
							<label class="govuk-label" for="more-detail">Comment</label>
							<textarea class="govuk-textarea" id="comment" name="comment" rows="5" aria-describedby="more-detail-hint"></textarea>
						</div> 
						<div class="govuk-form-group">
							<label class="govuk-label" for="full-name">User name</label>
							<input class="govuk-input" id="author" name="author" type="text" spellcheck="false" autocomplete="name">
						</div>
						<div class="govuk-form-group">
							<label class="govuk-label" for="email">Email address</label>
							<input class="govuk-input" id="email" name="email" type="email" spellcheck="false" aria-describedby="email-hint" autocomplete="email">
						</div>
						<div class="govuk-form-group">
							<label class="govuk-label" for="email">Website</label>
							<input class="govuk-input" id="url" name="url" type="text" spellcheck="false" autocomplete="url">
						</div>
						<input name="submit" type="submit" id="submit" class="govuk-button" data-module="govuk-button" value="Post Comment">
						<input type="hidden" name="comment_post_ID" value="<?= get_the_ID() ?>" id="comment_post_ID">
						<input type="hidden" name="comment_parent" id="comment_parent" value="0">
						</p>
					</form>	
				</div>

				<?php endwhile; ?>

					<?php gdstheme_page_navi(); ?>

				<?php else : ?>

				<article id="post-not-found" class="govuk-grid-column-full">
					<?php gdstheme_error('Post not found'); ?>
				</article>

				<?php endif; ?>
			</div>
		</div>
	</main>
</div>

<?php get_footer(); ?>
