<?php
/**
 * SEO Schema — Article and FAQPage JSON-LD
 *
 * @package FederalistTimes
 */

/**
 * Article schema on single posts
 */
add_action( 'wp_head', function () {
	if ( ! is_single() ) {
		return;
	}

	$post      = get_queried_object();
	$author    = get_the_author_meta( 'display_name', $post->post_author );
	$author_url = get_author_posts_url( $post->post_author );
	$image     = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'hero-thumb' ) : '';

	$logo_url = '';
	if ( has_custom_logo() ) {
		$logo_id  = get_theme_mod( 'custom_logo' );
		$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
	}

	// Use NewsArticle for categories that are news-oriented, Article for evergreen
	$news_cats  = array( 'politics', 'geopolitics', 'crime', 'economics', 'health', 'tech' );
	$categories = get_the_category( $post->ID );
	$is_news    = false;
	foreach ( $categories as $cat ) {
		if ( in_array( $cat->slug, $news_cats, true ) ) {
			$is_news = true;
			break;
		}
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => $is_news ? 'NewsArticle' : 'Article',
		'headline'        => get_the_title( $post ),
		'datePublished'   => get_the_date( 'c', $post ),
		'dateModified'    => get_the_modified_date( 'c', $post ),
		'author'          => array(
			'@type' => 'Person',
			'name'  => $author,
			'url'   => $author_url,
		),
		'publisher'       => array(
			'@type' => 'Organization',
			'name'  => 'The Federalist Times',
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => $logo_url ?: 'https://federalisttimes.com/wp-content/themes/federalist-times-theme/img/logo.png',
			),
		),
		'mainEntityOfPage' => get_permalink( $post ),
	);

	if ( $image ) {
		$schema['image'] = $image;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}, 6 );

/**
 * BreadcrumbList schema (if Yoast is not active)
 */
add_action( 'wp_head', function () {
	if ( defined( 'WPSEO_VERSION' ) || is_front_page() ) {
		return;
	}

	$items = array();
	$pos   = 1;

	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $pos++,
		'name'     => 'Home',
		'item'     => home_url( '/' ),
	);

	if ( is_single() ) {
		$cats = get_the_category();
		if ( $cats ) {
			$cat     = $cats[0];
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => $cat->name,
				'item'     => get_category_link( $cat->term_id ),
			);
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => get_the_title(),
		);
	} elseif ( is_category() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => single_cat_title( '', false ),
		);
	} elseif ( is_page() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => get_the_title(),
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}, 6 );
