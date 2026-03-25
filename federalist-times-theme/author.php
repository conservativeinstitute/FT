<?php
/**
 * Author archive template
 *
 * @package FederalistTimes
 */

get_header();

$author      = get_queried_object();
$author_id   = $author->ID;
$post_count  = count_user_posts( $author_id );
$description = get_the_author_meta( 'description', $author_id );
?>

<div class="inner-page">
	<div class="inner-page__hero author-hero" style="display:flex;gap:2rem;align-items:flex-start;">
		<div style="width:160px;flex-shrink:0;">
			<?php echo get_avatar( $author_id, 300, '', '', array( 'class' => 'masthead-card__headshot', 'style' => 'border-radius:0;', 'loading' => 'lazy', 'width' => 300, 'height' => 300 ) ); ?>
		</div>
		<div>
			<p class="sect-label">Staff Writer</p>
			<h1 style="font-size:2.4rem;"><?php echo esc_html( $author->display_name ); ?></h1>
			<?php if ( $description ) : ?>
				<p class="lead-p"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
			<p style="font-family:'Lato',sans-serif;font-size:.75rem;color:var(--ink-3);margin-top:.5rem;"><?php echo esc_html( $post_count ); ?> article<?php echo $post_count !== 1 ? 's' : ''; ?> published</p>
		</div>
	</div>

	<h2>Articles by <?php echo esc_html( $author->display_name ); ?></h2>

	<div class="archive__list">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template-parts/content-archive-item' ); ?>
		<?php endwhile; else : ?>
			<p>No articles yet.</p>
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
