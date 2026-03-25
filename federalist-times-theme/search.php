<?php
/**
 * Search results template
 *
 * @package FederalistTimes
 */

get_header();
?>

<div class="archive-page">
	<div class="archive__hero">
		<p class="archive__hero-label">Search Results</p>
		<h1>
			<?php
			/* translators: %s: search query */
			printf( esc_html__( 'Results for &ldquo;%s&rdquo;', 'federalist-times' ), get_search_query() );
			?>
		</h1>
		<p><?php echo esc_html( $wp_query->found_posts ); ?> article<?php echo $wp_query->found_posts !== 1 ? 's' : ''; ?> found</p>
	</div>

	<?php if ( have_posts() ) : ?>
		<div class="archive__list">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content-archive-item' ); ?>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination( array(
			'mid_size'  => 2,
			'prev_text' => '&laquo; Previous',
			'next_text' => 'Next &raquo;',
			'class'     => 'pagination',
		) ); ?>
	<?php else : ?>
		<div class="inner-page" style="text-align:center;">
			<h2>No results found</h2>
			<p>Try a different search term or browse our latest articles.</p>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="max-width:500px;margin:2rem auto;display:flex;">
				<label for="search-input" class="sr-only">Search</label>
				<input type="search" id="search-input" name="s" placeholder="Search articles..." value="<?php echo esc_attr( get_search_query() ); ?>" style="flex:1;font-family:'Libre Baskerville',serif;font-size:.9rem;padding:.7rem .9rem;border:1px solid var(--rule);border-right:none;outline:none;">
				<button type="submit" style="font-family:'Lato',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.7rem 1.5rem;background:var(--ink);color:var(--bg-content);border:1px solid var(--ink);cursor:pointer;">Search</button>
			</form>

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
	<?php endif; ?>
</div>

<?php get_footer(); ?>
