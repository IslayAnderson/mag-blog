<?php
/**
 * Template Name: Homepage
 */
get_header(); ?>
<div class="spread">
    <div class="page p1">
        <div class="feature">
            <?= get_the_post_thumbnail(); ?>
            <h1 class="accent"><?= get_the_title() ?></h1>
        </div>
        <div class="latest-blogs">
            <?php
            $args = array(
                'post_type' => array('post'),
                'post_status' => array('publish'),
            );
            $query = new WP_Query($args);
            $posts = (array)$query->posts;
            ?>
            <div class="inner">
                <?php for ($i = 0; $i <= 3; $i++) : ?>
                    <a href="<?= get_permalink($posts[$i]->ID) ?>">
                        <p><?= implode(' ', array_slice(explode(' ', strip_tags($posts[$i]->post_content)), 0, 30)) ?></p>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <div class="menu">
            <?php
            wp_nav_menu(array(
                'menu' => 'main-nav',
            ));
            ?>
        </div>
        <div class="decoration_text d1 big_abstract">
            <span>CODE</span>
        </div>
        <div class="decoration_text d2 big_abstract">
            <span>PHP</span>
        </div>
        <div class="decoration_text d3 big_abstract">
            <span>ART</span>
        </div>
    </div>
</div>
<?php get_footer(); ?>
