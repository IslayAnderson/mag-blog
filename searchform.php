<form role="search" method="get" id="searchform" class="searchform" action="<?php echo home_url('/'); ?>">
    <div class="govuk-form-group">
        <label class="govuk-label" for="s"><?php _e('Search for:', 'mag_blog_theme'); ?></label>
        <input class="govuk-input govuk-!-width-two-thirds" type="search" id="s" name="s" value=""/>
        <button class="govuk-button govuk-button--secondary govuk-!-static-margin-bottom-0" data-module="govuk-button"
                type="submit" id="searchsubmit"><?php _e('Search', 'mag_blog_theme'); ?></button>
</form>