<?php
/**
 * Archive item partial
 *
 * @package FederalistTimes
 */
?>
<article class="archive__item">
	<div class="archive__item-body">
		<?php $cats = get_the_category(); if ( $cats ) : ?>
			<p class="archive__item-cat"><?php echo esc_html( $cats[0]->name ); ?></p>
		<?php endif; ?>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="archive__item-deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<p class="byline">By <strong><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></strong> &middot; <?php echo esc_html( ft_reading_time() ); ?> &middot; <?php echo esc_html( get_the_date() ); ?></p>
	</div>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="archive__item-img">
			<?php the_post_thumbnail( 'card-thumb', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
		</div>
	<?php endif; ?>
</article>
