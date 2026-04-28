<?php
/**
 * Email Capture - Server-side handler.
 *
 * @package FederalistTimes
 */

/**
 * Read a server-side Beehiiv configuration value.
 *
 * Values can be set as WordPress constants in wp-config.php or as matching
 * environment variables. Do not expose these values to browser JavaScript.
 */
function ft_beehiiv_config_value( $constant ) {
	if ( defined( $constant ) ) {
		$value = constant( $constant );

		if ( is_string( $value ) || is_numeric( $value ) ) {
			return trim( (string) $value );
		}
	}

	$value = getenv( $constant );

	if ( is_string( $value ) || is_numeric( $value ) ) {
		return trim( (string) $value );
	}

	return '';
}

/**
 * Read a boolean Beehiiv configuration value.
 */
function ft_beehiiv_config_bool( $constant, $default = false ) {
	$value = ft_beehiiv_config_value( $constant );

	if ( '' === $value ) {
		return (bool) $default;
	}

	return in_array( strtolower( $value ), array( '1', 'true', 'yes', 'on' ), true );
}

/**
 * Read a comma-separated or array-based Beehiiv configuration list.
 */
function ft_beehiiv_config_list( $constant ) {
	$value = null;

	if ( defined( $constant ) ) {
		$value = constant( $constant );
	} else {
		$value = getenv( $constant );
	}

	if ( empty( $value ) ) {
		return array();
	}

	if ( ! is_array( $value ) ) {
		$value = preg_split( '/[\s,]+/', (string) $value );
	}

	return array_values( array_filter( array_map( 'sanitize_text_field', $value ) ) );
}

/**
 * Get all Beehiiv integration settings.
 */
function ft_beehiiv_config() {
	$double_opt_override = ft_beehiiv_config_value( 'FT_BEEHIIV_DOUBLE_OPT_OVERRIDE' );

	if ( ! in_array( $double_opt_override, array( 'on', 'off', 'not_set' ), true ) ) {
		$double_opt_override = 'not_set';
	}

	return array(
		'api_key'             => ft_beehiiv_config_value( 'FT_BEEHIIV_API_KEY' ),
		'publication_id'      => ft_beehiiv_config_value( 'FT_BEEHIIV_PUBLICATION_ID' ),
		'newsletter_list_ids' => ft_beehiiv_config_list( 'FT_BEEHIIV_NEWSLETTER_LIST_IDS' ),
		'automation_ids'      => ft_beehiiv_config_list( 'FT_BEEHIIV_AUTOMATION_IDS' ),
		'double_opt_override' => $double_opt_override,
		'reactivate_existing' => ft_beehiiv_config_bool( 'FT_BEEHIIV_REACTIVATE_EXISTING', true ),
		'send_welcome_email'  => ft_beehiiv_config_bool( 'FT_BEEHIIV_SEND_WELCOME_EMAIL', false ),
	);
}

/**
 * Determine whether the Beehiiv subscription call can run.
 */
function ft_beehiiv_is_enabled() {
	$config = ft_beehiiv_config();

	return ! empty( $config['api_key'] ) && ! empty( $config['publication_id'] );
}

/**
 * Pull acquisition attribution from the AJAX request.
 */
function ft_email_request_attribution() {
	$fields      = array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' );
	$attribution = array();

	foreach ( $fields as $field ) {
		$attribution[ $field ] = isset( $_POST[ $field ] )
			? sanitize_text_field( wp_unslash( $_POST[ $field ] ) )
			: '';
	}

	$attribution['page_url'] = isset( $_POST['page_url'] )
		? esc_url_raw( wp_unslash( $_POST['page_url'] ) )
		: '';

	if ( empty( $attribution['page_url'] ) ) {
		$attribution['page_url'] = esc_url_raw( wp_get_referer() );
	}

	return $attribution;
}

/**
 * Verify AJAX requests without breaking cached public pages.
 *
 * Logged-out newsletter forms are often served from page cache, which can make
 * WordPress nonces stale. Logged-in users keep nonce protection.
 */
function ft_verify_email_ajax_request() {
	if ( is_user_logged_in() ) {
		check_ajax_referer( 'ft_capture_email', 'nonce' );
	}
}

/**
 * Lightweight public-form rate limit.
 */
function ft_email_rate_limit_allows_request( $action ) {
	$ip    = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );
	$key   = 'ft_email_rate_' . md5( $action . '|' . $ip );
	$count = (int) get_transient( $key );

	if ( $count >= 10 ) {
		return false;
	}

	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	return true;
}

/**
 * Extract a readable, sanitized API error message.
 */
function ft_beehiiv_error_message( $body, $status_code ) {
	if ( is_array( $body ) ) {
		foreach ( array( 'message', 'error', 'detail' ) as $key ) {
			if ( ! empty( $body[ $key ] ) && is_scalar( $body[ $key ] ) ) {
				return sanitize_text_field( (string) $body[ $key ] );
			}
		}

		if ( ! empty( $body['errors'] ) && is_array( $body['errors'] ) ) {
			$first = reset( $body['errors'] );

			if ( is_array( $first ) && ! empty( $first['message'] ) ) {
				return sanitize_text_field( (string) $first['message'] );
			}

			if ( is_scalar( $first ) ) {
				return sanitize_text_field( (string) $first );
			}
		}
	}

	return 'Beehiiv returned HTTP ' . absint( $status_code ) . '.';
}

/**
 * Subscribe an email address to Beehiiv.
 */
function ft_beehiiv_subscribe( $email, $source, $attribution ) {
	$config = ft_beehiiv_config();

	if ( empty( $config['api_key'] ) || empty( $config['publication_id'] ) ) {
		return array(
			'success' => false,
			'status'  => 'local_only',
			'message' => 'Beehiiv is not configured.',
		);
	}

	$payload = array(
		'email'               => $email,
		'reactivate_existing' => (bool) $config['reactivate_existing'],
		'send_welcome_email'  => (bool) $config['send_welcome_email'],
		'utm_source'          => $attribution['utm_source'] ?: 'federalisttimes.com',
		'utm_medium'          => $attribution['utm_medium'] ?: 'website',
		'utm_campaign'        => $attribution['utm_campaign'] ?: $source,
		'referring_site'      => $attribution['page_url'] ?: home_url( '/' ),
	);

	foreach ( array( 'utm_term', 'utm_content' ) as $field ) {
		if ( ! empty( $attribution[ $field ] ) ) {
			$payload[ $field ] = $attribution[ $field ];
		}
	}

	if ( 'not_set' !== $config['double_opt_override'] ) {
		$payload['double_opt_override'] = $config['double_opt_override'];
	}

	if ( ! empty( $config['newsletter_list_ids'] ) ) {
		$payload['newsletter_list_ids'] = $config['newsletter_list_ids'];
	}

	if ( ! empty( $config['automation_ids'] ) ) {
		$payload['automation_ids'] = $config['automation_ids'];
	}

	$endpoint = sprintf(
		'https://api.beehiiv.com/v2/publications/%s/subscriptions',
		rawurlencode( $config['publication_id'] )
	);

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array(
				'Authorization' => 'Bearer ' . $config['api_key'],
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return array(
			'success' => false,
			'status'  => 'pending',
			'message' => sanitize_text_field( $response->get_error_message() ),
		);
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$body        = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $status_code >= 200 && $status_code < 300 ) {
		return array(
			'success'         => true,
			'status'          => 'synced',
			'message'         => 'Subscribed in Beehiiv.',
			'subscription_id' => isset( $body['data']['id'] ) ? sanitize_text_field( $body['data']['id'] ) : '',
		);
	}

	$message = ft_beehiiv_error_message( $body, $status_code );

	if ( 400 === $status_code && preg_match( '/already|exist|duplicate/i', $message ) ) {
		return array(
			'success' => true,
			'status'  => 'synced',
			'message' => 'Already exists in Beehiiv.',
		);
	}

	error_log( 'FT Beehiiv subscribe failed for email hash ' . hash( 'sha256', strtolower( $email ) ) . ': ' . $message );

	return array(
		'success' => false,
		'status'  => 'pending',
		'message' => $message,
	);
}

/**
 * Unsubscribe an email address from Beehiiv.
 */
function ft_beehiiv_unsubscribe( $email ) {
	$config = ft_beehiiv_config();

	if ( empty( $config['api_key'] ) || empty( $config['publication_id'] ) ) {
		return array(
			'success' => false,
			'status'  => 'local_only',
			'message' => 'Beehiiv is not configured.',
		);
	}

	$endpoint = sprintf(
		'https://api.beehiiv.com/v2/publications/%s/subscriptions/by_email/%s',
		rawurlencode( $config['publication_id'] ),
		rawurlencode( $email )
	);

	$response = wp_remote_request(
		$endpoint,
		array(
			'method'  => 'PUT',
			'timeout' => 10,
			'headers' => array(
				'Authorization' => 'Bearer ' . $config['api_key'],
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( array( 'unsubscribe' => true ) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return array(
			'success' => false,
			'status'  => 'failed',
			'message' => sanitize_text_field( $response->get_error_message() ),
		);
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );

	if ( $status_code >= 200 && $status_code < 300 ) {
		return array(
			'success' => true,
			'status'  => 'unsubscribed',
			'message' => 'Unsubscribed in Beehiiv.',
		);
	}

	if ( 404 === $status_code ) {
		return array(
			'success' => true,
			'status'  => 'not_found',
			'message' => 'Email was not found in Beehiiv.',
		);
	}

	$body    = json_decode( wp_remote_retrieve_body( $response ), true );
	$message = ft_beehiiv_error_message( $body, $status_code );

	error_log( 'FT Beehiiv unsubscribe failed for email hash ' . hash( 'sha256', strtolower( $email ) ) . ': ' . $message );

	return array(
		'success' => false,
		'status'  => 'failed',
		'message' => $message,
	);
}

/**
 * Store or update the local subscriber ledger.
 */
function ft_store_email_subscriber( $email, $source, $attribution, $beehiiv_result ) {
	$subscribers = get_option( 'ft_email_subscribers', array() );
	$now         = current_time( 'mysql' );
	$ip          = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$found_key   = null;

	foreach ( $subscribers as $key => $sub ) {
		if ( isset( $sub['email'] ) && strtolower( $sub['email'] ) === strtolower( $email ) ) {
			$found_key = $key;
			break;
		}
	}

	$record = array(
		'email'                   => $email,
		'timestamp'               => $now,
		'source'                  => $source,
		'ip'                      => $ip,
		'page_url'                => $attribution['page_url'],
		'beehiiv_status'          => isset( $beehiiv_result['status'] ) ? sanitize_text_field( $beehiiv_result['status'] ) : 'unknown',
		'beehiiv_subscription_id' => isset( $beehiiv_result['subscription_id'] ) ? sanitize_text_field( $beehiiv_result['subscription_id'] ) : '',
		'beehiiv_last_attempt'    => $now,
		'beehiiv_last_error'      => empty( $beehiiv_result['success'] ) && ! empty( $beehiiv_result['message'] )
			? substr( sanitize_text_field( $beehiiv_result['message'] ), 0, 255 )
			: '',
	);

	if ( null !== $found_key ) {
		$record['timestamp']       = isset( $subscribers[ $found_key ]['timestamp'] ) ? $subscribers[ $found_key ]['timestamp'] : $now;
		$record['last_seen']       = $now;
		$subscribers[ $found_key ] = array_merge( $subscribers[ $found_key ], $record );
	} else {
		$subscribers[] = $record;
	}

	update_option( 'ft_email_subscribers', array_values( $subscribers ) );
}

/**
 * Retry local-only or pending subscribers once Beehiiv is configured.
 */
function ft_beehiiv_sync_pending_subscribers( $limit = 25 ) {
	if ( ! ft_beehiiv_is_enabled() ) {
		return 0;
	}

	$subscribers = get_option( 'ft_email_subscribers', array() );
	$attempted   = 0;
	$synced      = 0;

	foreach ( $subscribers as $key => $sub ) {
		if ( $attempted >= $limit ) {
			break;
		}

		$status = isset( $sub['beehiiv_status'] ) ? $sub['beehiiv_status'] : 'legacy';

		if ( 'synced' === $status || empty( $sub['email'] ) || ! is_email( $sub['email'] ) ) {
			continue;
		}

		$source      = isset( $sub['source'] ) ? sanitize_text_field( $sub['source'] ) : 'legacy';
		$attribution = array(
			'utm_source'   => '',
			'utm_medium'   => '',
			'utm_campaign' => '',
			'utm_term'     => '',
			'utm_content'  => '',
			'page_url'     => isset( $sub['page_url'] ) ? esc_url_raw( $sub['page_url'] ) : '',
		);
		$result      = ft_beehiiv_subscribe( sanitize_email( $sub['email'] ), $source, $attribution );
		$now         = current_time( 'mysql' );
		$attempted++;

		$subscribers[ $key ]['beehiiv_status']          = isset( $result['status'] ) ? sanitize_text_field( $result['status'] ) : 'unknown';
		$subscribers[ $key ]['beehiiv_subscription_id'] = isset( $result['subscription_id'] ) ? sanitize_text_field( $result['subscription_id'] ) : '';
		$subscribers[ $key ]['beehiiv_last_attempt']    = $now;
		$subscribers[ $key ]['beehiiv_last_error']      = empty( $result['success'] ) && ! empty( $result['message'] )
			? substr( sanitize_text_field( $result['message'] ), 0, 255 )
			: '';

		if ( ! empty( $result['success'] ) ) {
			$synced++;
		}
	}

	update_option( 'ft_email_subscribers', array_values( $subscribers ) );

	return $synced;
}
add_action( 'ft_beehiiv_sync_pending_subscribers', 'ft_beehiiv_sync_pending_subscribers' );

/**
 * Schedule a lightweight retry job for pending local captures.
 */
function ft_schedule_beehiiv_sync() {
	if ( ! wp_next_scheduled( 'ft_beehiiv_sync_pending_subscribers' ) ) {
		wp_schedule_event( time() + 300, 'hourly', 'ft_beehiiv_sync_pending_subscribers' );
	}
}
add_action( 'init', 'ft_schedule_beehiiv_sync' );

/**
 * AJAX handler for email capture.
 */
function ft_capture_email_handler() {
	ft_verify_email_ajax_request();

	$email       = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$source      = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : 'unknown';
	$attribution = ft_email_request_attribution();

	if ( ! is_email( $email ) ) {
		wp_send_json_error( 'Please enter a valid email address.' );
	}

	if ( ! ft_email_rate_limit_allows_request( 'capture' ) ) {
		wp_send_json_error( 'Too many requests. Please try again shortly.' );
	}

	$beehiiv_result = ft_beehiiv_subscribe( $email, $source, $attribution );

	ft_store_email_subscriber( $email, $source, $attribution, $beehiiv_result );

	wp_send_json_success( 'Successfully subscribed.' );
}
add_action( 'wp_ajax_ft_capture_email', 'ft_capture_email_handler' );
add_action( 'wp_ajax_nopriv_ft_capture_email', 'ft_capture_email_handler' );

/**
 * AJAX handler for email unsubscribe.
 */
function ft_unsubscribe_email_handler() {
	ft_verify_email_ajax_request();

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( 'Please enter a valid email address.' );
	}

	if ( ! ft_email_rate_limit_allows_request( 'unsubscribe' ) ) {
		wp_send_json_error( 'Too many requests. Please try again shortly.' );
	}

	$subscribers = get_option( 'ft_email_subscribers', array() );
	$found       = false;

	foreach ( $subscribers as $key => $sub ) {
		if ( isset( $sub['email'] ) && strtolower( $sub['email'] ) === strtolower( $email ) ) {
			unset( $subscribers[ $key ] );
			$found = true;
			break;
		}
	}

	$beehiiv_result = ft_beehiiv_unsubscribe( $email );

	if ( ft_beehiiv_is_enabled() && empty( $beehiiv_result['success'] ) ) {
		wp_send_json_error( 'We could not complete that unsubscribe. Please try again.' );
	}

	if ( ! $found && ! ft_beehiiv_is_enabled() ) {
		wp_send_json_error( 'That email address was not found in our subscriber list.' );
	}

	update_option( 'ft_email_subscribers', array_values( $subscribers ) );

	wp_send_json_success( 'You have been successfully unsubscribed.' );
}
add_action( 'wp_ajax_ft_unsubscribe_email', 'ft_unsubscribe_email_handler' );
add_action( 'wp_ajax_nopriv_ft_unsubscribe_email', 'ft_unsubscribe_email_handler' );

/**
 * Admin page under Tools to view captured emails.
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
 * Admin page render.
 */
function ft_email_admin_page() {
	if ( isset( $_GET['ft_export_csv'] ) && wp_verify_nonce( $_GET['ft_export_csv'], 'ft_export_csv' ) ) {
		ft_export_csv();
		return;
	}

	$subscribers = get_option( 'ft_email_subscribers', array() );
	$count       = count( $subscribers );
	$synced      = count( array_filter( $subscribers, function ( $sub ) {
		return isset( $sub['beehiiv_status'] ) && 'synced' === $sub['beehiiv_status'];
	} ) );
	$pending     = count( array_filter( $subscribers, function ( $sub ) {
		return isset( $sub['beehiiv_status'] ) && in_array( $sub['beehiiv_status'], array( 'pending', 'local_only' ), true );
	} ) );
	$nonce       = wp_create_nonce( 'ft_export_csv' );
	?>
	<div class="wrap">
		<h1>Email Subscribers (<?php echo esc_html( $count ); ?>)</h1>
		<p>
			Beehiiv synced: <?php echo esc_html( $synced ); ?> |
			Pending/local only: <?php echo esc_html( $pending ); ?>
		</p>
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
						<th>Beehiiv Status</th>
						<th>Last Attempt</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( array_reverse( $subscribers ) as $sub ) : ?>
						<tr>
							<td><?php echo esc_html( $sub['email'] ); ?></td>
							<td><?php echo esc_html( $sub['source'] ?? '' ); ?></td>
							<td><?php echo esc_html( $sub['timestamp'] ?? '' ); ?></td>
							<td><?php echo esc_html( $sub['ip'] ?? '' ); ?></td>
							<td><?php echo esc_html( $sub['beehiiv_status'] ?? 'legacy' ); ?></td>
							<td><?php echo esc_html( $sub['beehiiv_last_attempt'] ?? '' ); ?></td>
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
 * CSV export.
 */
function ft_export_csv() {
	$subscribers = get_option( 'ft_email_subscribers', array() );

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=ft-subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );

	$output = fopen( 'php://output', 'w' );
	fputcsv( $output, array( 'Email', 'Source', 'Date', 'IP', 'Page URL', 'Beehiiv Status', 'Beehiiv Last Attempt', 'Beehiiv Last Error' ) );

	foreach ( $subscribers as $sub ) {
		fputcsv(
			$output,
			array(
				$sub['email'] ?? '',
				$sub['source'] ?? '',
				$sub['timestamp'] ?? '',
				$sub['ip'] ?? '',
				$sub['page_url'] ?? '',
				$sub['beehiiv_status'] ?? 'legacy',
				$sub['beehiiv_last_attempt'] ?? '',
				$sub['beehiiv_last_error'] ?? '',
			)
		);
	}

	fclose( $output );
	exit;
}

/**
 * Dashboard widget showing subscriber count.
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
