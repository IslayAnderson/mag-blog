<?php get_header(); ?>

    <div class="basic">
        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" role="article">
                <h1><?php the_title(); ?></h1>
                <?php the_content(); ?>
            </article>
        <?php endwhile; ?>
    </div>

<?php get_footer(); ?>