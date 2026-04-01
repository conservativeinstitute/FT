<?php
/**
 * Read Next popup partial — shown on single posts
 *
 * @package FederalistTimes
 */

if ( ! is_single() ) return;

// Get 3 related posts for the popup
$related = new WP_Query( array(
	'posts_per_page' => 3,
	'post__not_in'   => array( get_the_ID() ),
	'category__in'   => wp_list_pluck( get_the_category(), 'term_id' ),
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
) );

if ( ! $related->have_posts() ) return;

$posts_arr = array();
while ( $related->have_posts() ) {
	$related->the_post();
	$posts_arr[] = array(
		'title'     => get_the_title(),
		'url'       => get_permalink(),
		'excerpt'   => get_the_excerpt(),
		'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'card-thumb' ) : '',
	);
}
wp_reset_postdata();

if ( empty( $posts_arr ) ) return;

$primary = $posts_arr[0];
$secondary = array_slice( $posts_arr, 1 );
?>

<div class="rn-overlay" id="rnOverlay">
	<div class="rn-overlay__bg"></div>
	<div class="rn-modal" role="dialog" aria-modal="true" aria-label="Recommended articles">
		<div class="rn-modal__accent"></div>
		<button class="rn-modal__close" aria-label="Close recommendations">&times;</button>
		<div class="rn-modal__inner">

			<p class="rn-label">Before You Go</p>

			<div class="rn-primary">
				<?php if ( $primary['thumbnail'] ) : ?>
					<div class="rn-primary__img">
						<img src="<?php echo esc_url( $primary['thumbnail'] ); ?>" alt="<?php echo esc_attr( $primary['title'] ); ?>" width="680" height="383" loading="lazy">
					</div>
				<?php endif; ?>
				<a class="rn-primary__title" href="<?php echo esc_url( $primary['url'] ); ?>"><?php echo esc_html( $primary['title'] ); ?></a>
				<p class="rn-primary__teaser"><?php echo esc_html( $primary['excerpt'] ); ?></p>
				<a class="rn-cta" href="<?php echo esc_url( $primary['url'] ); ?>">Continue Reading</a>
			</div>

			<?php if ( $secondary ) : ?>
			<div class="rn-divider"></div>
			<p class="rn-label">Don't Miss These Stories</p>
			<div class="rn-secondary__list">
				<?php foreach ( $secondary as $story ) : ?>
				<a class="rn-story" href="<?php echo esc_url( $story['url'] ); ?>">
					<?php if ( $story['thumbnail'] ) : ?>
					<div class="rn-story__thumb">
						<img src="<?php echo esc_url( $story['thumbnail'] ); ?>" alt="<?php echo esc_attr( $story['title'] ); ?>" width="82" height="58" loading="lazy">
					</div>
					<?php endif; ?>
					<div class="rn-story__body">
						<p class="rn-story__title"><?php echo esc_html( $story['title'] ); ?></p>
						<span class="rn-story__link">Read Story &rarr;</span>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="rn-divider"></div>

			<div id="rnOptinForm">
				<p class="rn-optin__headline">Get the Morning Brief — free every weekday.</p>
				<p class="rn-optin__desc">The news that matters before the spin begins.</p>
				<form class="rn-optin__row ft-email-form" data-source="read-next-popup">
					<label for="rn-email" class="sr-only">Email address</label>
					<input type="email" class="rn-optin__input" id="rn-email" placeholder="Your email address" required>
					<button type="submit" class="rn-optin__btn">Subscribe</button>
				</form>
				<p style="font-size:.65rem;color:var(--ink-3);margin-top:.4rem;">By subscribing, you agree to receive emails from our brand family. <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:var(--gold);text-decoration:underline;">Privacy Policy</a>.</p>
			</div>

		</div>
	</div>
</div>
