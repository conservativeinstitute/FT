<?php
/**
 * Email Capture — Server-side handler
 *
 * @package FederalistTimes
 */

/**
 * AJAX handler for email capture (logged-in and non-logged-in users)
 */
function ft_capture_email_handler() {
	check_ajax_referer( 'ft_capture_email', 'nonce' );

	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : 'unknown';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( 'Please enter a valid email address.' );
	}

	$subscribers = get_option( 'ft_email_subscribers', array() );

	// Duplicate check
	$existing = wp_list_pluck( $subscribers, 'email' );
	if ( in_array( $email, $existing, true ) ) {
		wp_send_json_success( 'You are already subscribed.' );
	}

	$subscribers[] = array(
		'email'     => $email,
		'timestamp' => current_time( 'mysql' ),
		'source'    => $source,
		'ip'        => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
	);

	update_option( 'ft_email_subscribers', $subscribers );

	wp_send_json_success( 'Successfully subscribed.' );
}
add_action( 'wp_ajax_ft_capture_email', 'ft_capture_email_handler' );
add_action( 'wp_ajax_nopriv_ft_capture_email', 'ft_capture_email_handler' );

/**
 * Admin page under Tools to view captured emails
 */
function ft_email_admin_menu() {
	add_management_page(
		'Email Subscribers',
		'Email Subscribers',
		'manage_options',
		'ft-email-subscribers',
		'ft_email_admin_page'
	);
}
add_action( 'admin_menu', 'ft_email_admin_menu' );

/**
 * Admin page render
 */
function ft_email_admin_page() {
	// Handle CSV export
	if ( isset( $_GET['ft_export_csv'] ) && wp_verify_nonce( $_GET['ft_export_csv'], 'ft_export_csv' ) ) {
		ft_export_csv();
		return;
	}

	$subscribers = get_option( 'ft_email_subscribers', array() );
	$count       = count( $subscribers );
	$nonce       = wp_create_nonce( 'ft_export_csv' );
	?>
	<div class="wrap">
		<h1>Email Subscribers (<?php echo esc_html( $count ); ?>)</h1>
		<?php if ( $count > 0 ) : ?>
			<p>
				<a href="<?php echo esc_url( admin_url( 'tools.php?page=ft-email-subscribers&ft_export_csv=' . $nonce ) ); ?>" class="button button-primary">
					Export CSV
				</a>
			</p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th>Email</th>
						<th>Source</th>
						<th>Date</th>
						<th>IP</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( array_reverse( $subscribers ) as $sub ) : ?>
						<tr>
							<td><?php echo esc_html( $sub['email'] ); ?></td>
							<td><?php echo esc_html( $sub['source'] ); ?></td>
							<td><?php echo esc_html( $sub['timestamp'] ); ?></td>
							<td><?php echo esc_html( $sub['ip'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p>No subscribers yet.</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * CSV export
 */
function ft_export_csv() {
	$subscribers = get_option( 'ft_email_subscribers', array() );

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=ft-subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );

	$output = fopen( 'php://output', 'w' );
	fputcsv( $output, array( 'Email', 'Source', 'Date', 'IP' ) );

	foreach ( $subscribers as $sub ) {
		fputcsv( $output, array(
			$sub['email'],
			$sub['source'],
			$sub['timestamp'],
			$sub['ip'],
		) );
	}

	fclose( $output );
	exit;
}

/**
 * Dashboard widget showing subscriber count
 */
function ft_email_dashboard_widget() {
	wp_add_dashboard_widget(
		'ft_email_widget',
		'Email Subscribers',
		function () {
			$count = count( get_option( 'ft_email_subscribers', array() ) );
			echo '<p style="font-size:2rem;font-weight:700;margin:0;">' . esc_html( $count ) . '</p>';
			echo '<p>total email subscribers</p>';
			echo '<a href="' . esc_url( admin_url( 'tools.php?page=ft-email-subscribers' ) ) . '">View all &rarr;</a>';
		}
	);
}
add_action( 'wp_dashboard_setup', 'ft_email_dashboard_widget' );
