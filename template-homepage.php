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
            // size the latest post thumbnail at 3:2 so it runs from the excerpt text's left edge to just
            // before the decorative text's letters, and push it down so its bottom lines up with the feature image.
            // Lengthen the strip by the space it takes so the excerpts keep their room.
            (function () {
                var thumbnail = document.querySelector('.latest-blogs .thumbnail');
                var decoration = document.querySelector('.d3 span');
                var feature = document.querySelector('.feature img');
                if (!thumbnail || !decoration || !feature || !window.ResizeObserver) return;
                var inner = thumbnail.parentElement;
                // zero-size marker on the decorative text's baseline; the text is rotated so the baseline
                // is the visible left edge of the letters (PHOTOGRAPHY has no descenders)
                var baseline = document.createElement('span');
                baseline.setAttribute('aria-hidden', 'true');
                baseline.style.cssText = 'display:inline-block;width:0;height:0;vertical-align:baseline';
                decoration.style.whiteSpace = 'nowrap'; // keep the marker on the same line as the letters
                decoration.appendChild(baseline);
                var offset = 0;

                function fit() {
                    var box = thumbnail.getBoundingClientRect();
                    // gap to the decorative text matches the gap between excerpts
                    var gap = parseFloat(getComputedStyle(thumbnail).marginRight) || 0;
                    var width = Math.max(0, baseline.getBoundingClientRect().left - gap - box.left);
                    var height = width * 2 / 3;
                    var naturalTop = box.top - offset;
                    offset = Math.max(0, feature.getBoundingClientRect().bottom - height - naturalTop);
                    // .inner is rotated, so on-screen width is the element's height and the
                    // space above it is its left margin
                    thumbnail.style.height = width + 'px';
                    thumbnail.style.width = height + 'px';
                    thumbnail.style.marginLeft = offset + 'px';
                    inner.style.setProperty('--thumbnail-space', (offset + height + gap) + 'px');
                }

                new ResizeObserver(fit).observe(document.querySelector('.spread .page'));
                if (document.fonts) document.fonts.ready.then(fit);
            })();
        </script>
    </div>
</div>
<?php get_footer(); ?>
