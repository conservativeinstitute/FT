<?php
/**
 * 404 page template
 *
 * @package FederalistTimes
 */

get_header();
?>

<div class="inner-page" style="text-align:center;">
	<div class="inner-page__hero" style="border-bottom:none;padding-bottom:0;">
		<p class="sect-label">Error 404</p>
		<h1>Page Not Found</h1>
		<p class="lead-p">The page you're looking for doesn't exist, may have been moved, or is temporarily unavailable.</p>
	</div>

	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="max-width:500px;margin:2rem auto;display:flex;">
		<label for="search-404" class="sr-only">Search</label>
		<input type="search" id="search-404" name="s" placeholder="Search articles..." style="flex:1;font-family:'Libre Baskerville',serif;font-size:.9rem;padding:.7rem .9rem;border:1px solid var(--rule);border-right:none;outline:none;">
		<button type="submit" style="font-family:'Lato',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.7rem 1.5rem;background:var(--ink);color:var(--bg-content);border:1px solid var(--ink);cursor:pointer;">Search</button>
	</form>

	<div style="margin:2rem auto;max-width:400px;text-align:left;">
		<h3 style="font-family:'Lato',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ink-3);margin-bottom:1rem;">Quick Links</h3>
		<ul style="list-style:none;">
			<li style="margin-bottom:.6rem;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:var(--red);text-decoration:underline;text-underline-offset:2px;">Homepage</a></li>
			<li style="margin-bottom:.6rem;"><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" style="color:var(--red);text-decoration:underline;text-underline-offset:2px;">About Us</a></li>
			<li style="margin-bottom:.6rem;"><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--red);text-decoration:underline;text-underline-offset:2px;">Contact</a></li>
		</ul>
	</div>

	<?php
	$recent = new WP_Query( array( 'posts_per_page' => 5, 'post_status' => 'publish' ) );
	if ( $recent->have_posts() ) :
	?>
	<div style="text-align:left;max-width:700px;margin:2rem auto;">
		<h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:1rem;">Recent Articles</h3>
		<div class="archive__list">
			<?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
				<?php get_template_part( 'template-parts/content-archive-item' ); ?>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
