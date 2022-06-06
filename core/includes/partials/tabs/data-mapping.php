<?php

$templates = WPWHPRO()->data_mapping->get_data_mapping();
$webhook_actions = WPWHPRO()->webhook->get_hooks( 'action' );
$webhook_triggers = WPWHPRO()->webhook->get_hooks( 'trigger' );
$settings = WPWHPRO()->settings->get_data_mapping_key_settings();
$data_mapping_nonce = WPWHPRO()->settings->get_data_mapping_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );

$data_mapping_triggers = array();
foreach( $webhook_triggers as $trigger_group => $wt ){
  foreach( $wt as $st => $sd ){
    if( isset( $sd['settings'] ) ){

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping'],
        );
      }

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping_response'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping_response'],
        );
      }

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping_cookies'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping_cookies'],
        );
      }

      if( isset( $sd['settings']['wpwhpro_trigger_data_mapping_header'] ) ){
        if( ! isset( $data_mapping_triggers[ $trigger_group ] ) ){
          $data_mapping_triggers[ $trigger_group ] = array();
        }

        $data_mapping_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_data_mapping_header'],
        );
      }

    }
  }
}

$data_mapping_actions = array();
foreach( $webhook_actions as $action_name => $wa ){

  if( ! isset( $wa['api_key'] ) || ! is_string( $wa['api_key'] ) ){
    foreach( $wa as $action_slug => $action_data ){
      if( isset( $action_data['settings'] ) ){

        if( ! isset( $data_mapping_actions[ $action_slug ] ) ){
          $data_mapping_actions[ $action_slug ] = array();
        }
    
        if( isset( $action_data['settings']['wpwhpro_action_data_mapping'] ) && ! empty( $action_data['settings']['wpwhpro_action_data_mapping'] ) ){
    
          //An error caused by the Flows feature to save errors as arrays
          if( is_array( $action_data['settings']['wpwhpro_action_data_mapping'] ) && isset( $action_data['settings']['wpwhpro_action_data_mapping'][0] ) ){
            $action_data['settings']['wpwhpro_action_data_mapping'] = $action_data['settings']['wpwhpro_action_data_mapping'][0];
          }
    
          $data_mapping_actions[ $action_slug ][ $action_data['settings']['wpwhpro_action_data_mapping'] ] = array(
            'name' => sanitize_title( $action_slug ),
            'template' => $action_data['settings']['wpwhpro_action_data_mapping'],
          );
        }
    
        if( isset( $action_data['settings']['wpwhpro_action_data_mapping_response'] ) && ! empty( $action_data['settings']['wpwhpro_action_data_mapping_response'] ) ){
    
          //An error caused by the Flows feature to save errors as arrays
          if( is_array( $action_data['settings']['wpwhpro_action_data_mapping_response'] ) && isset( $action_data['settings']['wpwhpro_action_data_mapping_response'][0] ) ){
            $action_data['settings']['wpwhpro_action_data_mapping_response'] = $action_data['settings']['wpwhpro_action_data_mapping_response'][0];
          }
    
          $data_mapping_actions[ $action_slug ][ $action_data['settings']['wpwhpro_action_data_mapping_response'] ] = array(
            'name' => sanitize_title( $action_slug ),
            'template' => $action_data['settings']['wpwhpro_action_data_mapping_response'],
          );
        }
    
        if( empty( $data_mapping_actions[ $action_slug ] ) ){
          unset( $data_mapping_actions[ $action_slug ] );
        }
    
      }
    }
  }

}

if( isset( $_POST['ironikus-template-name'] ) ){
    if ( check_admin_referer( $data_mapping_nonce['action'], $data_mapping_nonce['arg'] ) ) {

      if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-data-mapping-add-template' ), 'wpwhpro-page-data-mapping-add-template' ) ){
        $data_mapping_name = isset( $_POST['ironikus-template-name'] ) ? sanitize_title( $_POST['ironikus-template-name'] ) : '';

        if( ! empty( $data_mapping_name ) ){
          $check = WPWHPRO()->data_mapping->add_template( $data_mapping_name );

          if( $check ){
            $templates = WPWHPRO()->data_mapping->get_data_mapping( 'all', true );
          }

        }
      }

    }
}

?>
<?php add_ThickBox(); ?>

<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
		<h2><?php echo WPWHPRO()->helpers->translate( 'Data Mapping', 'wpwhpro-page-data-mapping' ); ?></h2>
    <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping' ) ) ) : ?>
			<p class="wpwh-text-small wpwh-content"><?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping' ), 'admin-settings-license' ); ?></p>
		<?php else : ?>
			<p class="wpwh-text-small wpwh-content"><?php echo sprintf(WPWHPRO()->helpers->translate( 'Create your own data mapping templates down below. Mapping the data allows you to redirect certain data keys to new ones to fit the standards of %1$s (For incoming webhook actions) or your external service (For outgoing webhook triggers). For more information, please check out the data mapping documentation by clicking <a href="%2$s" target="_blank" >here</a>.', 'wpwhpro-page-data-mapping' ), WPWHPRO()->settings->get_page_title(), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-data-mapping/'); ?></p>
		<?php endif; ?>
  </div>

  <div class="wpwh-table-container mb-5">
	  <div class="wpwh-table-header d-flex align-items-center justify-content-between">
      <h4 class="mb-0"><?php echo WPWHPRO()->helpers->translate( 'Templates', 'wpwhpro-page-data-mapping' ); ?></h4>
      <button class="wpwh-btn wpwh-btn--sm wpwh-btn--secondary" title="<?php echo WPWHPRO()->helpers->translate( 'Create Template', 'wpwhpro-page-data-mapping' ); ?>" data-toggle="modal" data-target="#wpwhCreateDataMappingTemplateModal"><?php echo WPWHPRO()->helpers->translate( 'Create Template', 'wpwhpro-page-data-mapping' ); ?></button>
	  </div>

    <table class="wpwh-table wpwh-table--sm wpwh-data-mapping-templates">
      <thead>
        <tr>
          <th class="w-10"><?php echo WPWHPRO()->helpers->translate( 'Id', 'wpwhpro-page-data-mapping' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Name', 'wpwhpro-page-data-mapping' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Date & Time', 'wpwhpro-page-data-mapping' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Connected Triggers', 'wpwhpro-page-data-mapping' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Connected Actions', 'wpwhpro-page-data-mapping' ); ?></th>
          <th class="wpwh-text-center"><?php echo WPWHPRO()->helpers->translate( 'Actions', 'wpwhpro-page-data-mapping' ); ?></th>
        </tr>
      </thead>
      <tbody>
				<?php if( ! empty( $templates ) ) : ?>
          <?php foreach( $templates as $template ) :

            $log_time = date( 'F j, Y, g:i a', strtotime( $template->log_time ) );

            ?>
            <tr id="data-mapping-<?php echo $template->id; ?>">
              <td class="align-middle wpwh-text-left"><?php echo $template->id; ?></td>
              <td class="align-middle wpwh-text-left"><?php echo $template->name; ?></td>
              <td class="align-middle wpwh-text-left"><?php echo $log_time; ?></td>
              <td class="align-middle wpwh-text-left">
                <?php
                  if( ! empty( $data_mapping_triggers ) ){
                    $trigger_output = '';
                    foreach( $data_mapping_triggers as $group => $trigger_data ){
                      foreach( $trigger_data as $single_trigger_data ){
                        if( intval( $template->id ) === intval( $single_trigger_data['template'] ) ){
                          $trigger_output .= $single_trigger_data['name'] . ' (' . $single_trigger_data['group'] . ')<br>';
                        }
                      }
                    }

                    echo trim( $trigger_output, '<br>' );
                  }
                ?>
              </td>
              <td class="align-middle wpwh-text-left">
               <?php
                  if( ! empty( $data_mapping_actions ) ){
                    $action_output = '';
                    foreach( $data_mapping_actions as $an => $single_action_data_array ){
                      foreach( $single_action_data_array as $single_action_data ){
                        if( intval( $template->id ) === intval( $single_action_data['template'] ) ){
                          $action_output .= $single_action_data['name'] . '<br>';
                        }
                      }
                    }

                    echo trim( $action_output, '<br>' );
                  }
                ?>
              </td>
              <td class="wpwh-text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button
                    type="button"
                    class="wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon wpwh-dm-delete-template-btn"
                    title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-data-mapping' ); ?>"

                    data-wpwh-mapping-id="<?php echo $template->id; ?>"
                    data-tippy=""
                    data-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-data-mapping' ); ?>"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
                  </button>
                  <button
                    type="button"
                    class="wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon wpwh-dm-view-template-btn"

                    data-wpwh-mapping-id="<?php echo $template->id; ?>"
                    data-tippy=""
                    data-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-data-mapping' ); ?>"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="<?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-data-mapping' ); ?>">
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="2" class="wpwh-text-center"><?php echo WPWHPRO()->helpers->translate( 'You currently don\'t have any data mapping templates available. Please create one first.', 'wpwhpro-page-data-mapping' ); ?></td>
					</tr>
				<?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="wpwh-table-container mt-5">
    <div class="wpwh-table-header d-flex align-items-center justify-content-between">
      <h4 class="mb-0"><?php echo WPWHPRO()->helpers->translate( 'Helpers', 'wpwhpro-page-data-mapping' ); ?></h4>
    </div>

    <table class="wpwh-table wpwh-table--sm">
      <thead>
        <tr>
          <th><?php echo WPWHPRO()->helpers->translate( 'Tag', 'wpwhpro-page-data-mapping' ); ?></th>
          <th><?php echo WPWHPRO()->helpers->translate( 'Used by', 'wpwhpro-page-data-mapping' ); ?></th>
          <th><?php echo WPWHPRO()->helpers->translate( 'Description', 'wpwhpro-page-data-mapping' ); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td scope="row"><strong>{:</strong>key<strong>:}</strong></td>
          <td>
            <?php echo WPWHPRO()->helpers->translate( 'Actions', 'wpwhpro-page-data-mapping' ); ?>, <?php echo WPWHPRO()->helpers->translate( 'Triggers', 'wpwhpro-page-data-mapping' ); ?>
          </td>
          <td>
            <?php echo WPWHPRO()->helpers->translate( 'By defining {:some_key:} within a <strong>Data Value</strong> field, it will be replaced by the content of the given key of the response. You can also use multiple of these tags. Example: you get the key first_name and you want to add it to the following string: "This is my first name: MYNAME",  you can do the following: "This is my first name: {:first_name:}" ', 'wpwhpro-page-data-mapping' ); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<div class="modal fade" id="wpwhCreateDataMappingTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Create Template', 'wpwhpro-page-data-mapping' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="wpwh_data_mapping_add_template_name"><?php echo WPWHPRO()->helpers->translate( 'Template Name', 'wpwhpro-page-data-mapping' ); ?></label>
          <input class="wpwh-form-input w-100" type="text" id="wpwh_data_mapping_add_template_name" name="ironikus-template-name" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Enter template name', 'wpwhpro-page-data-mapping' ); ?>" />
        </div>
        <div class="modal-footer">
          <?php echo WPWHPRO()->helpers->get_nonce_field( $data_mapping_nonce ); ?>
          <input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo WPWHPRO()->helpers->translate( 'Create', 'wpwhpro-page-data-mapping' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Create/Edit Template', 'wpwhpro-page-data-mapping' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="wpwh-form-field">
          <label class="wpwh-form-label" for="wpwh_data_mapping_template_name"><?php echo WPWHPRO()->helpers->translate( 'Template Name', 'wpwhpro-page-data-mapping' ); ?></label>
          <input class="wpwh-form-input w-100" type="text" id="wpwh_data_mapping_template_name" name="ironikus-template-name" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Enter template name', 'wpwhpro-page-data-mapping' ); ?>" value="" readonly />
        </div>
        <div class="wpwh-data-mapping-wrapper">
          <div class="wpwh-data-editor ui-sortable">
          </div>
          <div class="wpwh-data-mapping-actions">
            <button type="button" class="wpwh-btn wpwh-btn--outline-secondary wpwh-btn--sm wpwh-add-row-button-text">Add Row</button>
            <div class="wpwh-data-mapping-imexport">
              <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-success wpwh-dm-import-data">
                <strong>Import</strong>
              </button>
              <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-secondary wpwh-dm-export-data ml-3">
                <strong>Export</strong>
              </button>
              <p class="wpwh-dm-export-data-dialogue" style="display:none !important;"></p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <div class="d-flex align-items-center justify-content-center mt-4">
          <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingPreviewModal" data-backdrop="static" data-keyboard="false">
            <strong><?php echo WPWHPRO()->helpers->translate( 'PREVIEW TEMPLATE', 'wpwhpro-page-data-mapping' ); ?></strong>
          </button>
          <div class="mx-3"><small><strong>OR</strong></small></div>
          <button type="submit" class="wpwh-dm-save-template-btn wpwh-btn wpwh-btn--secondary" data-wpwh-mapping-id="">
            <span><?php echo WPWHPRO()->helpers->translate( 'Save Template', 'wpwhpro-page-data-mapping' ); ?></span>
          </button>
        </div>
        <div class="d-flex align-items-center justify-content-center mt-4">
          <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success mr-3" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModalSettings" data-backdrop="static" data-keyboard="false">
            <strong><?php echo WPWHPRO()->helpers->translate( 'TEMPLATE SETTINGS', 'wpwhpro-page-data-mapping' ); ?></strong>
          </button>
          <button
            type="button"
            class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-dm-delete-template-btn wpwh-text-danger ml-3"
            title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-data-mapping' ); ?>"

            data-wpwh-mapping-id=""
          >
            <strong><?php echo WPWHPRO()->helpers->translate( 'DELETE TEMPLATE', 'wpwhpro-page-data-mapping' ); ?></strong>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModalSettings" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Template Settings', 'wpwhpro-page-data-mapping' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-target="#wpwhDataMappingModal">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <form id="wpwh-data-mapping-template-settings-form" action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body"></div>
        <div class="modal-footer text-center">
          <button type="button" class="wpwh-btn wpwh-btn--secondary" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal" data-backdrop="static" data-keyboard="false">
            <span><?php echo WPWHPRO()->helpers->translate( 'Apply Settings', 'wpwhpro-page-data-mapping' ); ?></span>
          </button>
          <div class="d-flex align-items-center justify-content-center mt-4">
            <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal" data-backdrop="static" data-keyboard="false">
              <strong><?php echo WPWHPRO()->helpers->translate( 'BACK', 'wpwhpro-page-data-mapping' ); ?></strong>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingPreviewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Data Mapping Preview', 'wpwhpro-page-data-mapping' ); ?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-target="#wpwhDataMappingModal">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="wpwh-title-area mb-4">
          <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping_preview' ) ) ) : ?>
            <p class="wpwh-text-small wpwh-content">
            <?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_data_mapping_preview' ), 'admin-settings-license' ); ?>
            </p>
          <?php else : ?>
            <p class="wpwh-text-small wpwh-content">
              <?php echo WPWHPRO()->helpers->translate( 'You can use the preview down below to apply your data mapping template to some given data. This allows you to see instant results for your defined data mapping template. <strong>Please note that the preview uses the currently given data mapping template with all of its unsaved changes.</strong> If you want to check it with the saved changes, simply refresh the page without making changes to the mapping template.', 'wpwhpro-page-data-mapping' ); ?>
              <br>
              <?php echo sprintf( WPWHPRO()->helpers->translate( 'To get started, you can simply include your <strong>JSON-, Query-, or XML-string</strong> down below. <a href="%s" target="_blank">Click here to learn more</a>.', 'wpwhpro-page-data-mapping' ), 'https://wp-webhooks.com/docs/knowledge-base/advanced-data-mapping/' ); ?>
            </p>
          <?php endif; ?>
        </div>

        <div class="wpwh-dm-preview">
          <div class="row wpwh-dm-preview__row">
            <div class="col-md-6 wpwh-dm-preview__input-container">
              <h4 class="mb-3"><?php echo WPWHPRO()->helpers->translate( 'Before Data Mapping', 'wpwhpro-page-data-mapping' ); ?> <small class="text-gray">(Input)</small></h4>
              <textarea id="wpwh-data-mapping-preview-input" class="wpwh-dm-preview__input wpwh-form-input w-100 rounded-sm" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Include your payload here.', 'wpwhpro-page-data-mapping' ); ?>"></textarea>
            </div>
            <div class="col-md-6 wpwh-dm-preview__output-container">
              <h4 class="mb-3"><?php echo WPWHPRO()->helpers->translate( 'After Data Mapping', 'wpwhpro-page-data-mapping' ); ?> <small class="text-gray">(Output)</small></h4>
              <pre id="wpwh-data-mapping-preview-output" class="wpwh-dm-preview__output"></pre>
            </div>
          </div>

          <div class="d-flex align-items-center">
            <a href="#" class="wpwh-btn wpwh-btn--sm wpwh-btn--outline-secondary wpwh-dm-preview__submit-btn" data-mapping-type="trigger"><span><?php echo WPWHPRO()->helpers->translate( 'Apply for outgoing data', 'wpwhpro-page-data-mapping' ); ?></span></a>
            <a href="#" class="wpwh-btn wpwh-btn--sm wpwh-btn--outline-primary wpwh-dm-preview__submit-btn ml-3" data-mapping-type="action"><span><?php echo WPWHPRO()->helpers->translate( 'Apply for incoming data', 'wpwhpro-page-data-mapping' ); ?></span></a>
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <div class="d-flex align-items-center justify-content-center mt-4">
          <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal" data-backdrop="static" data-keyboard="false">
            <strong><?php echo WPWHPRO()->helpers->translate( 'BACK', 'wpwhpro-page-data-mapping' ); ?></strong>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var wpwhDataMappingSettings = <?php echo json_encode( $settings, JSON_HEX_QUOT | JSON_HEX_TAG ); ?>;
</script>

<?php if ( ! empty( $templates ) && false ): ?>
  <?php foreach ( $templates as $template ): ?>
    <div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModal-<?php echo $template->id; ?>" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Create/Edit Template', 'wpwhpro-page-data-mapping' ); ?></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="wpwh-form-field">
              <label class="wpwh-form-label" for="wpwh_data_mapping_template_name-<?php echo $template->id; ?>"><?php echo WPWHPRO()->helpers->translate( 'Template Name', 'wpwhpro-page-data-mapping' ); ?></label>
              <input class="wpwh-form-input w-100" type="text" id="wpwh_data_mapping_template_name-<?php echo $template->id; ?>" name="ironikus-template-name" placeholder="<?php echo WPWHPRO()->helpers->translate( 'Enter template name', 'wpwhpro-page-data-mapping' ); ?>" value="<?php echo $template->name; ?>" readonly />
            </div>
            <div class="wpwh-data-mapping-wrapper">
              <div class="wpwh-data-editor ui-sortable">
                <div class="wpwh-empty ui-sortable-handle">Add a row to get started!</div>
              </div>
              <div class="wpwh-data-mapping-actions">
                <button type="button" class="wpwh-btn wpwh-btn--outline-secondary wpwh-btn--sm wpwh-add-row-button-text">Add Row</button>
                <div class="wpwh-data-mapping-imexport">
                  <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-success wpwh-dm-import-data">
                    <strong>Import</strong>
                  </button>
                  <button type="button" class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 text-secondary wpwh-dm-export-data ml-3">
                    <strong>Export</strong>
                  </button>
                  <p class="wpwh-dm-export-data-dialogue" style="display:none !important;"></p>
                </div>
              </div>
              <div class="wpwh-data-mapping-key-settings d-none">
                <?php
                  echo json_encode( $settings, JSON_HEX_QUOT | JSON_HEX_TAG );
                ?>
              </div>
            </div>
          </div>
          <div class="modal-footer text-center">
            <button type="submit" class="wpwh-dm-save-template-btn wpwh-btn wpwh-btn--secondary" data-wpwh-mapping-id="<?php echo $template->id; ?>">
              <span><?php echo WPWHPRO()->helpers->translate( 'Save Template', 'wpwhpro-page-data-mapping' ); ?></span>
            </button>
            <div class="d-flex align-items-center justify-content-center mt-4">
              <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success mr-3" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModalSettings-<?php echo $template->id; ?>">
                <strong><?php echo WPWHPRO()->helpers->translate( 'TEMPLATE SETTINGS', 'wpwhpro-page-data-mapping' ); ?></strong>
              </button>
              <button
                type="button"
                class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-dm-delete-template-btn wpwh-text-danger ml-3"
                title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-data-mapping' ); ?>"

                data-wpwh-mapping-id="<?php echo $template->id; ?>"
              >
                <strong><?php echo WPWHPRO()->helpers->translate( 'DELETE TEMPLATE', 'wpwhpro-page-data-mapping' ); ?></strong>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade wpwh-mapping-modal" id="wpwhDataMappingModalSettings-<?php echo $template->id; ?>" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Template Settings', 'wpwhpro-page-data-mapping' ); ?></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-tippy data-tippy-placement="left" data-tippy-content="Close without saving">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
          <form id="wpwhDataMappingModalSettingsForm-<?php echo $template->id; ?>" action="<?php echo $clear_form_url; ?>" method="post">
            <div class="modal-body">
              <p><?php echo WPWHPRO()->helpers->translate('Check this settings item to only send over the keys defined within this template (Whitelist) or every key except of the ones in this template. This way, you can prevents unnecessary data to be sent over via the endpoint. To only map a key without modifications, simply define the same key as the new key and assign the same key again. E.g.: user_email -> user_email', 'wpwhpro-fields-data-mapping-settings'); ?></p>
              <label class="wpwh-form-label" for="wpwhpro_data_mapping_whitelist_payload"><?php echo WPWHPRO()->helpers->translate('Whitelist/Blacklist Payload', 'wpwhpro-fields-action-required-settings'); ?></label>
              <select class="wpwh-form-input w-100" id="wpwhpro_data_mapping_whitelist_payload" name="wpwhpro_data_mapping_whitelist_payload">
                <option value="none"><?php echo WPWHPRO()->helpers->translate('Choose..', 'wpwhpro-fields-data-mapping-required-settings'); ?></option>
                <option value="whitelist"><?php echo WPWHPRO()->helpers->translate('Whitelist', 'wpwhpro-fields-data-mapping-required-settings'); ?></option>
                <option value="blacklist"><?php echo WPWHPRO()->helpers->translate('Blacklist', 'wpwhpro-fields-data-mapping-required-settings'); ?></option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="submit" class="wpwh-btn wpwh-btn--secondary w-100">
                <span><?php echo WPWHPRO()->helpers->translate( 'Apply Settings', 'wpwhpro-page-data-mapping' ); ?></span>
              </button>
              <div class="d-flex align-items-center justify-content-center mt-5">
                <button class="wpwh-btn wpwh-btn--link wpwh-btn--sm px-0 wpwh-text-success ml-3" data-dismiss="modal" aria-label="close" data-toggle="modal" data-target="#wpwhDataMappingModal-<?php echo $template->id; ?>">
                  <strong><?php echo WPWHPRO()->helpers->translate( 'BACK', 'wpwhpro-page-data-mapping' ); ?></strong>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>