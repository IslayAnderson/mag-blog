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
                    <?php $words = preg_split('/\s+/', trim(strip_tags($posts[$i]->post_content)), -1, PREG_SPLIT_NO_EMPTY); ?>
                    <a href="<?= get_permalink($posts[$i]->ID) ?>">
                        <?php // 30 words without the script; the script fills the space from the longer data-excerpt ?>
                        <p data-excerpt="<?= esc_attr(implode(' ', array_slice($words, 0, 300))) ?>"><?= esc_html(implode(' ', array_slice($words, 0, 30))) ?></p>
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
            // Lay out the latest posts strip:
            // - the thumbnail is 3:2 and runs from the excerpt text's left edge to just before the
            //   decorative text's letters, with its bottom lined up with the feature image
            // - each excerpt wraps to exactly the thumbnail's width
            // - the excerpt before the thumbnail fills the space above it, and the excerpts after it share
            //   the space down to the PHP decoration (or the bottom of the page if that is out of the way)
            // .inner is rotated 90deg, so for anything inside it on-screen width is the element's height,
            // on-screen height is its width and the space above it is its left margin.
            (function () {
                var thumbnail = document.querySelector('.latest-blogs .thumbnail');
                var decoration = document.querySelector('.d3 span');
                var footer = document.querySelector('.d2 span');
                var feature = document.querySelector('.feature img');
                var page = document.querySelector('.spread .page');
                if (!thumbnail || !decoration || !footer || !feature || !page || !window.ResizeObserver) return;
                var inner = thumbnail.parentElement;

                function excerptsIn(links) {
                    return links.map(function (a) {
                        var p = a.querySelector('p');
                        return p && { p: p, words: (p.getAttribute('data-excerpt') || '').split(' ').filter(Boolean) };
                    }).filter(function (e) { return e && e.words.length; });
                }
                var links = Array.prototype.slice.call(inner.children);
                var index = links.indexOf(thumbnail);
                var before = excerptsIn(links.slice(0, index));
                var after = excerptsIn(links.slice(index + 1));

                // zero-size markers on the decorative texts' baselines
                function marker(text) {
                    var span = document.createElement('span');
                    span.setAttribute('aria-hidden', 'true');
                    span.style.cssText = 'display:inline-block;width:0;height:0;vertical-align:baseline';
                    text.style.whiteSpace = 'nowrap'; // keep the marker on the same line as the letters
                    text.appendChild(span);
                    return span;
                }
                // PHOTOGRAPHY is rotated so its baseline is the visible left edge of the letters (no descenders)
                var decorationEdge = marker(decoration);
                var footerBaseline = marker(footer);
                var offset = 0;

                // wrap an excerpt into whole lines that exactly fill the given on-screen width: pick the
                // line count from the natural line height, stretch the line height to fill, then find the
                // shortest line length that fits the text in that many lines
                function wrapTo(p, width) {
                    var lines = Math.max(1, Math.round(width / (parseFloat(getComputedStyle(p).fontSize) * 1.2)));
                    p.style.lineHeight = (width / lines) + 'px';
                    var low = 1, high = 5000;
                    while (high - low > 0.5) {
                        var mid = (low + high) / 2;
                        p.style.width = mid + 'px';
                        if (p.offsetHeight <= width + 0.5) high = mid; else low = mid;
                    }
                    p.style.width = high + 'px';
                }

                // use as many words as fit in the given on-screen height at the given width
                function fill(excerpt, width, length) {
                    var p = excerpt.p;
                    p.parentElement.style.flex = 'none';
                    var low = 0, high = excerpt.words.length;
                    while (low < high) {
                        var mid = Math.ceil((low + high) / 2);
                        p.textContent = excerpt.words.slice(0, mid).join(' ');
                        wrapTo(p, width);
                        if (p.offsetWidth <= length) low = mid; else high = mid - 1;
                    }
                    p.textContent = excerpt.words.slice(0, low).join(' ');
                    wrapTo(p, width);
                }

                function fit() {
                    var box = thumbnail.getBoundingClientRect();
                    // gaps match the gap between excerpts
                    var gap = parseFloat(getComputedStyle(thumbnail).marginRight) || 0;
                    var width = Math.max(0, decorationEdge.getBoundingClientRect().left - gap - box.left);
                    var height = width * 2 / 3;
                    var featureBottom = feature.getBoundingClientRect().bottom;
                    var top = inner.getBoundingClientRect().top;

                    thumbnail.style.height = width + 'px';
                    thumbnail.style.width = height + 'px';

                    // above the thumbnail
                    var above = (featureBottom - height - top - gap * before.length) / Math.max(1, before.length);
                    before.forEach(function (e) { fill(e, width, above); });

                    box = thumbnail.getBoundingClientRect();
                    var naturalTop = box.top - offset;
                    offset = Math.max(0, featureBottom - height - naturalTop);
                    thumbnail.style.marginLeft = offset + 'px';

                    // below the thumbnail: down to the PHP letters if they sit under the excerpts, else the page
                    var left = box.left, right = box.left + width;
                    var limit = page.getBoundingClientRect().bottom - gap;
                    var footerRange = document.createRange();
                    footerRange.selectNodeContents(footer.firstChild);
                    var footerBox = footerRange.getBoundingClientRect();
                    if (footerBox.right > left && footerBox.left < right) {
                        // cap height of Courier New is about 0.6em above the baseline
                        var capTop = footerBaseline.getBoundingClientRect().top - parseFloat(getComputedStyle(footer).fontSize) * 0.6;
                        limit = Math.min(limit, capTop - gap);
                    }
                    var start = thumbnail.getBoundingClientRect().bottom + gap;
                    var below = (limit - start - gap * (after.length - 1)) / Math.max(1, after.length);
                    after.forEach(function (e) { fill(e, width, Math.max(0, below)); });
                }

                new ResizeObserver(fit).observe(page);
                if (document.fonts) document.fonts.ready.then(fit);
            })();
        </script>
    </div>
</div>
<?php get_footer(); ?>
