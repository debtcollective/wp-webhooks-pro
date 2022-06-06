<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_all_export_Triggers_wpaex_export_created' ) ) :

 /**
  * Load the wpaex_export_created trigger
  *
  * @since 5.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_all_export_Triggers_wpaex_export_created {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'pmxe_after_export',
				'callback' => array( $this, 'wpaex_export_created_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpaex_export_created-description";

		$parameter = array(
			'export_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the export.', $translation_ident ) ),
			'friendly_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The readable name of the export.', $translation_ident ) ),
			'template_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The export template name.', $translation_ident ) ),
			'export_to' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The file type of the export.', $translation_ident ) ),
			'export_type' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The type of export. E.g. specific.', $translation_ident ) ),
			'encoding' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The export file encoding.', $translation_ident ) ),
			'delimiter' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The item delimiter.', $translation_ident ) ),
			'scheduling_timezone' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The timezone of the export.', $translation_ident ) ),
			'records_per_iteration' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The maximum amount of exported items per iteration.', $translation_ident ) ),
			'wpml_lang' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The slug of a specific language in case WPML (WordPress Multilingual) is used.', $translation_ident ) ),
			'file_path' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The relative file path of the created export.', $translation_ident ) ),
			'file_url' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The url of the created export file.', $translation_ident ) ),
			'bundle_path' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The relative bundle file path of the created export.', $translation_ident ) ),
			'bundle_url' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The url of the created export bundle file.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Export created',
			'webhook_slug' => 'pmxe_after_export',
			'post_delay' => false,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wsf_submit_post_complete',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific exports only. To do that, simply specify the export ID within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhpro_wp_all_export_trigger_on_selected_exports' => array(
					'id'		  => 'wpwhpro_wp_all_export_trigger_on_selected_exports',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'wp-all-export',
							'helper' => 'wpaex_helpers',
							'function' => 'get_query_exports',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected exports', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the exports you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wpaex_export_created',
			'name'			  => WPWHPRO()->helpers->translate( 'Export created', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'an export was created', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an export was created within WP All Export.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-all-export',
			'premium'		   => false,
		);

	}

	public function wpaex_export_created_callback( $export_id, $export ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpaex_export_created' );
		$response_data_array = array();
		$export_id = intval( $export_id );
		$upload_dir   = wp_upload_dir();
		$export_options = ( $export->offsetExists( 'options' ) ) ? $export->options : array();

		$payload = array(
			'export_id' => $export_id,
			'friendly_name' => ( isset( $export_options['friendly_name'] ) ) ? $export_options['friendly_name'] : '',
			'template_name' => ( isset( $export_options['template_name'] ) ) ? $export_options['template_name'] : '',
			'export_to' => ( isset( $export_options['export_to'] ) ) ? $export_options['export_to'] : '',
			'export_type' => ( isset( $export_options['export_type'] ) ) ? $export_options['export_type'] : '',
			'encoding' => ( isset( $export_options['encoding'] ) ) ? $export_options['encoding'] : '',
			'delimiter' => ( isset( $export_options['delimiter'] ) ) ? $export_options['delimiter'] : '',
			'scheduling_timezone' => ( isset( $export_options['scheduling_timezone'] ) ) ? $export_options['scheduling_timezone'] : '',
			'records_per_iteration' => ( isset( $export_options['records_per_iteration'] ) ) ? $export_options['records_per_iteration'] : '',
			'wpml_lang' => ( isset( $export_options['wpml_lang'] ) ) ? $export_options['wpml_lang'] : '',
			'file_path' => ( isset( $export_options['filepath'] ) ) ? $export_options['filepath'] : '',
			'file_url' => '',
			'bundle_path' => ( isset( $export_options['bundlepath'] ) ) ? $export_options['bundlepath'] : '',
			'bundle_url' => '',
		);

		if( ! empty( $payload['file_path'] ) ){
			$payload['file_url'] = $upload_dir['baseurl'] . $payload['file_path'];
		}

		if( ! empty( $payload['bundle_path'] ) ){
			$payload['bundle_url'] = $upload_dir['baseurl'] . $payload['bundle_path'];
		}

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_wp_all_export_trigger_on_selected_exports' && ! empty( $settings_data ) ){
					if( ! in_array( $export_id, $settings_data ) ){
					  $is_valid = false;
					}
				  }
	  
				}
			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wpaex_export_created', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'export_id' => 1,
			'friendly_name' => 'Posts Export - 2022 May 14 16:11',
			'template_name' => 'Posts Export - 2022 May 14 16:11',
			'export_to' => 'csv',
			'export_type' => 'specific',
			'encoding' => 'UTF-8',
			'delimiter' => ',',
			'scheduling_timezone' => 'Asia/Dubai',
			'records_per_iteration' => '500',
			'wpml_lang' => 'all',
			'file_path' => '/wpallexport/exports/6332289c5e95d0980db0e5a1bb90a111/Posts-Export-2022-May-14-1611-10.csv',
			'file_url' => 'https://yourdomain.test/wp-content/uploads/wpallexport/exports/6332289c5e95d0980db0e5a1bb90a111/Posts-Export-2022-May-14-1611-10.csv',
			'bundle_path' => '/wpallexport/exports/6332289c5e95d0980db0e5a1bb90a111/Posts-Export-2022-May-14-1611.zip',
			'bundle_url' => 'https://yourdomain.test/wp-content/uploads/wpallexport/exports/6332289c5e95d0980db0e5a1bb90a111/Posts-Export-2022-May-14-1611.zip',
		);

		return $data;
	}

  }

endif; // End if class_exists check.