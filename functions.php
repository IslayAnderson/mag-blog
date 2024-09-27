<?php

function gdstheme_head_cleanup() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	remove_action( 'wp_head', 'wp_generator' );

} 

// http://www.deluxeblogtips.com/2012/03/better-title-meta-tag.html
function rw_title( $title, $sep, $seplocation ) {
  global $page, $paged;
  if ( is_feed() ) return $title;
  if ( 'right' == $seplocation ) {
	$title .= get_bloginfo( 'name' );
  } else {
    $title = get_bloginfo( 'name' ) . $title;
  }
  $site_description = get_bloginfo( 'description', 'display' );
  if ( $site_description && ( is_home() || is_front_page() ) ) {
    $title .= " {$sep} {$site_description}";
  }
  if ( $paged >= 2 || $page >= 2 ) {
    $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
  }

  return $title;

} 

// remove WP version from RSS
function gdstheme_rss_version() { return ''; }

// remove injected CSS for recent comments widget
function gdstheme_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function gdstheme_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}


function gdstheme_scripts_and_styles() {
	global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
	if (!is_admin()) {
		// register main stylesheet
		wp_register_style( 'stylesheet', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), '', 'all' );
		wp_register_style( 'additional_stylesheet', get_stylesheet_directory_uri() . '/assets/css/additional.css', array(), '', 'all' );

		// enqueue styles and scripts
		wp_enqueue_style( 'stylesheet' );
		wp_enqueue_style( 'additional_stylesheet' );
	}
}


function gdstheme_theme_support() {
	// rss
	add_theme_support('automatic-feed-links');
	add_theme_support('post-thumbnails');

	// adding post format support
	add_theme_support( 'post-formats',
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
	add_theme_support( 'menus' );

	// register menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'gdstheme' ),   // main nav in header
			'footer-links' => __( 'Footer Links', 'gdstheme' ) // secondary nav in footer
		)
	);

} 

// Pagination
function gdstheme_page_navi() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
	$pages = paginate_links( array(
		'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
		'format'       => '',
		'current'      => max( 1, get_query_var('paged') ),
		'total'        => $wp_query->max_num_pages,
		'prev_text'    => '<svg class="govuk-pagination__icon govuk-pagination__icon--prev" xmlns="http://www.w3.org/2000/svg" height="13" width="15" aria-hidden="true" focusable="false" viewBox="0 0 15 13"><path d="m6.5938-0.0078125-6.7266 6.7266 6.7441 6.4062 1.377-1.449-4.1856-3.9768h12.896v-2h-12.984l4.2931-4.293-1.414-1.414z"></path></svg> <span class="govuk-pagination__link-title">Previous<span class="govuk-visually-hidden"> page</span></span>',
		'next_text'    => '<span class="govuk-pagination__link-title">Next<span class="govuk-visually-hidden"> page</span><svg class="govuk-pagination__icon govuk-pagination__icon--next" xmlns="http://www.w3.org/2000/svg" height="13" width="15" aria-hidden="true" focusable="false" viewBox="0 0 15 13"><path d="m8.107-0.0078125-1.4136 1.414 4.2926 4.293h-12.986v2h12.896l-4.1855 3.9766 1.377 1.4492 6.7441-6.4062-6.7246-6.7266z"></path></svg>',
		'type'         => 'array',
		'end_size'     => 3,
		'mid_size'     => 3
	  ) );
	  ?>
	<nav class="govuk-pagination">
		<ul class="govuk-pagination__list">
		<?php
		foreach($pages as $key=>$page){
			?>
			<li class="govuk-pagination__item">
				<?=$page?>
			</li>
			<?php
		}
		?>
		</ul>
	</nav>
  <?php
} 

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function gdstheme_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// modify read more 
function gdstheme_excerpt_more($more) {
	global $post;
	return '...  <a class="excerpt-read-more" href="'. get_permalink( $post->ID ) . '" title="'. __( 'Read ', 'gdstheme' ) . esc_attr( get_the_title( $post->ID ) ).'">'. __( 'Read more &raquo;', 'gdstheme' ) .'</a>';
}

function gdstheme_wp_settings_scss_customiser($wp_customize) {
	$log = fopen(get_template_directory() . '/assets/css/customizer_changes.log', 'a');
	$additional_css = fopen(get_template_directory() . '/assets/sass/colour.scss', 'w');
	fwrite($log, date('Y-m-d H:i:s.u') . " - Customizer changes saved start \n");
	$colors = array( 'govuk-brand-colour', 'govuk-text-colour', 'govuk-canvas-background-colour', 'govuk-body-background-colour', 'govuk-print-text-colour', 'govuk-secondary-text-colour', 'govuk-focus-colour', 'govuk-focus-text-colour', 'govuk-error-colour', 'govuk-success-colour', 'govuk-border-colour', 'govuk-input-border-colour', 'govuk-hover-colour', 'govuk-link-colour', 'govuk-link-visited-colour', 'govuk-link-hover-colour', 'govuk-link-active-colour');
	
	 $out = '';
	 foreach( $colors as $color) {

		$control = $wp_customize->get_control($color);
		$value = $control->value();
		$out .= "$$color : $value ; \n";
 
	 }

	fwrite($additional_css, $out);
	try{
		gdstheme_wp_settings_scss_compile();
	}
	catch (Exception $e) {
		fwrite($log, date('Y-m-d H:i:s.u') . " - 'Caught exception: " . $e->getMessage() . "\n");
	}

	fwrite($log, date('Y-m-d H:i:s.u') . " - Customizer changes saved end \n");
	fclose($additional_css);
	fclose($log);
}

function gdstheme_wp_settings_scss_compile($args = null){
	$compiler = new ScssPhp\ScssPhp\Compiler();
	$compressor = new tubalmartin\CssMin\Minifier();
	
	$source_scss = get_template_directory() . '/assets/sass/main.scss';
	$scssContents = file_get_contents($source_scss);
	$import_path = get_template_directory() . '/assets/sass';
	$compiler->addImportPath($import_path);
	$target_css = get_template_directory() . '/assets/css/main.css';
	$stylesheetRel = explode($_SERVER['SERVER_NAME'],get_template_directory_uri())[1] . '/assets/';
	
	$variables = [
		'$govuk-assets-path' => $stylesheetRel
	];

	if(!is_null($args)){
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

function gdstheme_launch() {

	// include composer 
	add_action('init', function(){
		include(get_template_directory(). '/vendor/autoload.php');
	});

	// launching operation cleanup
	add_action( 'init', 'gdstheme_head_cleanup' );
	// A better title
	add_filter( 'wp_title', 'rw_title', 10, 3 );
	// remove WP version from RSS
	add_filter( 'the_generator', 'gdstheme_rss_version' );
	// remove injected css from comments widget
	add_filter( 'wp_head', 'gdstheme_remove_wp_widget_recent_comments_style', 1 );
	add_action( 'wp_head', 'gdstheme_remove_recent_comments_style', 1 );

	// enqueue base scripts and styles
	add_action( 'wp_enqueue_scripts', 'gdstheme_scripts_and_styles', 999 );

	// launching this stuff after theme setup
	gdstheme_theme_support();

	// add sidebars
	add_action( 'widgets_init', 'gdstheme_register_sidebars' );

	// remove p tags
	add_filter( 'the_content', 'gdstheme_filter_ptags_on_images' );
	// modfy excerpt
	add_filter( 'excerpt_more', 'gdstheme_excerpt_more' );

	// add wordpress constants to scss
	add_action('after_setup_theme', 'gdstheme_wp_settings_scss_compile');

	// build customiser scss
	add_action('customize_save_after', 'gdstheme_wp_settings_scss_customiser');

} 

// run actions on init
add_action( 'after_setup_theme', 'gdstheme_launch' );



function gdstheme_theme_customizer($wp_customize) {
	// $wp_customize calls go here.
	// add sections
	$GLOBALS['colors'] = array( 
		array(
			'slug'=>'govuk-brand-colour',
			'default' => '#1d70b8',
			'label' => __('Brand colour', 'gds')
		),
		array(
			'slug'=>'govuk-text-colour',
			'default' => '#0b0c0c',
			'label' => __('Text colour', 'gds')
		),
		array(
			'slug'=>'govuk-canvas-background-colour',
			'default' => '#f3f2f1',
			'label' => __('Canvas background colour', 'gds')
		),
		array(
			'slug'=>'govuk-body-background-colour',
			'default' => '#ffffff',
			'label' => __('Body background colour', 'gds')
		),
		array(
			'slug'=>'govuk-print-text-colour',
			'default' => '#000000',
			'label' => __('Print text colour', 'gds')
		),
		array(
			'slug'=>'govuk-secondary-text-colour',
			'default' => '#505a5f',
			'label' => __('Secondary text colour', 'gds')
		),
		array(
			'slug'=>'govuk-focus-colour',
			'default' => '#ffdd00',
			'label' => __('Focus colour', 'gds')
		),
		array(
			'slug'=>'govuk-focus-text-colour',
			'default' => '#0b0c0c',
			'label' => __('Focus text colour', 'gds')
		),
		array(
			'slug'=>'govuk-error-colour',
			'default' => '#d4351c',
			'label' => __('Error colour', 'gds')
		),
		array(
			'slug'=>'govuk-success-colour',
			'default' => '#00703c',
			'label' => __('Success colour', 'gds')
		),
		array(
			'slug'=>'govuk-border-colour',
			'default' => '#b1b4b6',
			'label' => __('Border colour', 'gds')
		),
		array(
			'slug'=>'govuk-input-border-colour',
			'default' => '#0b0c0c',
			'label' => __('Input border colour', 'gds')
		),
		array(
			'slug'=>'govuk-hover-colour',
			'default' => '#b1b4b6',
			'label' => __('Hover colour', 'gds')
		),
		array(
			'slug'=>'govuk-link-colour',
			'default' => '#1d70b8',
			'label' => __('Link colour', 'gds')
		),
		array(
			'slug'=>'govuk-link-visited-colour',
			'default' => '#4c2c92',
			'label' => __('Link visited colour', 'gds')
		),
		array(
			'slug'=>'govuk-link-hover-colour',
			'default' => '#003078',
			'label' => __('Link hover colour', 'gds')
		),
		array(
			'slug'=>'govuk-link-active-colour',
			'default' => '#0b0c0c',
			'label' => __('Link active colour', 'gds')
		)
	);
	foreach( $GLOBALS['colors'] as $color ) {
		$wp_customize->add_setting(
			$color['slug'], array(
			'default' => $color['default'],
			'type' => 'option',
			'capability' =>
			'edit_theme_options'
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
			$wp_customize,
			$color['slug'],
			array('label' => $color['label'],
			'section' => 'colors',
			'settings' => $color['slug'])
			)
		);
	}
}

add_action( 'customize_register', 'gdstheme_theme_customizer' );



// Sidebars & Widgets
function gdstheme_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'gdstheme' ),
		'description' => __( 'The first (primary) sidebar.', 'gdstheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	
} 

// Comment Layout
function gdstheme_comments( $comment ) {
   $GLOBALS['comment'] = $comment; ?>
  	<div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
		<div class="govuk-inset-text">
			<?php
			$bgauthemail = get_comment_author_email();
			?>
			<img src="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>" class="load-gravatar avatar avatar-48 photo" height="40" width="40"  />
			<?php printf(__( '<span>%1$s: </span> %2$s', 'gdstheme' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'gdstheme' ),'  ','') ) ?>
			<time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__( 'F jS, Y', 'gdstheme' )); ?> </a></time>
			<?php comment_text() ?>
		</div>
		<?php if ($comment->comment_approved == '0') : ?>
			<?php gdstheme_error( 'Your comment is awaiting moderation.', 'gdstheme' ) ?>
		<?php endif; ?>
	</div>
<?php
}


function gdstheme_error($message){
  ?>
  <div class="govuk-warning-text">
    <span class="govuk-warning-text__icon" aria-hidden="true">!</span>
    <strong class="govuk-warning-text__text">
      <span class="govuk-visually-hidden">Warning</span>
      <?php _e($message, 'gdstheme') ?>
    </strong>
  </div>
  <?php
}

function gdstheme_body_class($option){
	switch($option){
		case 'string':
			$body_class_string = '';
			foreach(get_body_class() as $class){
				$body_class_string .= $class . ' ';
			}
			//return substr($body_class_string, -1, 1); //i'm stupid and cant be bothered figuring out why this broken 
			return $body_class_string;
			break;	
		default:
			return get_body_class();
			break;
	}
}




?>
