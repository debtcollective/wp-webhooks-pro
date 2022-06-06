<?php


$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );
$log_nonce_data = WPWHPRO()->settings->get_log_nonce();
$log_count = WPWHPRO()->logs->get_log_count();
$logs = null;

// Delete all logs
if( isset( $_POST['wpwhpro_delete_logs'] ) ) {
	if ( check_admin_referer( $log_nonce_data['action'], $log_nonce_data['arg'] ) ) {
		if ( current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-delete-logs' ), array() ) ) ) { //$webhook argument is deprecated
			WPWHPRO()->logs->delete_log();

			echo WPWHPRO()->helpers->create_admin_notice( 'All logs have bee successfully deleted.', 'success', true );
		}
	}
}

// Delete a single log
if( isset( $_GET['wpwhpro_log_delete'] ) ) {
	if ( current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-delete-log' ), array() ) ) ) { //$webhook argument is deprecated
		$log_id = intval( $_GET['wpwhpro_log_delete'] );

		unset( $_GET['wpwhpro_log_delete'] );
		$clear_form_url = str_replace( '&wpwhpro_log_delete=' . $log_id, '', $clear_form_url );

		WPWHPRO()->logs->delete_log( $log_id );

		echo WPWHPRO()->helpers->create_admin_notice( array(
			'Log has been successfully deleted: %s',
			$log_id,
		), 'success', true );
	}
}

$per_page = '';
$current_offset = '';
$per_page = ( isset( $_POST['item_count'] ) && ! empty( $_POST['item_count'] ) ) ? intval( $_POST['item_count'] ) : 10;
$per_page = ( isset( $_GET['item_count'] ) && ! empty( $_GET['item_count'] ) && ! isset( $_POST['item_count'] ) ) ? intval( $_GET['item_count'] ) : $per_page;
$log_page = ( isset( $_GET['log_page'] ) && ! empty( $_GET['log_page'] ) ) ? intval( $_GET['log_page'] ) : 1;
$log_last_page = ceil( $log_count / $per_page );

if( isset( $_POST['item_count'] ) && isset( $_POST['item_offset'] ) || isset( $_GET['log_page'] ) ){

	if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-filter-logs' ), 'wpwhpro-page-logs-filter-logs' ) ){
		if( isset( $_GET['log_page'] ) ){
			if ( current_user_can( apply_filters( 'wpwhpro/admin/settings/webhook/page_capability', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-logs-paginate-logs' ), array() ) ) ) { //$webhook argument is deprecated
				
				$log_page = intval( $_GET['log_page'] );
				
				$per_page = $per_page;
				$current_offset = ( $log_page - 1 ) * $per_page;
	
				$logs = WPWHPRO()->logs->get_log( $current_offset, $per_page );
			}
		} else {
			if( check_admin_referer( $log_nonce_data['action'], $log_nonce_data['arg'] ) ){
				$current_offset = ( ! empty( $_POST['item_offset'] ) ) ? intval( $_POST['item_offset'] ) : 0;
		
				$logs = WPWHPRO()->logs->get_log( $current_offset, $per_page );
			}
		}
	}

}

if( $logs === null ){
	$logs = WPWHPRO()->logs->get_log();
}

?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
		<h2><?php echo WPWHPRO()->helpers->translate( 'Logs', 'wpwhpro-page-logs' ); ?></h2>
		<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_logs' ) ) ) : ?>
			<p><?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_logs' ), 'admin-settings-license' ); ?></p>
		<?php else : ?>
			<p><?php echo sprintf( WPWHPRO()->helpers->translate( 'The log feature will log every single request of your website that was triggered either by a trigger or by a valid action. An action is valid once the authentication of the webhook URL was successful. To find out more about a specific log, Check its details with the button on the right.', 'wpwhpro-page-logs' ), WPWHPRO()->settings->get_page_title() ); ?></p>
		<?php endif; ?>
  </div>

  <div class="wpwh-table-container mb-5">
	  <div class="wpwh-table-header d-flex justify-content-between">
		<div class="d-flex align-items-center justify-content-start">
			<form method="post" action="<?php echo $clear_form_url; ?>">
				<?php echo WPWHPRO()->helpers->get_nonce_field( $log_nonce_data ); ?>
				<input type="hidden" name="wpwhpro_delete_logs" value="1">
				<button type="submit" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm"><?php echo WPWHPRO()->helpers->translate( 'Delete All Logs', 'wpwhpro-page-logs' ); ?></button>
			</form>
		</div>
		<div class="d-flex align-items-center justify-content-end">
				<form method="post" action="<?php echo $clear_form_url; ?>" class="wpwh-form-filter">
					<?php echo WPWHPRO()->helpers->get_nonce_field( $log_nonce_data ); ?>
					<input type="text" class="wpwh-form-input wpwh-form-input--sm" name="item_count" value="<?php echo $per_page; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Nr. of Logs: 10', 'wpwhpro-page-logs' ); ?>">
					<input type="text" class="wpwh-form-input wpwh-form-input--sm" name="item_offset" value="<?php echo $current_offset; ?>" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Offset: 0', 'wpwhpro-page-logs' ); ?>">
					<button type="submit" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm"><?php echo WPWHPRO()->helpers->translate( 'Filter', 'wpwhpro-page-logs' ); ?></button>
				</form>
		</div>
	  </div>

		<table class="wpwh-table wpwh-table--sm">
		<thead>
			<tr>
			<th><?php echo WPWHPRO()->helpers->translate( 'Log Id', 'wpwhpro-page-logs' ); ?></th>
			<th><?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-logs' ); ?></th>
			<th><?php echo WPWHPRO()->helpers->translate( 'Endpoint', 'wpwhpro-page-logs' ); ?></th>
			<th><?php echo WPWHPRO()->helpers->translate( 'Type', 'wpwhpro-page-logs' ); ?></th>
			<th><?php echo WPWHPRO()->helpers->translate( 'Date & Time', 'wpwhpro-page-logs' ); ?></th>
			<th><?php echo WPWHPRO()->helpers->translate( 'Log Version', 'wpwhpro-page-logs' ); ?></th>
			<th><?php echo WPWHPRO()->helpers->translate( 'Actions', 'wpwhpro-page-logs' ); ?></th>
			</tr>
		</thead>
		<tbody>
					<?php if( ! empty( $logs ) ) : ?>
						<?php foreach( $logs as $data ) :

							$log_time = date( 'F j, Y, g:i a', strtotime( $data->log_time ) );
							$log_version = '';
							$message = htmlspecialchars( base64_decode( $data->message ) );
							$content_backend = base64_decode( $data->content );
							$identifier = '';
							$webhook_type = '';
							$webhook_url_name = '';
							$endpoint_response = '';
							$content = '';

							if( WPWHPRO()->helpers->is_json( $content_backend ) ){
									$single_data = json_decode( $content_backend, true );
									if( $single_data && is_array( $single_data ) ){

										if( isset( $single_data['request_data'] ) ){
											$content = WPWHPRO()->logs->sanitize_array_object_values( $single_data['request_data'] );
										}

										if( isset( $single_data['response_data'] ) ){
											$endpoint_response = WPWHPRO()->logs->sanitize_array_object_values( $single_data['response_data'] );
										}

										if( isset( $single_data['identifier'] ) ){
											$identifier = htmlspecialchars( $single_data['identifier'] );
										}

										if( isset( $single_data['webhook_type'] ) ){
											$webhook_type = htmlspecialchars( $single_data['webhook_type'] );
										}

										if( isset( $single_data['webhook_name'] ) ){
											$webhook_name = htmlspecialchars( $single_data['webhook_name'] );
										}

										if( isset( $single_data['webhook_url_name'] ) ){
											$webhook_url_name = htmlspecialchars( $single_data['webhook_url_name'] );
										}

										if( isset( $single_data['log_version'] ) ){
											$log_version = htmlspecialchars( $single_data['log_version'] );
										}

									}
							}

							?>
							<tr id="log-element-<?php echo $data->id; ?>" >
								<td class="align-middle">#<?php echo $data->id; ?></td>
								<td class="align-middle"><?php echo sanitize_title( $webhook_url_name ); ?></td>
								<td class="align-middle"><?php echo sanitize_title( $webhook_name ); ?></td>
								<td class="align-middle"><?php echo $webhook_type; ?></td>
								<td class="align-middle"><?php echo $log_time; ?></td>
								<td class="align-middle"><?php echo $log_version; ?></td>
								<td class="align-middle">
									<div class="dropdown">
										<button type="button" class="wpwh-btn wpwh-btn--link px-2 py-3 wpwh-dropdown-trigger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/settings.svg'; ?>" alt="Settings Icon">
											<span class="sr-only">Options</span>
										</button>
										<div class="dropdown-menu">
											<a
												class="dropdown-item"
												href="<?php echo WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'wpwhpro_log_delete' => $data->id, ) ) ); ?>"
												title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-logs' ); ?>"
											>
												<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
												<span><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-logs' ); ?></span>
											</a>
											<a
												class="dropdown-item"
												href="#"

												data-toggle="modal"
												data-target="#wpwhLogId<?php echo $data->id; ?>"
											>
												<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="<?php echo WPWHPRO()->helpers->translate( 'Details', 'wpwhpro-page-logs' ); ?>">
												<span><?php echo WPWHPRO()->helpers->translate( 'Details', 'wpwhpro-page-logs' ); ?></span>
											</a>
										</div>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<?php if( isset( $_POST['item_offset'] ) ) : ?>
								<td colspan="7" class="wpwh-text-center"><?php echo WPWHPRO()->helpers->translate( 'We could not find any logs for your given settings.', 'wpwhpro-page-logs' ); ?></td>
							<?php else : ?>
								<td colspan="7" class="wpwh-text-center"><?php echo WPWHPRO()->helpers->translate( 'You currently don\'t have any logs available.', 'wpwhpro-page-logs' ); ?></td>
							<?php endif; ?>
						</tr>
					<?php endif; ?>
		</tbody>
		</table>

		<div class="wpwh-table-header d-flex justify-content-between">
			<div class="d-flex align-items-center justify-content-start">
				<?php echo WPWHPRO()->logs->pagination( array( 'log_page' => $log_page, 'per_page' => $per_page ) ); ?>
			</div>
			<div class="d-flex align-items-center justify-content-end">
			<?php echo sprintf( WPWHPRO()->helpers->translate( 'Page %1$d of %2$d', 'wpwhpro-page-logs' ), $log_page, $log_last_page ); ?>
			</div>
		</div>
	</div>

</div>

<?php if( ! empty( $logs ) ) : ?>
	<?php foreach( $logs as $data ) :

		$log_time = date( 'F j, Y, g:i a', strtotime( $data->log_time ) );
		$log_version = '';
		$message = htmlspecialchars( base64_decode( $data->message ) );
		$content_backend = base64_decode( $data->content );
		$identifier = '';
		$webhook_type = '';
		$webhook_url_name = '';
		$endpoint_response = '';
		$content = '';

		if( WPWHPRO()->helpers->is_json( $content_backend ) ){
				$single_data = json_decode( $content_backend, true );
				if( $single_data && is_array( $single_data ) ){

					if( isset( $single_data['request_data'] ) ){
						$content = WPWHPRO()->logs->sanitize_array_object_values( $single_data['request_data'] );
					}

					if( isset( $single_data['response_data'] ) ){
						$endpoint_response = WPWHPRO()->logs->sanitize_array_object_values( $single_data['response_data'] );
					}

					if( isset( $single_data['identifier'] ) ){
						$identifier = htmlspecialchars( $single_data['identifier'] );
					}

					if( isset( $single_data['webhook_type'] ) ){
						$webhook_type = htmlspecialchars( $single_data['webhook_type'] );
					}

					if( isset( $single_data['webhook_name'] ) ){
						$webhook_name = htmlspecialchars( $single_data['webhook_name'] );
					}

					if( isset( $single_data['webhook_url_name'] ) ){
						$webhook_url_name = htmlspecialchars( $single_data['webhook_url_name'] );
					}

					if( isset( $single_data['log_version'] ) ){
						$log_version = htmlspecialchars( $single_data['log_version'] );
					}
				}
		}

		?>
		<div class="modal modal--lg fade" id="wpwhLogId<?php echo $data->id; ?>" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">#<?php echo $data->id; ?> <?php echo WPWHPRO()->helpers->translate( 'Log Details', 'wpwhpro-page-whitelist' ); ?></h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</div>
					<div class="modal-body">
						<div class="row pt-4">
							<div class="wpwh-table-container">
								<table class="wpwh-table wpwh-table--sm">
									<thead>
										<tr>
											<th><?php echo WPWHPRO()->helpers->translate( 'Webhook Name', 'wpwhpro-page-logs' ); ?></th>
											<th><?php echo WPWHPRO()->helpers->translate( 'Endpoint', 'wpwhpro-page-logs' ); ?></th>
											<th><?php echo WPWHPRO()->helpers->translate( 'Type', 'wpwhpro-page-logs' ); ?></th>
											<th><?php echo WPWHPRO()->helpers->translate( 'Date & Time', 'wpwhpro-page-logs' ); ?></th>
											<th><?php echo WPWHPRO()->helpers->translate( 'Log Version', 'wpwhpro-page-logs' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><?php echo sanitize_title( $webhook_url_name ); ?></td>
											<td><?php echo sanitize_title( $webhook_name ); ?></td>
											<td><?php echo $webhook_type; ?></td>
											<td><?php echo $log_time; ?></td>
											<td><?php echo $log_version; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="row pt-4">
							<div class="col-md-12">
								<h4><?php echo WPWHPRO()->helpers->translate( 'Identifier', 'wpwhpro-page-log' ); ?></h4>
								<p><?php echo WPWHPRO()->helpers->translate( 'The identifier contains either an IP if the request was an action, or the webhook URL if it was a trigger. It can also contain "test" in case you send a demo request.', 'wpwhpro-page-log' ); ?></p>
								<pre><?php echo $identifier; ?></pre>
							</div>
						</div>
						<?php if( isset( $content['content_type'] ) && ! empty( $content['content_type'] ) ) : ?>
							<div class="row pt-4">
								<div class="col-md-12">
									<h4><?php echo WPWHPRO()->helpers->translate( 'Content Type', 'wpwhpro-page-log' ); ?></h4>
									<pre><?php echo $content['content_type']; ?></pre>
								</div>
							</div>
						<?php endif; ?>
						<div class="row pt-4">
							<?php if( $webhook_type === 'action' || $webhook_type === 'flow_action' ) : 
							
							$response_data = ( isset( $single_data['response_data'] ) && isset( $single_data['response_data']['arguments'] ) ) ? $single_data['response_data']['arguments'] : null;

							?>

								<div class="col-md-<?php echo ( $response_data ) ? '6' : '12'; ?>">
									<h4><?php echo WPWHPRO()->helpers->translate( 'Incoming data:', 'wpwhpro-page-log' ); ?></h4>
									<p><?php echo sprintf( WPWHPRO()->helpers->translate( 'The JSON down below contains the full data that was sent to the webhook URL of %s, after data mapping was applied.', 'wpwhpro-page-log' ), WPWHPRO()->settings->get_page_title()); ?></p>
									<?php if( isset( $content['content'] ) ) : ?>
										<pre id="wpwhpro-log-json-output-response-<?php echo $data->id; ?>"><?php echo json_encode( $content['content'], JSON_PRETTY_PRINT ); ?></pre>
									<?php endif; ?>
								</div>

								<?php if( $response_data ) : ?>
									<div class="col-md-6">
										<h4><?php echo WPWHPRO()->helpers->translate( 'Outgoing data:', 'wpwhpro-page-log' ); ?></h4>
										<p><?php echo WPWHPRO()->helpers->translate( 'The JSON down below contains the whole data we sent back to your webhook action caller.', 'wpwhpro-page-log' ); ?></p>
										<?php if( ! empty( $content ) ) : ?>
											<pre id="wpwhpro-log-json-output-payload-<?php echo $data->id; ?>"><?php echo json_encode( $response_data, JSON_PRETTY_PRINT ); ?></pre>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							<?php else : 

							$trigger_content = ( ! empty( $single_data['request_data'] ) && ! is_string( $single_data['request_data'] ) ) ? (array) $single_data['request_data'] : '';

							if( isset( $trigger_content['body'] ) && WPWHPRO()->helpers->is_json( $trigger_content['body'] ) ){
								$trigger_content['body'] = json_decode( $trigger_content['body'], true );
							}

							$trigger_header = ( isset( $trigger_content['headers'] ) ) ? htmlspecialchars( json_encode( $trigger_content['headers'], JSON_PRETTY_PRINT ) ) : '';
							$trigger_payload = ( isset( $trigger_content['body'] ) ) ? htmlspecialchars( json_encode( $trigger_content['body'], JSON_PRETTY_PRINT ) ) : '';
							
							?>
								<div class="col-md-6">
									<h4><?php echo WPWHPRO()->helpers->translate( 'Outgoing data:', 'wpwhpro-page-log' ); ?></h4>
									<p><?php echo WPWHPRO()->helpers->translate( 'The JSON down below contains the whole request we sent based on your fired trigger. You will find the data within the body key.', 'wpwhpro-page-log' ); ?></p>
									<?php if( ! empty( $content ) ) : ?>
										<pre id="wpwhpro-log-json-output-payload-<?php echo $data->id; ?>"><?php echo $trigger_payload; ?></pre>
									<?php endif; ?>
								</div>
								<div class="col-md-6">
									<h4><?php echo WPWHPRO()->helpers->translate( 'Endpoint Response:', 'wpwhpro-page-log' ); ?></h4>
									<p><?php echo WPWHPRO()->helpers->translate( 'In JSON contains the data we got back from the server where we sent the webhook request to.', 'wpwhpro-page-log' ); ?></p>
									<?php if( isset( $endpoint_response ) ) : ?>
										<pre id="wpwhpro-log-json-output-response-<?php echo $data->id; ?>"><?php echo json_encode( $endpoint_response, JSON_PRETTY_PRINT ); ?></pre>
									<?php endif; ?>
								</div>
								<div class="col-md-6">
									<h4><?php echo WPWHPRO()->helpers->translate( 'Outgoing header data:', 'wpwhpro-page-log' ); ?></h4>
									<p><?php echo WPWHPRO()->helpers->translate( 'The JSON down below contains all headers that have been sent along with the trigger.', 'wpwhpro-page-log' ); ?></p>
									<?php if( ! empty( $trigger_header ) ) : ?>
										<pre id="wpwhpro-log-json-output-header-<?php echo $data->id; ?>"><?php echo $trigger_header; ?></pre>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>