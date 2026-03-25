<?php
/**
 * Share buttons partial
 *
 * @package FederalistTimes
 */

$position   = $args['position'] ?? 'top';
$post_url   = urlencode( get_permalink() );
$post_title = urlencode( get_the_title() );
$fb_url     = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;
$x_url      = 'https://x.com/intent/tweet?url=' . $post_url . '&text=' . $post_title;
$truth_url  = 'https://truthsocial.com/share?url=' . $post_url;

if ( $position === 'top' ) :
?>
<div class="ft-share-strip-top">
	<p class="ft-share-heading">Share this article</p>
	<div class="ft-share-buttons">
		<a class="ft-share facebook" href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook"><span class="ft-share-icon" aria-hidden="true">f</span><span class="ft-share-label">Facebook</span></a>
		<a class="ft-share x" href="<?php echo esc_url( $x_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on X"><span class="ft-share-icon" aria-hidden="true">X</span><span class="ft-share-label">Post</span></a>
		<a class="ft-share truth" href="<?php echo esc_url( $truth_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Truth Social"><span class="ft-share-icon" aria-hidden="true">&#9733;</span><span class="ft-share-label">Truth</span></a>
	</div>
</div>
<?php else : ?>
<div class="ft-share-strip-bottom">
	<p class="ft-share-heading">Help expose this story</p>
	<p class="ft-share-subheading">Share it with others.</p>
	<div class="ft-share-buttons">
		<a class="ft-share facebook" href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook"><span class="ft-share-icon" aria-hidden="true">f</span><span class="ft-share-label">Facebook</span></a>
		<a class="ft-share x" href="<?php echo esc_url( $x_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on X"><span class="ft-share-icon" aria-hidden="true">X</span><span class="ft-share-label">Post</span></a>
		<a class="ft-share truth" href="<?php echo esc_url( $truth_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Truth Social"><span class="ft-share-icon" aria-hidden="true">&#9733;</span><span class="ft-share-label">Truth</span></a>
	</div>
</div>
<?php endif; ?>
