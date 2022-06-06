<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_all_import_Triggers_wpaim_import_processed' ) ) :

 /**
  * Load the wpaim_import_processed trigger
  *
  * @since 5.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_all_import_Triggers_wpaim_import_processed {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'pmxi_after_xml_import',
				'callback' => array( $this, 'wpaim_import_processed_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpaim_import_processed-description";

		$parameter = array(
			'import_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the import.', $translation_ident ) ),
			'import' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the import.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Import processed',
			'webhook_slug' => 'pmxi_after_xml_import',
			'post_delay' => false,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wsf_submit_post_complete',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific imports only. To do that, simply specify the import ID within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => false,
			'data' => array(
				'wpwhpro_wp_all_import_trigger_on_selected_imports' => array(
					'id'		  => 'wpwhpro_wp_all_import_trigger_on_selected_imports',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'wp-all-import',
							'helper' => 'wpaim_helpers',
							'function' => 'get_query_imports',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected imports', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the imports you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wpaim_import_processed',
			'name'			  => WPWHPRO()->helpers->translate( 'Import processed', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'an import was processed', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an import was processed within WP All Import.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-all-import',
			'premium'		   => true,
		);

	}

	public function wpaim_import_processed_callback( $import_id, $import ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpaim_import_processed' );
		$response_data_array = array();
		$import_id = intval( $import_id );

		$payload = array(
			'import_id' => $import_id,
			'import' => $import,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_wp_all_import_trigger_on_selected_imports' && ! empty( $settings_data ) ){
					if( ! in_array( $import_id, $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_wpaim_import_processed', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'import_id' => 1,
			'import' => 
			array (
			  'id' => '1',
			  'parent_import_id' => '0',
			  'name' => 'Posts_Export_2022_May_14_1611_9.csv',
			  'friendly_name' => '',
			  'type' => 'upload',
			  'feed_type' => '',
			  'path' => '/wpallimport/uploads/5cff70f9fa8392490c5e00c5d0833214/Posts_Export_2022_May_14_1611_9.csv',
			  'xpath' => '/node',
			  'options' => 
			  array (
				'type' => 'post',
				'is_override_post_type' => 0,
				'post_type_xpath' => '',
				'deligate' => '',
				'wizard_type' => 'new',
				'ftp_host' => '',
				'ftp_path' => '',
				'ftp_root' => '/',
				'ftp_port' => '21',
				'ftp_username' => '',
				'ftp_password' => '',
				'ftp_private_key' => '',
				'custom_type' => 'post',
				'featured_delim' => ',',
				'atch_delim' => ',',
				'is_search_existing_attach' => '0',
				'post_taxonomies' => 
				array (
				  'category' => '[{"item_id":"1","left":2,"right":5,"parent_id":null,"xpath":"","assign":false},{"item_id":"2","left":3,"right":4,"parent_id":"1","xpath":"","assign":false}]',
				),
				'parent' => 0,
				'is_multiple_page_parent' => 'yes',
				'single_page_parent' => '',
				'order' => '0',
				'status' => 'publish',
				'page_template' => 'default',
				'is_multiple_page_template' => 'yes',
				'single_page_template' => '',
				'page_taxonomies' => 
				array (
				),
				'date_type' => 'specific',
				'date' => 'now',
				'date_start' => 'now',
				'date_end' => 'now',
				'custom_name' => 
				array (
				),
				'custom_value' => 
				array (
				),
				'custom_format' => 
				array (
				  0 => '0',
				  1 => '0',
				),
				'custom_mapping' => 
				array (
				),
				'serialized_values' => 
				array (
				  0 => '["",""]',
				  1 => '["",""]',
				),
				'custom_mapping_rules' => 
				array (
				  0 => '[]',
				  1 => '[]',
				),
				'comment_status' => 'open',
				'comment_status_xpath' => '',
				'ping_status' => 'open',
				'ping_status_xpath' => '',
				'create_draft' => 'no',
				'author' => '',
				'post_excerpt' => '',
				'post_slug' => '',
				'attachments' => '',
				'is_import_specified' => '0',
				'import_specified' => '',
				'is_delete_source' => 0,
				'is_cloak' => 0,
				'unique_key' => '- {id[1]}',
				'tmp_unique_key' => '- {id[1]}',
				'feed_type' => 'auto',
				'search_existing_images' => '1',
				'create_new_records' => '1',
				'is_selective_hashing' => '1',
				'is_delete_missing' => '0',
				'set_missing_to_draft' => '0',
				'is_update_missing_cf' => '0',
				'update_missing_cf_name' => '',
				'update_missing_cf_value' => '',
				'is_keep_former_posts' => 'no',
				'is_update_status' => '1',
				'is_update_content' => '1',
				'is_update_title' => '1',
				'is_update_slug' => '1',
				'is_update_excerpt' => '1',
				'is_update_categories' => '1',
				'is_update_author' => '1',
				'is_update_comment_status' => '1',
				'is_update_ping_status' => '1',
				'is_update_post_type' => '1',
				'is_update_post_format' => '1',
				'update_categories_logic' => 'full_update',
				'taxonomies_list' => '0',
				'taxonomies_only_list' => '',
				'taxonomies_except_list' => '',
				'is_update_attachments' => '1',
				'is_update_images' => '1',
				'update_images_logic' => 'full_update',
				'is_update_dates' => '1',
				'is_update_menu_order' => '1',
				'is_update_parent' => '1',
				'is_keep_attachments' => '0',
				'is_keep_imgs' => '0',
				'do_not_remove_images' => '1',
				'is_update_custom_fields' => '1',
				'update_custom_fields_logic' => 'full_update',
				'custom_fields_list' => '0',
				'custom_fields_only_list' => '',
				'custom_fields_except_list' => '',
				'duplicate_matching' => 'auto',
				'duplicate_indicator' => 'title',
				'custom_duplicate_name' => '',
				'custom_duplicate_value' => '',
				'is_update_previous' => 0,
				'is_scheduled' => '',
				'scheduled_period' => '',
				'friendly_name' => '',
				'records_per_request' => '20',
				'auto_rename_images' => '0',
				'auto_rename_images_suffix' => '',
				'images_name' => 'filename',
				'post_format' => 'standard',
				'post_format_xpath' => '',
				'encoding' => 'UTF-8',
				'delimiter' => ',',
				'image_meta_title' => '',
				'image_meta_title_delim' => ',',
				'image_meta_caption' => '',
				'image_meta_caption_delim' => ',',
				'image_meta_alt' => '',
				'image_meta_alt_delim' => ',',
				'image_meta_description' => '',
				'image_meta_description_delim' => ',',
				'image_meta_description_delim_logic' => 'separate',
				'status_xpath' => '',
				'download_images' => 'yes',
				'converted_options' => '1',
				'update_all_data' => 'yes',
				'is_fast_mode' => '0',
				'chuncking' => '1',
				'import_processing' => 'ajax',
				'processing_iteration_logic' => 'auto',
				'save_template_as' => '1',
				'title' => '',
				'content' => '',
				'name' => '',
				'is_keep_linebreaks' => '1',
				'is_leave_html' => '0',
				'fix_characters' => 0,
				'pid_xpath' => '',
				'slug_xpath' => '',
				'title_xpath' => '',
				'featured_image' => '',
				'download_featured_image' => '',
				'download_featured_delim' => ',',
				'gallery_featured_image' => '',
				'gallery_featured_delim' => ',',
				'is_featured' => '1',
				'is_featured_xpath' => '',
				'set_image_meta_title' => '0',
				'set_image_meta_caption' => '0',
				'set_image_meta_alt' => '0',
				'set_image_meta_description' => '0',
				'auto_set_extension' => '0',
				'new_extension' => '',
				'tax_logic' => 
				array (
				  'category' => 'single',
				  'post_tag' => 'single',
				),
				'tax_assing' => 
				array (
				  'category' => '0',
				  'post_tag' => '0',
				),
				'term_assing' => 
				array (
				  'category' => '1',
				  'post_tag' => '1',
				),
				'multiple_term_assing' => 
				array (
				  'category' => '1',
				  'post_tag' => '1',
				),
				'tax_hierarchical_assing' => 
				array (
				  'category' => 
				  array (
					0 => '1',
					'NUMBER' => '1',
				  ),
				),
				'tax_hierarchical_last_level_assign' => 
				array (
				  'category' => '0',
				),
				'tax_single_xpath' => 
				array (
				  'category' => '',
				  'post_tag' => '',
				),
				'tax_multiple_xpath' => 
				array (
				  'category' => '',
				  'post_tag' => '',
				),
				'tax_hierarchical_xpath' => 
				array (
				  'category' => 
				  array (
					0 => '',
					1 => '',
				  ),
				),
				'tax_multiple_delim' => 
				array (
				  'category' => ',',
				  'post_tag' => ',',
				),
				'tax_hierarchical_delim' => 
				array (
				  'category' => '>',
				),
				'tax_manualhierarchy_delim' => 
				array (
				  'category' => ',',
				),
				'tax_hierarchical_logic_entire' => 
				array (
				  'category' => '0',
				),
				'tax_hierarchical_logic_manual' => 
				array (
				  'category' => '0',
				),
				'tax_enable_mapping' => 
				array (
				  'category' => '0',
				  'post_tag' => '0',
				),
				'tax_is_full_search_single' => 
				array (
				  'category' => '0',
				  'post_tag' => '0',
				),
				'tax_is_full_search_multiple' => 
				array (
				  'category' => '0',
				  'post_tag' => '0',
				),
				'tax_assign_to_one_term_single' => 
				array (
				  'category' => '0',
				  'post_tag' => '0',
				),
				'tax_assign_to_one_term_multiple' => 
				array (
				  'category' => '0',
				  'post_tag' => '0',
				),
				'tax_mapping' => 
				array (
				  'category' => '[]',
				  'post_tag' => '[]',
				),
				'tax_logic_mapping' => 
				array (
				  'category' => '0',
				  'post_tag' => '0',
				),
				'is_tax_hierarchical_group_delim' => 
				array (
				  'category' => '0',
				),
				'tax_hierarchical_group_delim' => 
				array (
				  'category' => '|',
				),
				'nested_files' => 
				array (
				),
				'xml_reader_engine' => '0',
				'taxonomy_type' => '',
				'taxonomy_parent' => '',
				'taxonomy_slug' => 'auto',
				'taxonomy_slug_xpath' => '',
				'taxonomy_display_type' => '',
				'taxonomy_display_type_xpath' => '',
				'import_img_tags' => '0',
				'search_existing_images_logic' => 'by_url',
				'enable_import_scheduling' => 'false',
				'scheduling_enable' => false,
				'scheduling_weekly_days' => '',
				'scheduling_run_on' => 'weekly',
				'scheduling_monthly_day' => '',
				'scheduling_times' => 
				array (
				),
				'scheduling_timezone' => 'UTC',
				'is_update_comment_post_id' => 1,
				'is_update_comment_author' => 1,
				'is_update_comment_author_email' => 1,
				'is_update_comment_author_url' => 1,
				'is_update_comment_author_IP' => 1,
				'is_update_comment_karma' => 1,
				'is_update_comment_approved' => 1,
				'is_update_comment_verified' => 1,
				'is_update_comment_rating' => 1,
				'is_update_comment_agent' => 1,
				'is_update_comment_user_id' => 1,
				'is_update_comment_type' => 1,
				'is_update_comments' => 1,
				'update_comments_logic' => 'full_update',
				'comment_author' => '',
				'comment_author_email' => '',
				'comment_author_url' => '',
				'comment_author_IP' => '',
				'comment_karma' => '',
				'comment_parent' => '',
				'comment_approved' => '1',
				'comment_approved_xpath' => '',
				'comment_verified' => '1',
				'comment_verified_xpath' => '',
				'comment_agent' => '',
				'comment_type' => '',
				'comment_type_xpath' => '',
				'comment_user_id' => 'email',
				'comment_user_id_xpath' => '',
				'comment_post' => '',
				'comment_rating' => '',
				'comments_repeater_mode' => 'csv',
				'comments_repeater_mode_separator' => '|',
				'comments_repeater_mode_foreach' => '',
				'comments' => 
				array (
				  'content' => '',
				  'author' => '',
				  'author_email' => '',
				  'author_url' => '',
				  'author_ip' => '',
				  'karma' => '',
				  'approved' => '',
				  'type' => '',
				  'date' => 'now',
				),
			  ),
			  'registered_on' => '2022-05-15 04:49:31',
			  'root_element' => 'node',
			  'processing' => 0,
			  'executing' => 0,
			  'triggered' => 0,
			  'queue_chunk_number' => 0,
			  'first_import' => '2022-05-15 04:48:03',
			  'count' => '46',
			  'imported' => '0',
			  'created' => '0',
			  'updated' => '0',
			  'skipped' => '46',
			  'deleted' => '0',
			  'canceled' => '0',
			  'canceled_on' => '0000-00-00 00:00:00',
			  'failed' => '0',
			  'failed_on' => '0000-00-00 00:00:00',
			  'settings_update_on' => '0000-00-00 00:00:00',
			  'last_activity' => '2022-05-15 04:49:30',
			  'iteration' => 1,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.