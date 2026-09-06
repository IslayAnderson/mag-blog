<?php get_header(); ?>

    <div class="spread">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="page p1">
                <article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
                    <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                        <div class="title">
                            <h2 class="big_abstract"><?php strlen(the_title(), 20, 0); ?></h2>
                        </div>
                        <div class="excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <div class="featured">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    </a>
                </article>
            </div>
        <?php endwhile; ?>

            <?php mag_blog_theme_page_navi(); ?>

        <?php else : ?>
            <div class="page p1">
                <article id="post-not-found" class="">
                    <?php mag_blog_theme_error('Post not found'); ?>
                </article>
            </div>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>