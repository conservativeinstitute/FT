<?php
/**
 * Template Name: Corrections Policy
 *
 * @package FederalistTimes
 */

get_header();
?>

<div class="inner-page">
	<div class="inner-page__hero">
		<p class="sect-label">Policies</p>
		<h1>Corrections and Updates Policy</h1>
		<p class="lead-p">Getting it right matters more than getting it first. When we make errors — and we will, because journalism is a human enterprise — we fix them promptly, transparently, and without equivocation.</p>
	</div>

	<h2>How to Report an Error</h2>
	<p>If you believe we have published an error, please contact us at <a href="mailto:corrections@federalisttimes.com" style="color:var(--gold);">corrections@federalisttimes.com</a> or use our <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--gold);text-decoration:underline;text-underline-offset:2px;">contact form</a>. Please include:</p>
	<ul style="margin:1rem 0 1.5rem 1.5rem;color:var(--ink-2);">
		<li>The article URL</li>
		<li>The specific claim you believe is inaccurate</li>
		<li>Any supporting evidence or sources</li>
	</ul>

	<h2>What We Review</h2>
	<p>We review all correction requests related to factual claims, attributions, dates, statistics, names, titles, and any other verifiable information. We do not issue corrections for differences of opinion or interpretation.</p>

	<h2>Our Correction Process</h2>
	<p>When a factual error is identified, we correct it as quickly as possible — typically within 24 hours. Our editors verify the error, determine the appropriate remedy, and apply the correction.</p>

	<h2>Labeling System</h2>
	<p><strong>Correction:</strong> A factual error has been identified and fixed. The original incorrect information is noted.</p>
	<p><strong>Update:</strong> New information has become available since publication that materially changes the story.</p>
	<p><strong>Clarification:</strong> The article was technically accurate but could reasonably be misunderstood. Additional context has been added.</p>

	<h2>Transparency</h2>
	<p>All corrections are noted at the bottom of the affected article with the date the correction was made and a clear description of what was changed and why. We do not silently edit articles after publication.</p>

	<h2>Retractions</h2>
	<p>In the rare event that a story is found to be fundamentally flawed — based on fabricated sources, materially inaccurate in its central claims, or otherwise unsalvageable through correction — we will retract the article. Retracted articles are removed from circulation and replaced with a retraction notice explaining what happened and why.</p>

	<h2>Response Time</h2>
	<p>We review every correction submission and respond within 48 hours.</p>
</div>

<?php get_footer(); ?>
