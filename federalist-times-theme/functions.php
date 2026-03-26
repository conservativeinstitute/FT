<?php
/**
 * Federalist Times Theme Functions
 *
 * @package FederalistTimes
 */

define( 'FT_VERSION', '1.0.0' );
define( 'FT_DIR', get_template_directory() );
define( 'FT_URI', get_template_directory_uri() );

/**
 * Theme setup
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'comment-form', 'comment-list', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	add_image_size( 'card-thumb', 600, 340, true );
	add_image_size( 'hero-thumb', 1200, 630, true );

	register_nav_menus( array(
		'primary'        => 'Primary Navigation',
		'footer-pages'   => 'Footer Pages',
		'footer-policies' => 'Footer Policies',
	) );
} );

/**
 * Enqueue styles and scripts
 */
add_action( 'wp_enqueue_scripts', function () {
	// Google Fonts — only weights actually used
	wp_enqueue_style(
		'ft-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;0,900;1,400&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700;900&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'ft-style',
		get_stylesheet_uri(),
		array( 'ft-fonts' ),
		filemtime( FT_DIR . '/style.css' )
	);

	wp_enqueue_script(
		'ft-main',
		FT_URI . '/js/main.js',
		array(),
		filemtime( FT_DIR . '/js/main.js' ),
		true
	);

	wp_enqueue_script(
		'ft-email-capture',
		FT_URI . '/js/email-capture.js',
		array(),
		filemtime( FT_DIR . '/js/email-capture.js' ),
		true
	);

	wp_localize_script( 'ft-email-capture', 'ftAjax', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'ft_capture_email' ),
	) );
} );

/**
 * Preconnect hints
 */
add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

/**
 * Custom excerpt
 */
add_filter( 'excerpt_length', function () {
	return 25;
} );
add_filter( 'excerpt_more', function () {
	return '&hellip;';
} );

/**
 * Calculate reading time
 */
function ft_reading_time( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$content  = get_post_field( 'post_content', $post_id );
	$words    = str_word_count( wp_strip_all_tags( $content ) );
	$minutes  = max( 1, ceil( $words / 250 ) );
	return $minutes . ' min read';
}

/**
 * Breadcrumbs
 */
function ft_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	echo '<nav class="art-breadcrumb" aria-label="Breadcrumb">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';
	echo '<span class="sep" aria-hidden="true">&rsaquo;</span>';

	if ( is_single() ) {
		$cats = get_the_category();
		if ( $cats ) {
			$cat = $cats[0];
			echo '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
			echo '<span class="sep" aria-hidden="true">&rsaquo;</span>';
		}
		echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_category() ) {
		echo '<span class="current">' . esc_html( single_cat_title( '', false ) ) . '</span>';
	} elseif ( is_author() ) {
		echo '<span class="current">' . esc_html( get_the_author() ) . '</span>';
	} elseif ( is_search() ) {
		echo '<span class="current">Search Results</span>';
	} elseif ( is_page() ) {
		echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
	}

	echo '</nav>';
}

/**
 * Organization JSON-LD (every page)
 */
add_action( 'wp_head', function () {
	$logo_url = '';
	if ( has_custom_logo() ) {
		$logo_id  = get_theme_mod( 'custom_logo' );
		$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
	}
	$schema = array(
		'@context'     => 'https://schema.org',
		'@type'        => array( 'Organization', 'NewsMediaOrganization' ),
		'name'         => 'The Federalist Times',
		'alternateName' => 'Federalist Times',
		'url'          => 'https://federalisttimes.com',
		'logo'         => $logo_url ?: 'https://federalisttimes.com/wp-content/themes/federalist-times-theme/img/logo.png',
		'description'  => 'Independent, subscriber-supported journalism covering politics, economics, national security, crime, and health.',
		'foundingDate' => '2026',
		'sameAs'       => array(
			'https://x.com/federalisttimes',
			'https://truthsocial.com/@federalisttimes',
			'https://www.youtube.com/@federalisttimes',
			'https://rumble.com/federalisttimes',
		),
		'contactPoint' => array(
			'@type'       => 'ContactPoint',
			'email'       => 'contact@federalisttimes.com',
			'contactType' => 'customer service',
		),
		'address'      => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => '1328 Highpoint Way',
			'addressLocality' => 'Roanoke',
			'addressRegion'   => 'TX',
			'postalCode'      => '76262',
			'addressCountry'  => 'US',
		),
		'publishingPrinciples' => 'https://federalisttimes.com/editorial-policy/',
		'correctionsPolicy'    => 'https://federalisttimes.com/corrections/',
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}, 5 );

/**
 * Open Graph meta tags
 */
add_action( 'wp_head', function () {
	// Skip if Yoast or similar handles OG
	if ( defined( 'WPSEO_VERSION' ) ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = get_bloginfo( 'description' );
	$url         = is_singular() ? get_permalink() : home_url( '/' );
	$image       = '';
	$type        = 'website';

	if ( is_singular() ) {
		$type = 'article';
		$post = get_queried_object();
		if ( has_excerpt( $post ) ) {
			$description = wp_strip_all_tags( get_the_excerpt( $post ) );
		} else {
			$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 );
		}
		if ( has_post_thumbnail( $post ) ) {
			$image = get_the_post_thumbnail_url( $post, 'hero-thumb' );
		}
	}

	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="The Federalist Times">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}, 5 );

/**
 * Include modular files
 */
require_once FT_DIR . '/inc/email-capture.php';
require_once FT_DIR . '/inc/seo-schema.php';
require_once FT_DIR . '/inc/sample-content.php';

/**
 * Fallback navigation when no menu is assigned
 */
function ft_fallback_nav() {
	echo '<ul>';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">About</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/blog/' ) ) . '">Blog</a></li>';
	echo '</ul>';
}
