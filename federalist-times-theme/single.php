<?php
/**
 * Single post template
 *
 * @package FederalistTimes
 */

get_header();

while ( have_posts() ) : the_post();
	$post_url   = urlencode( get_permalink() );
	$post_title = urlencode( get_the_title() );
?>

<div class="article-wrap">
	<?php ft_breadcrumbs(); ?>

	<h1><?php the_title(); ?></h1>

	<div class="art-meta">
		<span>By <strong><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></strong></span>
		<span><?php echo esc_html( get_the_date() ); ?></span>
		<span><?php echo esc_html( ft_reading_time() ); ?></span>
		<?php
		$cats = get_the_category();
		if ( $cats ) :
		?>
			<span><a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a></span>
		<?php endif; ?>
	</div>

	<!-- Share buttons — top -->
	<?php get_template_part( 'template-parts/share-buttons', null, array( 'position' => 'top' ) ); ?>

	<div class="art-body">
		<?php
		// Split content to insert ad slot after 3rd paragraph
		$content    = apply_filters( 'the_content', get_the_content() );
		$paragraphs = preg_split( '/(<\/p>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
		$p_count    = 0;
		$ad_inserted = false;

		for ( $i = 0; $i < count( $paragraphs ); $i++ ) {
			echo $paragraphs[ $i ];
			if ( $paragraphs[ $i ] === '</p>' ) {
				$p_count++;
				if ( $p_count === 3 && ! $ad_inserted ) {
					echo '<div class="ad-slot ad-slot-in-article" data-ad-slot="in-article" aria-hidden="true"></div>';
					$ad_inserted = true;
				}
			}
		}
		?>
	</div>

	<!-- In-article email capture -->
	<div class="art-email">
		<h4>Following this story?</h4>
		<p>Get our daily briefing — every weekday. Free.</p>
		<form class="art-email__row ft-email-form" data-source="in-article">
			<label for="art-email-input" class="sr-only">Email address</label>
			<input type="email" class="art-email__input" id="art-email-input" placeholder="Your email address" required>
			<button type="submit" class="art-email__submit">Subscribe</button>
		</form>
		<p style="font-size:.7rem;color:var(--ink-3);margin-top:.5rem;">By subscribing, you agree to receive emails from our brand family. See <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:var(--gold);text-decoration:underline;">Privacy Policy</a>.</p>
	</div>

	<!-- Ad Inserter Pro — Block 16 -->
	<?php echo do_shortcode( '[adinserter block="16"]' ); ?>

	<!-- Share buttons — bottom -->
	<?php get_template_part( 'template-parts/share-buttons', null, array( 'position' => 'bottom' ) ); ?>

	<!-- Tags -->
	<?php
	$tags = get_the_tags();
	if ( $tags ) :
	?>
	<div style="margin-top:2rem;">
		<?php foreach ( $tags as $tag ) : ?>
			<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="archive__sub-tag"><?php echo esc_html( $tag->name ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<!-- Author bio -->
	<div style="margin-top:2.5rem;padding-top:2rem;border-top:2px solid var(--ink);">
		<div style="display:flex;gap:1.2rem;align-items:flex-start;">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 80, '', '', array( 'class' => 'author-bio__photo', 'loading' => 'lazy' ) ); ?>
			<div>
				<p class="masthead-card__name"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></p>
				<p class="masthead-card__title"><?php echo esc_html( get_the_author_meta( 'description' ) ? '' : 'Staff Writer' ); ?></p>
				<p class="masthead-card__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
			</div>
		</div>
	</div>

	<!-- Related articles -->
	<?php
	$related_query = new WP_Query( array(
		'posts_per_page' => 6,
		'post__not_in'   => array( get_the_ID() ),
		'category__in'   => wp_list_pluck( get_the_category(), 'term_id' ),
		'post_status'    => 'publish',
	) );

	if ( $related_query->have_posts() ) :
	?>
	<div class="art-related" style="margin-top:2.5rem;">
		<p class="art-related__title">Read Next</p>
		<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<?php endif; ?>

</div>

<!-- Read Next Popup -->
<?php get_template_part( 'template-parts/read-next-popup' ); ?>

<?php endwhile; ?>

<?php get_footer(); ?>
