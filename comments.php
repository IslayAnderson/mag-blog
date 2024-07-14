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
