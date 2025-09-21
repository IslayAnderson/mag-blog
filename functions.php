<?php

function mag_blog_theme_head_cleanup()
{
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_generator');

}

// http://www.deluxeblogtips.com/2012/03/better-title-meta-tag.html
function rw_title($title, $sep, $seplocation)
{
    global $page, $paged;
    if (is_feed()) return $title;
    if ('right' == $seplocation) {
        $title .= get_bloginfo('name');
    } else {
        $title = get_bloginfo('name') . $title;
    }
    $site_description = get_bloginfo('description', 'display');
    if ($site_description && (is_home() || is_front_page())) {
        $title .= " {$sep} {$site_description}";
    }
    if ($paged >= 2 || $page >= 2) {
        $title .= " {$sep} " . sprintf(__('Page %s', 'dbt'), max($paged, $page));
    }

    return $title;

}

// remove WP version from RSS
function mag_blog_theme_rss_version()
{
    return '';
}

// remove injected CSS for recent comments widget
function mag_blog_theme_remove_wp_widget_recent_comments_style()
{
    if (has_filter('wp_head', 'wp_widget_recent_comments_style')) {
        remove_filter('wp_head', 'wp_widget_recent_comments_style');
    }
}

// remove injected CSS from recent comments widget
function mag_blog_theme_remove_recent_comments_style()
{
    global $wp_widget_factory;
    if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
        remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
    }
}


function mag_blog_theme_scripts_and_styles()
{
    global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
    if (!is_admin()) {
        // register main stylesheet
        wp_register_style('stylesheet', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), '', 'all');

        // enqueue styles and scripts
        wp_enqueue_style('stylesheet');
    }
}


function mag_blog_theme_theme_support()
{
    // rss
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');

    // adding post format support
    add_theme_support('post-formats',
        array(
            'aside',             // title less blurb
            'gallery',           // gallery of images
            'link',              // quick link to other site
            'image',             // an image
            'quote',             // a quick quote
            'status',            // a Facebook like status update
            'video',             // video
            'audio',             // audio
            'chat',               // chat transcript
        )
    );

    // wp menus
    add_theme_support('menus');

    // register menus
    register_nav_menus(
        array(
            'main-nav' => __('The Main Menu', 'mag_blog_theme'),   // main nav in header
            'footer-links' => __('Footer Links', 'mag_blog_theme') // secondary nav in footer
        )
    );

}


// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function mag_blog_theme_filter_ptags_on_images($content)
{
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// modify read more 
function mag_blog_theme_excerpt_more($more)
{
    global $post;
    return '...  <a class="excerpt-read-more" href="' . get_permalink($post->ID) . '" title="' . __('Read ', 'mag_blog_theme') . esc_attr(get_the_title($post->ID)) . '">' . __('Read more &raquo;', 'mag_blog_theme') . '</a>';
}

function mag_blog_theme_wp_settings_scss_compile($args = null)
{
    $compiler = new ScssPhp\ScssPhp\Compiler();
    $compressor = new tubalmartin\CssMin\Minifier();

    $source_scss = get_template_directory() . '/assets/sass/main.scss';
    $scssContents = file_get_contents($source_scss);
    $import_path = get_template_directory() . '/assets/sass';
    $compiler->addImportPath($import_path);
    $target_css = get_template_directory() . '/assets/css/main.css';
    $stylesheetRel = explode($_SERVER['SERVER_NAME'], get_template_directory_uri())[1] . '/assets/';

    $variables = [
        '$govuk-assets-path' => $stylesheetRel
    ];

    if (!is_null($args)) {
        $variables = array_merge($variables, $args);
        $target_css = get_template_directory() . '/assets/css/test.css';
    }

    $compiler->setVariables($variables);

    $css = $compiler->compile($scssContents);
    if (!empty($css) && is_string($css)) {
        file_put_contents($target_css, $css);
    }

    $minified_css = $compressor->run(file_get_contents($target_css));
    if (!empty($minified_css) && is_string($minified_css)) {
        file_put_contents($target_css, $minified_css);
    }
}

function mag_blog_theme_launch()
{

    // include composer
    add_action('init', function () {
        include(get_template_directory() . '/vendor/autoload.php');
    });

    // launching operation cleanup
    add_action('init', 'mag_blog_theme_head_cleanup');
    // A better title
    add_filter('wp_title', 'rw_title', 10, 3);
    // remove WP version from RSS
    add_filter('the_generator', 'mag_blog_theme_rss_version');
    // remove injected css from comments widget
    add_filter('wp_head', 'mag_blog_theme_remove_wp_widget_recent_comments_style', 1);
    add_action('wp_head', 'mag_blog_theme_remove_recent_comments_style', 1);

    // enqueue base scripts and styles
    add_action('wp_enqueue_scripts', 'mag_blog_theme_scripts_and_styles', 999);

    // launching this stuff after theme setup
    mag_blog_theme_theme_support();

    // remove p tags
    add_filter('the_content', 'mag_blog_theme_filter_ptags_on_images');
    // modfy excerpt
    add_filter('excerpt_more', 'mag_blog_theme_excerpt_more');

    // add wordpress constants to scss
//    add_action('after_setup_theme', 'mag_blog_theme_wp_settings_scss_compile');

    // build customiser scss
    add_action('customize_save_after', 'mag_blog_theme_wp_settings_scss_customiser');
    add_action('customize_save_after', 'mag_blog_theme_wp_settings_scss_compile');

}

// run actions on init
add_action('after_setup_theme', 'mag_blog_theme_launch');

//check is acf is installed
function acf_admin_notice($activate = 'install')
{
    $class = 'notice notice-error';
    $url = 'https://www.advancedcustomfields.com/';
    $message = __($activate == 'install' ? 'Please install' : 'Please activate', 'sample-text-domain');

    printf('<div class="%1$s"><p>%2$s <a href="%3$s">Advanced Custom Fields Pro</a></p></div>', esc_attr($class), esc_html($message), $url);
}

if (!in_array('advanced-custom-fields-pro/acf.php', get_option('active_plugins')) && isset(get_plugins()['advanced-custom-fields-pro/acf.php'])) {
    add_action('admin_notices', function () {
        acf_admin_notice('activate');
    });
} elseif (!in_array('advanced-custom-fields-pro/acf.php', get_option('active_plugins'))) {
    add_action('admin_notices', function () {
        acf_admin_notice('install');
    });
} else {
    include __DIR__ . '/advanced_custom_fields/acf.php';
}


//disable block editor
add_filter('use_block_editor_for_post', '__return_false');


?>
