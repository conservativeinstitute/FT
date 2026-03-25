<?php
/**
 * Article index / blog template
 *
 * @package FederalistTimes
 */

get_header();
?>

<div class="archive-page">
	<div class="archive__hero">
		<p class="archive__hero-label">All Articles</p>
		<h1>Latest News &amp; Analysis</h1>
		<p>Coverage of politics, economics, national security, crime, health, and culture from The Federalist Times newsroom.</p>
		<?php
		$categories = get_categories( array( 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 10 ) );
		if ( $categories ) :
		?>
		<div class="archive__subs">
			<?php foreach ( $categories as $cat ) : ?>
				<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="archive__sub-tag"><?php echo esc_html( $cat->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

	<div class="archive__list">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template-parts/content-archive-item' ); ?>
		<?php endwhile; else : ?>
			<div style="padding:3rem var(--px);text-align:center;">
				<p>No articles found.</p>
			</div>
		<?php endif; ?>
	</div>

	<?php the_posts_pagination( array(
		'mid_size'  => 2,
		'prev_text' => '&laquo; Previous',
		'next_text' => 'Next &raquo;',
		'class'     => 'pagination',
	) ); ?>
</div>

<?php get_footer(); ?>
