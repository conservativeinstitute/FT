<?php
/**
 * Template Name: Thank You for Subscribing
 *
 * @package FederalistTimes
 */

get_header();
?>

<div class="inner-page">
	<div class="inner-page__hero">
		<p class="sect-label">Welcome Aboard</p>
		<h1>Thank You for Subscribing</h1>
		<p class="lead-p">You're now part of a community that values truth, accountability, and independent journalism.</p>
	</div>

	<p>Your subscription to The Federalist Times is confirmed. You'll receive our daily newsletter — the news that matters, before the spin begins.</p>

	<div style="margin:2.5rem 0;padding:1.8rem;background:var(--bg-warm);border-left:3px solid var(--gold);">
		<p style="font-family:'Lato',sans-serif;font-size:.82rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.5rem;">What to expect</p>
		<ul style="margin:0;padding-left:1.2rem;">
			<li style="margin-bottom:.5rem;">Breaking news and analysis delivered to your inbox</li>
			<li style="margin-bottom:.5rem;">In-depth reporting on politics, economics, and national security</li>
			<li style="margin-bottom:.5rem;">Conservative commentary you won't find in the mainstream press</li>
			<li>No spam — just the stories that matter</li>
		</ul>
	</div>

	<p>While you're here, explore some of our latest reporting:</p>

	<div style="margin:1.5rem 0 2.5rem;">
		<?php
		$recent = new WP_Query( array(
			'posts_per_page' => 3,
			'post_status'    => 'publish',
		) );
		if ( $recent->have_posts() ) :
			while ( $recent->have_posts() ) :
				$recent->the_post();
				?>
				<div style="margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--rule-light);">
					<a href="<?php the_permalink(); ?>" style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--ink);text-decoration:none;"><?php the_title(); ?></a>
					<p style="font-family:'Lato',sans-serif;font-size:.78rem;color:var(--ink-2);margin-top:.3rem;"><?php echo esc_html( get_the_excerpt() ); ?></p>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		endif;
		?>
	</div>

	<p>If you ever want to manage your subscription, look for the "Manage Preferences" link at the bottom of any email, or visit our <a href="<?php echo esc_url( home_url( '/unsubscribe/' ) ); ?>" style="color:var(--gold);text-decoration:underline;">unsubscribe page</a>.</p>
</div>

<?php get_footer(); ?>
