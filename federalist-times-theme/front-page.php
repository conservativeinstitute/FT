<?php
/**
 * Homepage template
 *
 * @package FederalistTimes
 */

get_header();

// Get the latest post for the lead story
$lead_query = new WP_Query( array(
	'posts_per_page' => 1,
	'post_status'    => 'publish',
) );

// Sidebar stories: next 4 posts
$sidebar_query = new WP_Query( array(
	'posts_per_page' => 4,
	'offset'         => 1,
	'post_status'    => 'publish',
) );

// Define 3 river categories — use the first 3 that exist
$river_cats = array();
$all_cats   = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 3, 'hide_empty' => true ) );
foreach ( $all_cats as $cat ) {
	$river_cats[] = $cat;
}
?>

<!-- HERO ZONE -->
<div class="hero-zone">
	<div class="hero-left">
		<?php if ( $lead_query->have_posts() ) : $lead_query->the_post(); ?>
			<article class="lead">
				<a href="<?php the_permalink(); ?>">
					<?php
					$lead_cats = get_the_category();
					if ( $lead_cats ) :
					?>
						<p class="lead__cat"><?php echo esc_html( $lead_cats[0]->name ); ?></p>
					<?php endif; ?>
					<h2><?php the_title(); ?></h2>
					<p class="lead__deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<p class="byline">By <strong><?php the_author(); ?></strong> &middot; <?php echo esc_html( ft_reading_time() ); ?> &middot; <?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> ago</p>
				</a>
			</article>
		<?php wp_reset_postdata(); endif; ?>

		<!-- Email capture — hero placement -->
		<div class="subscribe-box">
			<p class="subscribe-box__label">The Morning Brief</p>
			<h3>The news that matters, before the spin begins.</h3>
			<p class="subscribe-box__desc">Join readers every weekday at 6 AM ET.</p>
			<form class="subscribe-box__row ft-email-form" data-source="hero">
				<label for="hero-email" class="sr-only">Email address</label>
				<input type="email" class="subscribe-box__input" id="hero-email" placeholder="Your email address" required>
				<button type="submit" class="subscribe-box__go">Subscribe</button>
			</form>
			<p class="subscribe-box__fine">Free. No spam. Unsubscribe anytime.</p>
		</div>
	</div>

	<div class="hero-right">
		<?php
		if ( $sidebar_query->have_posts() ) :
			while ( $sidebar_query->have_posts() ) : $sidebar_query->the_post();
		?>
			<article class="sidebar-story">
				<a href="<?php the_permalink(); ?>">
					<?php $s_cats = get_the_category(); if ( $s_cats ) : ?>
						<p class="sidebar-story__cat"><?php echo esc_html( $s_cats[0]->name ); ?></p>
					<?php endif; ?>
					<h3><?php the_title(); ?></h3>
					<p class="byline">By <strong><?php the_author(); ?></strong> &middot; <?php echo esc_html( ft_reading_time() ); ?></p>
				</a>
			</article>
		<?php
			endwhile;
			wp_reset_postdata();
		endif;
		?>
	</div>
</div>

<!-- RIVER — 3-column category sections -->
<?php if ( count( $river_cats ) >= 3 ) : ?>
<div class="river">
	<?php foreach ( $river_cats as $rcat ) :
		$river_posts = new WP_Query( array(
			'posts_per_page' => 2,
			'cat'            => $rcat->term_id,
			'post_status'    => 'publish',
		) );
	?>
	<div class="river__col">
		<div class="river__head">
			<a href="<?php echo esc_url( get_category_link( $rcat->term_id ) ); ?>"><?php echo esc_html( $rcat->name ); ?></a>
		</div>
		<?php while ( $river_posts->have_posts() ) : $river_posts->the_post(); ?>
			<article class="river-item">
				<a href="<?php the_permalink(); ?>">
					<h4><?php the_title(); ?></h4>
					<p class="river-item__deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<p class="byline">By <strong><?php the_author(); ?></strong> &middot; <?php echo esc_html( ft_reading_time() ); ?></p>
				</a>
			</article>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- OPINION SECTION -->
<?php
$opinion_cat = get_category_by_slug( 'opinion' );
if ( $opinion_cat ) :
	$opinion_query = new WP_Query( array(
		'posts_per_page' => 4,
		'cat'            => $opinion_cat->term_id,
		'post_status'    => 'publish',
	) );
	if ( $opinion_query->have_posts() ) :
?>
<div class="opinion">
	<div class="opinion__label">Opinion</div>
	<div class="opinion__items">
		<?php while ( $opinion_query->have_posts() ) : $opinion_query->the_post(); ?>
		<article class="opinion-item">
			<a href="<?php the_permalink(); ?>">
				<p class="opinion-item__author"><?php the_author(); ?></p>
				<h4><?php the_title(); ?></h4>
			</a>
		</article>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
</div>
<?php endif; endif; ?>

<!-- AD SLOT: in-feed -->
<div class="ad-slot ad-slot-in-article" data-ad-slot="homepage-mid" aria-hidden="true"></div>

<!-- AD SLOT: mobile banner -->
<div class="ad-slot ad-slot-mobile-banner" data-ad-slot="mobile-banner" aria-hidden="true"></div>

<?php get_footer(); ?>
