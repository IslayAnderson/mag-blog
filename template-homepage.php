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
            <div class="menu">
                <?php
                wp_nav_menu(array(
                    'menu' => 'main-nav',
                ));
                ?>
            </div>
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
                    <?php if ($i == 0): ?>
                        <a href="<?= get_permalink($posts[$i]->ID) ?>" class="thumbnail">
                            <?php print_r(get_the_post_thumbnail($posts[$i]->ID, 'large')) ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
        <div class="decoration_text d1 big_abstract">
            <span>CODE</span>
        </div>
        <div class="decoration_text d2 big_abstract">
            <span>PHP</span>
        </div>
        <div class="decoration_text d3 big_abstract">
            <span>PHOTOGRAPHY</span>
        </div>
        <script>
            // size the latest post thumbnail so it runs from the excerpt text's left edge to halfway
            // across the decorative text, at 3:2. Lengthen the strip by the space it takes so the
            // excerpts keep their room.
            (function () {
                var thumbnail = document.querySelector('.latest-blogs .thumbnail');
                var decoration = document.querySelector('.d3 span');
                if (!thumbnail || !decoration || !window.ResizeObserver) return;
                var inner = thumbnail.parentElement;
                var range = document.createRange();
                range.selectNodeContents(decoration);

                function fit() {
                    var text = range.getBoundingClientRect();
                    var left = thumbnail.getBoundingClientRect().left;
                    var width = Math.max(0, (text.left + text.right) / 2 - left);
                    var height = width * 2 / 3;
                    var margin = parseFloat(getComputedStyle(thumbnail).marginRight) || 0;
                    // .inner is rotated, so on-screen width is the element's height
                    thumbnail.style.height = width + 'px';
                    thumbnail.style.width = height + 'px';
                    inner.style.setProperty('--thumbnail-space', (height + margin) + 'px');
                }

                new ResizeObserver(fit).observe(document.querySelector('.spread .page'));
                if (document.fonts) document.fonts.ready.then(fit);
            })();
        </script>
    </div>
</div>
<?php get_footer(); ?>
