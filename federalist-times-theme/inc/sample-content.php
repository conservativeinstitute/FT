<?php
/**
 * Sample Content Importer
 *
 * Run via: WP Admin > Tools > Import Sample Content
 * Or via WP-CLI: wp eval "require get_template_directory().'/inc/sample-content.php'; ft_import_sample_content();"
 *
 * @package FederalistTimes
 */

// Admin menu item
add_action( 'admin_menu', function () {
	add_management_page(
		'Import Sample Content',
		'Import Sample Content',
		'manage_options',
		'ft-sample-content',
		'ft_sample_content_page'
	);
} );

function ft_sample_content_page() {
	if ( isset( $_POST['ft_import'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'ft_import_sample' ) ) {
		ft_import_sample_content();
		echo '<div class="notice notice-success"><p>Sample content imported successfully.</p></div>';
	}
	?>
	<div class="wrap">
		<h1>Import Sample Content</h1>
		<p>This will create categories, sample posts, and pages for The Federalist Times theme.</p>
		<form method="post">
			<?php wp_nonce_field( 'ft_import_sample' ); ?>
			<input type="hidden" name="ft_import" value="1">
			<?php submit_button( 'Import Sample Content' ); ?>
		</form>
	</div>
	<?php
}

function ft_import_sample_content() {
	// Create categories
	$categories = array(
		'geopolitics' => array( 'name' => 'Geopolitics', 'desc' => 'Coverage of great-power competition, alliance structures, and the foreign policy decisions that shape the international order.' ),
		'politics'    => array( 'name' => 'Politics', 'desc' => 'Reporting from Capitol Hill, the White House, state legislatures, and the campaign trail.' ),
		'economics'   => array( 'name' => 'Economics', 'desc' => 'Federal Reserve policy, markets, trade, personal finance, and the forces that determine economic outcomes.' ),
		'crime'       => array( 'name' => 'Crime', 'desc' => 'Reporting on crime trends, law enforcement, prosecution policy, and the justice system.' ),
		'health'      => array( 'name' => 'Health', 'desc' => 'Evidence-based reporting on medicine, nutrition, mental health, and public health policy.' ),
		'opinion'     => array( 'name' => 'Opinion', 'desc' => 'The views expressed here belong to the individual authors and do not represent the editorial position of The Federalist Times.' ),
	);

	$cat_ids = array();
	foreach ( $categories as $slug => $data ) {
		$existing = get_category_by_slug( $slug );
		if ( $existing ) {
			$cat_ids[ $slug ] = $existing->term_id;
		} else {
			$result = wp_insert_category( array(
				'cat_name'             => $data['name'],
				'category_nicename'    => $slug,
				'category_description' => $data['desc'],
			) );
			if ( ! is_wp_error( $result ) ) {
				$cat_ids[ $slug ] = $result;
			}
		}
	}

	// Create default admin user attribution
	$author_id = get_current_user_id() ?: 1;

	// Sample posts
	$posts = array(
		array(
			'title'    => 'The Largest American Military Build-Up in the Pacific Since 1996 Signals a New Era of Deterrence',
			'slug'     => 'pacific-military-buildup-taiwan-strait-deterrence',
			'cat'      => 'geopolitics',
			'tags'     => array( 'Taiwan', 'China', 'US Navy', 'Indo-Pacific', 'Deterrence' ),
			'excerpt'  => 'The concentration of U.S. naval firepower around the Taiwan Strait now looks less like signalling and more like sequencing.',
			'meta'     => 'Three carrier strike groups now patrol the Western Pacific as Washington shifts from strategic ambiguity to visible deterrence near Taiwan.',
			'days_ago' => 1,
			'content'  => '<p>The USS <em>Ronald Reagan</em> carrier strike group slipped through the Luzon Strait at dawn on a Tuesday in February, joining two additional carrier groups already positioned in the Philippine Sea. No announcement was made. No press release went out. But defense attachés across the Indo-Pacific noticed — and so did Beijing.</p>

<p>What is unfolding across the Western Pacific is the most significant repositioning of American naval assets since the Third Taiwan Strait Crisis in 1996, when President Clinton ordered two carrier groups into the region as a warning shot against Chinese missile tests near Taiwan. This time, the scale is larger, the stakes arguably higher, and the strategic logic more complex.</p>

<h3>Deterrence by Presence</h3>

<p>Pentagon officials have been careful to describe the current posture as "routine freedom of navigation operations" and "scheduled exercises." But four senior defense officials, speaking on background, described a deliberate decision taken at the National Security Council level to demonstrate resolve at a moment when Beijing has grown increasingly assertive over Taiwanese airspace and the South China Sea.</p>

<!-- Featured image needed: Three aircraft carriers in formation in the Philippine Sea with Taiwan visible in the background -->

<blockquote><p>"We are not trying to provoke. We are trying to be unambiguous. There is a difference."</p></blockquote>

<p>The distinction matters. American deterrence strategy in the Pacific has historically rested on ambiguity — keeping adversaries uncertain about the precise threshold that would trigger a military response. But a faction within the current defense establishment argues that ambiguity itself has become a liability, inviting miscalculation from a Chinese leadership that may read restraint as weakness.</p>

<h3>The Semiconductor Factor</h3>

<p>Beneath the military posturing lies a set of economic grievances that have sharpened since the passage of the CHIPS and Science Act and subsequent rounds of export controls restricting China\'s access to advanced semiconductors. Beijing has framed these restrictions as acts of economic warfare; Washington argues they are national security necessities.</p>

<p>Taiwan, which produces the vast majority of the world\'s most advanced chips through TSMC, sits at the intersection of both disputes. An economic motive overlays every military calculation, making the current standoff qualitatively different from previous crises in the strait.</p>

<h3>What Comes Next</h3>

<p>Analysts at the Center for Strategic and International Studies project that the current elevated posture will be maintained through at least mid-summer, encompassing the anniversary of the 1996 crisis and a series of Taiwanese local government elections. Whether Beijing interprets that presence as stabilizing or as a provocation that demands a response remains the central variable in a situation with precious few controllable ones.</p>

<p>For now, the carriers hold their position. The watch rotates. And both sides study each other\'s movements with the intensity that only comes when the consequences of misreading them are incalculable.</p>',
		),
		array(
			'title'    => 'House Passes Sweeping Border Security Bill with Bipartisan Support',
			'slug'     => 'house-border-security-bill-bipartisan-vote',
			'cat'      => 'politics',
			'tags'     => array( 'Congress', 'Border Security', 'Immigration', 'Bipartisan' ),
			'excerpt'  => 'The Secure America\'s Borders Act cleared the lower chamber 268 to 163, drawing 41 Democratic votes.',
			'meta'     => 'House passes $34 billion border security bill with bipartisan support, funding 12,000 new Border Patrol agents and barrier construction.',
			'days_ago' => 2,
			'content'  => '<p>After months of procedural maneuvering and two failed cloture votes in the Senate, the House moved swiftly Wednesday to pass the most comprehensive border security legislation in nearly two decades. The bill authorizes $34 billion over five years for physical infrastructure, personnel, and immigration court expansion.</p>

<p>The winning coalition was assembled, improbably, by two members who rarely share a dais: Rep. Maria Santos (D-TX) and Rep. Greg Forsythe (R-AZ), a third-term conservative who has made border enforcement his signature issue.</p>

<!-- Featured image needed: The U.S. Capitol building with American flag at sunset -->

<blockquote><p>"My constituents don\'t care about the politics. They care about whether their daughters can walk to school safely."</p></blockquote>

<p>The legislation funds an additional 12,000 Border Patrol agents over three years, resumes physical barrier construction in the San Diego, El Paso, and Rio Grande Valley sectors, and dramatically expands the immigration court system — adding 150 new judges to address a backlog that has grown to over 3.2 million cases.</p>

<h3>The Bipartisan Coalition</h3>

<p>The 268-163 vote included 41 Democrats, nearly all representing border districts or swing seats. The defections infuriated progressive caucus leaders, who had demanded a party-line vote against the bill. But members like Santos argued that the electoral math was clear: immigration ranked as the top issue for 67% of voters in her district in the most recent poll.</p>

<h3>What\'s in the Bill</h3>

<p>Beyond physical barriers and personnel, the bill includes $2.1 billion for surveillance technology along the southern border, mandatory E-Verify for employers with more than 25 workers, a provision creating a narrowly defined pathway for long-term DACA recipients who meet stringent requirements, and a complete overhaul of the asylum process designed to reduce the average adjudication timeline from 4.3 years to under 6 months.</p>

<p>The bill now moves to the Senate, where Majority Leader Collins has promised a vote within 30 days. Analysts give it roughly even odds of surviving the filibuster threshold.</p>',
		),
		array(
			'title'    => 'Federal Reserve Holds Rates Steady as Inflation Data Defies Wall Street Forecasts',
			'slug'     => 'federal-reserve-holds-rates-inflation-defies-forecasts',
			'cat'      => 'economics',
			'tags'     => array( 'Federal Reserve', 'Interest Rates', 'Inflation', 'Markets' ),
			'excerpt'  => 'The FOMC\'s unanimous decision to keep the federal funds rate in the 5.25-5.50% range surprised markets expecting a dovish signal.',
			'meta'     => 'Fed holds interest rates steady for the fifth straight meeting as inflation remains above target, dashing hopes for near-term cuts.',
			'days_ago' => 3,
			'content'  => '<p>The Federal Reserve held interest rates steady for the fifth consecutive meeting Wednesday, as Chairman Powell told reporters that the central bank needed "greater confidence" that inflation was returning durably to its 2% target.</p>

<!-- Featured image needed: Federal Reserve building in Washington D.C. -->

<blockquote><p>"We want to do this right. We have come too far to stumble at the finish line."</p></blockquote>

<p>Core Personal Consumption Expenditures — the Fed\'s preferred inflation gauge — came in at 2.8% for January, stubbornly above the target. Services inflation in particular has proved resistant, driven by shelter costs and healthcare expenses that have shown little response to 18 months of restrictive monetary policy.</p>

<h3>Market Reaction</h3>

<p>Markets reacted sharply, with the S&P 500 posting its largest single-day decline in three months — falling 1.4% on the day. The 10-year Treasury yield rose 12 basis points to 4.61%, reflecting a repricing of rate cut expectations. Futures markets now price only one rate cut for the entirety of 2026, a dramatic shift from the three cuts that had been consensus at the start of the year.</p>

<h3>Housing Market Impact</h3>

<p>The decision has immediate implications for the housing market. With mortgage rates expected to remain above 7% through the second quarter, the affordability squeeze that has frozen much of the real estate market shows no sign of easing. Home sales have fallen to their lowest level since 2010, and inventory remains historically tight as homeowners with sub-4% mortgages refuse to sell into a 7% market.</p>

<p>The next FOMC meeting is scheduled for late April. Between now and then, the Fed will receive two more inflation reports, a jobs report, and first-quarter GDP data — all of which will factor into what Powell described as a decision that "will be driven by data, not by the calendar."</p>',
		),
		array(
			'title'    => 'Chicago Carjackings Surge 34% as Progressive DA Faces Recall',
			'slug'     => 'chicago-carjackings-surge-progressive-da-recall',
			'cat'      => 'crime',
			'tags'     => array( 'Chicago', 'Crime', 'Recall', 'Prosecution Policy' ),
			'excerpt'  => 'A coalition of neighborhood groups has collected 183,000 signatures — well above the 120,000 required to trigger a recall.',
			'meta'     => 'Carjackings up 34% in Chicago as recall effort against progressive DA gains steam with 183,000 signatures collected.',
			'days_ago' => 1,
			'content'  => '<p>On a Tuesday night in Pilsen, a 67-year-old retired schoolteacher was pulled from her Hyundai Tucson at a stoplight two blocks from her home. She was not injured, but the car was found stripped in Gary, Indiana, the following morning. It was the third carjacking on her block in eight months. Her alderman, she says, has stopped returning her calls.</p>

<!-- Featured image needed: Chicago skyline at dusk with police lights in foreground -->

<blockquote><p>"These aren\'t statistics. These are my neighbors, my customers, my family. Enough."</p></blockquote>

<p>Carjackings have increased 34% year-over-year, with 1,847 incidents recorded through February — a pace that would make 2026 the worst year for vehicle theft by force in Chicago\'s recorded history. State\'s Attorney Renata Voss, who ran in 2022 on a platform of decarceration and police accountability, has become the focal point of public frustration.</p>

<h3>The Recall Effort</h3>

<p>A coalition of neighborhood organizations spanning the South Side, West Side, and northwest suburbs has collected 183,000 signatures — well above the 120,000 required to trigger a recall. If the recall qualifies for the ballot — a legal challenge from Voss\'s office is pending — it would be only the second recall of a major Illinois official in 25 years.</p>

<h3>The Data</h3>

<p>A University of Chicago Crime Lab poll found that 61% of Cook County residents support removing Voss from office. Support crosses party lines, with 54% of self-identified Democrats favoring removal. The poll also found that carjacking victims were disproportionately concentrated in low-income neighborhoods on the South and West Sides — communities that had overwhelmingly supported Voss in 2022.</p>

<p>The outcome could have significant implications for how other large-city prosecutors calibrate their approach heading into a cycle of local elections across the country.</p>',
		),
		array(
			'title'    => 'DOJ Announces Largest-Ever RICO Indictment Against Transnational Cartel',
			'slug'     => 'doj-rico-indictment-transnational-cartel',
			'cat'      => 'crime',
			'tags'     => array( 'DOJ', 'Cartel', 'RICO', 'Drug Trafficking', 'Law Enforcement' ),
			'excerpt'  => 'The 147-count indictment names 42 defendants across the United States and Mexico in a sweeping fentanyl trafficking case.',
			'meta'     => 'Department of Justice announces the largest RICO indictment in history targeting transnational fentanyl trafficking network.',
			'days_ago' => 2,
			'content'  => '<p>The Department of Justice on Thursday unsealed what officials called the largest RICO indictment in federal history, naming 42 defendants in a sprawling case that traces the flow of fentanyl from chemical precursor suppliers in China through Mexican manufacturing operations and into American distribution networks spanning 14 states.</p>

<!-- Featured image needed: Department of Justice seal with American flag backdrop -->

<p>The 147-count indictment is the culmination of a three-year investigation involving the DEA, FBI, Homeland Security Investigations, and Mexican federal police. Attorney General outlined the scope of the case at a press conference in Washington, describing a network that was responsible for an estimated 23% of all fentanyl seized at the southern border in 2025.</p>

<h3>The Network</h3>

<p>According to the indictment, the network operated a vertically integrated supply chain — sourcing precursor chemicals from three Chinese chemical companies, manufacturing fentanyl in seven laboratories in Sinaloa and Jalisco, transporting finished product across the border through a combination of commercial vehicles and tunnel systems, and distributing through a network of wholesale and retail operations in major American cities.</p>

<h3>Seizures and Arrests</h3>

<p>Coordinated raids conducted simultaneously in 14 states and three Mexican states resulted in the arrest of 38 of the 42 named defendants. Four remain at large, all believed to be in Mexico. Agents seized approximately 940 kilograms of fentanyl — enough for an estimated 470 million lethal doses — $67 million in cash, 143 firearms, and 47 vehicles.</p>

<p>Legal experts say the case represents the most aggressive use of RICO statutes against a foreign drug trafficking organization in American history, potentially setting precedent for how federal prosecutors pursue transnational criminal enterprises.</p>',
		),
		array(
			'title'    => 'New Study Links Ultra-Processed Foods to Accelerated Cognitive Decline',
			'slug'     => 'ultra-processed-foods-cognitive-decline-study',
			'cat'      => 'health',
			'tags'     => array( 'Nutrition', 'Brain Health', 'Research', 'Ultra-Processed Foods' ),
			'excerpt'  => 'A 12-year study of 47,000 adults found those consuming more than 30% of calories from ultra-processed foods experienced faster memory loss.',
			'meta'     => 'Major 12-year study links high ultra-processed food consumption to significantly accelerated cognitive decline beginning in the mid-50s.',
			'days_ago' => 1,
			'content'  => '<p>A landmark 12-year longitudinal study published in the Lancet Neurology has found a significant association between ultra-processed food consumption and accelerated cognitive decline, particularly in memory and executive function, beginning as early as the mid-50s.</p>

<!-- Featured image needed: Scientific laboratory with brain imaging scans on monitors -->

<p>The study tracked 47,000 adults aged 45 to 75 across eight countries, measuring dietary intake through validated food frequency questionnaires and assessing cognitive function through standardized neuropsychological testing every two years.</p>

<h3>Key Findings</h3>

<p>Participants who derived more than 30% of their daily calories from ultra-processed foods — defined using the NOVA classification system — showed a rate of cognitive decline 28% faster than those who consumed less than 15% of calories from such foods. The association remained statistically significant after controlling for age, sex, education, physical activity, smoking, alcohol consumption, BMI, cardiovascular disease, and diabetes.</p>

<p>The effect was most pronounced in memory tasks and processing speed. Executive function — the ability to plan, organize, and switch between tasks — was also significantly affected, though to a lesser degree.</p>

<h3>What Counts as Ultra-Processed</h3>

<p>Ultra-processed foods include soft drinks, packaged snacks, reconstituted meat products, instant noodles, and industrially produced breads and pastries — foods characterized by the presence of additives such as emulsifiers, artificial flavors, and preservatives not typically found in home-cooked meals.</p>

<p>The average American derives approximately 58% of their calories from ultra-processed foods, according to recent data from the National Health and Nutrition Examination Survey — nearly double the threshold at which the study observed accelerated decline.</p>',
		),
		array(
			'title'    => 'Virginia Governor Signs Constitutional Carry Into Law',
			'slug'     => 'virginia-constitutional-carry-signed-into-law',
			'cat'      => 'politics',
			'tags'     => array( 'Second Amendment', 'Virginia', 'Gun Rights', 'State Legislatures' ),
			'excerpt'  => 'Virginia becomes the 30th state to allow permitless concealed carry for legal firearm owners.',
			'meta'     => 'Virginia governor signs constitutional carry bill, making it the 30th state to allow permitless concealed carry for legal gun owners.',
			'days_ago' => 4,
			'content'  => '<p>Virginia Governor signed HB 1247 into law Tuesday morning, making the Commonwealth the 30th state to allow legal firearm owners to carry a concealed weapon without a government-issued permit. The law takes effect July 1.</p>

<!-- Featured image needed: Virginia state capitol building in Richmond -->

<p>The bill passed the state Senate 21-19 along party lines after clearing the House of Delegates by a wider margin of 56-44, with two Democrats joining all Republicans in support. The legislation eliminates the requirement for a concealed handgun permit for any person who is legally eligible to purchase a firearm under state and federal law.</p>

<h3>What the Law Changes</h3>

<p>Under the current system, Virginians seeking to carry a concealed firearm must complete a safety training course, submit an application, pass a background check, and pay a $50 fee. The new law removes those requirements while preserving all existing prohibitions on who may possess a firearm — felons, domestic violence offenders, and those adjudicated mentally incompetent remain barred from carrying.</p>

<h3>The Debate</h3>

<p>Supporters argue the permit system was an unconstitutional burden on a fundamental right, citing the Supreme Court\'s 2022 Bruen decision which held that the Second Amendment protects an individual\'s right to carry a firearm in public. Opponents warned that removing training requirements would lead to an increase in accidental discharges and complicate law enforcement interactions.</p>

<p>Data from other states that have adopted constitutional carry is mixed. A 2024 RAND Corporation review found no statistically significant impact on violent crime rates in states that adopted permitless carry between 2015 and 2023, though the researchers cautioned that the time horizon may be too short for definitive conclusions.</p>',
		),
		array(
			'title'    => 'Pentagon Confirms Third Carrier Group Enters Philippine Sea',
			'slug'     => 'pentagon-third-carrier-group-philippine-sea',
			'cat'      => 'geopolitics',
			'tags'     => array( 'US Navy', 'Indo-Pacific', 'Taiwan', 'China', 'Military' ),
			'excerpt'  => 'The USS Abraham Lincoln strike group joined two other carrier groups already operating east of Taiwan.',
			'meta'     => 'Pentagon confirms three carrier strike groups now deployed in the Philippine Sea, the largest Pacific naval concentration since 1996.',
			'days_ago' => 3,
			'content'  => '<p>The Pentagon confirmed Thursday that the USS Abraham Lincoln carrier strike group has entered the Philippine Sea, joining the USS Ronald Reagan and USS Carl Vinson groups in what defense officials described as the largest concentration of American naval power in the Western Pacific since the 1996 Taiwan Strait Crisis.</p>

<!-- Featured image needed: Aircraft carrier at sea with fighter jets on deck -->

<p>The deployment brings the total number of American warships operating within 500 nautical miles of Taiwan to 27, including three nuclear-powered aircraft carriers, four guided-missile cruisers, twelve destroyers, and various support vessels.</p>

<h3>Official Response</h3>

<p>Defense Department spokesperson Rear Admiral Sarah Chen described the deployment as "consistent with our longstanding commitment to a free and open Indo-Pacific" and declined to characterize it as a response to any specific Chinese military activity. However, the deployment coincides with a series of increasingly aggressive Chinese air force incursions into Taiwan\'s air defense identification zone, which reached a record 42 sorties in a single day last month.</p>

<h3>Beijing\'s Reaction</h3>

<p>China\'s Ministry of National Defense issued a statement calling the deployment "a dangerous provocation that undermines peace and stability in the Taiwan Strait" and warned that "the Chinese military will take all necessary measures to defend national sovereignty and territorial integrity." The foreign ministry separately summoned the American ambassador for a formal protest.</p>

<p>Regional allies have reacted with a mixture of reassurance and anxiety. Japan\'s defense minister expressed "appreciation for the stabilizing presence of American forces" while calling for "continued dialogue to prevent miscalculation." Australia\'s prime minister was more cautious, urging "restraint from all parties."</p>',
		),
		array(
			'title'    => 'The West Cannot Survive on Sentiment Alone',
			'slug'     => 'west-cannot-survive-sentiment-alone-opinion',
			'cat'      => 'opinion',
			'tags'     => array( 'Western Civilization', 'Defense', 'Culture', 'Opinion' ),
			'excerpt'  => 'Democracies that mistake their own moral superiority for a strategic advantage have a habit of losing wars they thought they could not lose.',
			'meta'     => 'Victor Davis Hanson argues that the West\'s reliance on moral sentiment rather than strategic strength invites catastrophe.',
			'days_ago' => 1,
			'content'  => '<p>There is a peculiar delusion that afflicts prosperous democracies at the peak of their power: the conviction that the values which made them strong are themselves sufficient to keep them safe. They are not. Values must be defended, and defense requires capabilities — military, economic, and cultural — that sentiment alone cannot sustain.</p>

<!-- Featured image needed: Ancient Greek columns with modern city skyline in background -->

<p>The Romans did not fall because they stopped believing in civic virtue. They fell because they stopped being willing to do what civic virtue required — to patrol the borders, to enforce the laws, to demand of citizens what citizens had once demanded of themselves. The barbarians did not defeat Rome. Rome defeated itself, through a combination of administrative bloat, military outsourcing, and the fatal belief that its civilization was too sophisticated to be conquered by people it regarded as unsophisticated.</p>

<h3>The Modern Parallel</h3>

<p>The parallels to our current moment are uncomfortable precisely because they are obvious. The West\'s military advantage — overwhelming in 1991, substantial in 2001, and merely significant in 2015 — is now contested in every domain. China has built a navy larger than America\'s. Russia has demonstrated a willingness to use military force to redraw European borders. Iran operates a network of proxies across the Middle East that no Western power has successfully countered.</p>

<p>And yet the debate in Western capitals remains focused on whether defense spending should be 2% of GDP or 2.5% — as though the threat can be managed through incremental adjustments rather than a fundamental reorientation of priorities.</p>

<h3>What Must Change</h3>

<p>The West must recover three things it has lost: the willingness to identify threats clearly, the capacity to build and sustain military forces that overmatch those threats, and the cultural confidence to assert that its civilization is worth defending — not because it is perfect, but because the alternatives on offer are measurably worse.</p>

<p>Sentiment is not strategy. Aspiration is not deterrence. And a civilization that will not fight for its own survival does not deserve to survive — a harsh truth that every previous civilization learned too late.</p>',
		),
		array(
			'title'    => 'S&P 500 Falls 1.4% After Fed Decision — Largest Decline in Three Months',
			'slug'     => 'sp500-falls-after-fed-decision-largest-decline',
			'cat'      => 'economics',
			'tags'     => array( 'Markets', 'S&P 500', 'Federal Reserve', 'Stocks' ),
			'excerpt'  => 'The broad sell-off hit technology and real estate stocks hardest as rate cut hopes faded.',
			'meta'     => 'S&P 500 drops 1.4% following Fed\'s decision to hold rates, marking the largest single-day decline in three months.',
			'days_ago' => 3,
			'content'  => '<p>The S&P 500 fell 1.4% Wednesday in the aftermath of the Federal Reserve\'s decision to hold interest rates at 5.25-5.50%, marking the index\'s sharpest single-day decline since December. The Nasdaq Composite dropped 1.8%, while the Dow Jones Industrial Average fell 1.1%, or approximately 440 points.</p>

<!-- Featured image needed: Stock market trading floor with screens showing red/declining charts -->

<p>The sell-off was broad-based but concentrated in interest rate-sensitive sectors. Real estate investment trusts (REITs) fell 2.3% on average, reflecting expectations that commercial real estate financing costs will remain elevated. Technology stocks, particularly high-growth names that had rallied on rate-cut expectations, gave back recent gains.</p>

<h3>Bond Market Impact</h3>

<p>The 10-year Treasury yield rose 12 basis points to 4.61%, its highest level in two months. The 2-year yield, more sensitive to near-term rate expectations, jumped 15 basis points to 4.92%. The moves reflect a significant repricing of rate cut expectations: futures markets now price only one 25-basis-point cut for the entirety of 2026, down from three cuts priced in at the start of the year.</p>

<h3>What Analysts Are Saying</h3>

<p>Wall Street strategists were quick to recalibrate their outlooks. "The Fed is telling us in plain language that the last mile of inflation is the hardest," wrote one chief market strategist. "The market had priced in a more favorable inflation trajectory than the data supports. Today is the reckoning."</p>

<p>Despite the decline, the S&P 500 remains up 4.2% year-to-date, and corporate earnings growth continues to surprise to the upside. The question is whether earnings strength can sustain valuations in an environment where the discount rate — heavily influenced by Fed policy — is higher than the market had assumed.</p>',
		),
	);

	// Create posts
	foreach ( $posts as $i => $post_data ) {
		// Check if post already exists
		$existing = get_page_by_path( $post_data['slug'], OBJECT, 'post' );
		if ( $existing ) {
			continue;
		}

		$post_date = gmdate( 'Y-m-d H:i:s', strtotime( '-' . $post_data['days_ago'] . ' days' ) );

		$post_id = wp_insert_post( array(
			'post_title'   => $post_data['title'],
			'post_name'    => $post_data['slug'],
			'post_content' => $post_data['content'],
			'post_excerpt' => $post_data['excerpt'],
			'post_status'  => 'publish',
			'post_author'  => $author_id,
			'post_date'    => $post_date,
			'post_date_gmt' => $post_date,
			'post_category' => isset( $cat_ids[ $post_data['cat'] ] ) ? array( $cat_ids[ $post_data['cat'] ] ) : array(),
			'tags_input'   => $post_data['tags'],
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			// Set Yoast meta description if Yoast is active
			update_post_meta( $post_id, '_yoast_wpseo_metadesc', $post_data['meta'] );
		}
	}

	// Create pages with templates
	$pages = array(
		array( 'title' => 'About', 'slug' => 'about', 'template' => 'page-about.php' ),
		array( 'title' => 'Contact', 'slug' => 'contact', 'template' => 'page-contact.php' ),
		array( 'title' => 'Advertise', 'slug' => 'advertise', 'template' => 'page-advertise.php' ),
		array( 'title' => 'Unsubscribe', 'slug' => 'unsubscribe', 'template' => 'page-unsubscribe.php' ),
		array( 'title' => 'Editorial Policy', 'slug' => 'editorial-policy', 'template' => 'page-editorial-policy.php' ),
		array( 'title' => 'Corrections & Updates Policy', 'slug' => 'corrections', 'template' => 'page-corrections.php' ),
		array( 'title' => 'Advertising Disclosure', 'slug' => 'advertising-disclosure', 'template' => 'page-advertising-disclosure.php' ),
		array( 'title' => 'Affiliate Disclosure', 'slug' => 'affiliate-disclosure', 'template' => 'page-affiliate-disclosure.php' ),
		array( 'title' => 'Disclaimer', 'slug' => 'disclaimer', 'template' => 'page-disclaimer.php' ),
		array( 'title' => 'Terms of Service', 'slug' => 'terms', 'template' => 'page-terms.php' ),
		array( 'title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'template' => 'page-privacy-policy.php' ),
		array( 'title' => 'Sources', 'slug' => 'sources', 'template' => 'page-sources.php' ),
	);

	foreach ( $pages as $page ) {
		$existing = get_page_by_path( $page['slug'] );
		if ( $existing ) {
			continue;
		}

		$page_id = wp_insert_post( array(
			'post_title'  => $page['title'],
			'post_name'   => $page['slug'],
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_author' => $author_id,
		) );

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page['template'] );
		}
	}
}
