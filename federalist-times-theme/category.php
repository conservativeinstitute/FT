<?php
/**
 * Category archive template
 *
 * @package FederalistTimes
 */

get_header();

$cat = get_queried_object();
?>

<div class="archive-page">
	<div class="archive__hero">
		<p class="archive__hero-label">Section</p>
		<h1><?php single_cat_title(); ?></h1>
		<?php if ( category_description() ) : ?>
			<p><?php echo wp_kses_post( category_description() ); ?></p>
		<?php endif; ?>

		<?php
		// Show child categories as sub-tags
		$children = get_categories( array( 'parent' => $cat->term_id, 'hide_empty' => false ) );
		if ( $children ) :
		?>
		<div class="archive__subs">
			<?php foreach ( $children as $child ) : ?>
				<a href="<?php echo esc_url( get_category_link( $child->term_id ) ); ?>" class="archive__sub-tag"><?php echo esc_html( $child->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

	<div class="archive__list">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template-parts/content-archive-item' ); ?>
		<?php endwhile; else : ?>
			<div style="padding:3rem var(--px);text-align:center;">
				<p>No articles found in this section.</p>
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
