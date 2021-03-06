<?php

$templates = WPWHPRO()->auth->get_auth_templates();
$auth_methods = WPWHPRO()->settings->get_authentication_methods();
$webhook_actions = WPWHPRO()->webhook->get_hooks( 'action' );
$webhook_triggers = WPWHPRO()->webhook->get_hooks( 'trigger' );
$authentication_nonce = WPWHPRO()->settings->get_authentication_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );

if( isset( $_POST['wpwh-authentication-name'] ) && isset( $_POST['wpwh-authentication-type'] ) ){
  if ( check_admin_referer( $authentication_nonce['action'], $authentication_nonce['arg'] ) ) {

    if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-authentication-add-template' ), 'wpwhpro-page-authentication-add-template' ) ){
      $auth_template = isset( $_POST['wpwh-authentication-name'] ) ? sanitize_title( $_POST['wpwh-authentication-name'] ) : '';
      $auth_type = isset( $_POST['wpwh-authentication-type'] ) ? sanitize_title( $_POST['wpwh-authentication-type'] ) : '';

      if( ! empty( $auth_template ) && ! empty( $auth_type ) ){
        $check = WPWHPRO()->auth->add_template( $auth_template, $auth_type );

          if( ! empty( $check ) ){
            $templates = WPWHPRO()->auth->get_auth_templates( 'all', false );
          }

      }
    }

  }
}

$authentication_triggers = array();
foreach( $webhook_triggers as $trigger_group => $wt ){
  foreach( $wt as $st => $sd ){
    if( isset( $sd['settings'] ) ){

      if( isset( $sd['settings']['wpwhpro_trigger_authentication'] ) ){
        if( ! isset( $authentication_triggers[ $trigger_group ] ) ){
          $authentication_triggers[ $trigger_group ] = array();
        }

        $authentication_triggers[ $trigger_group ][ $st ] = array(
          'name' => sanitize_title( $st ),
          'group' => sanitize_title( $trigger_group ),
          'template' => $sd['settings']['wpwhpro_trigger_authentication'],
        );
      }

    }
  }
}

$authentication_actions = array();
foreach( $webhook_actions as $action_name => $wa ){

  if( ! isset( $wa['api_key'] ) || ! is_string( $wa['api_key'] ) ){
    foreach( $wa as $action_slug => $action_data ){
      if( isset( $action_data['settings'] ) ){

        if( isset( $action_data['settings']['wpwhpro_action_authentication'] ) ){
          $authentication_actions[ $action_slug ] = array(
            'name' => sanitize_title( $action_slug ),
            'template' => $action_data['settings']['wpwhpro_action_authentication'],
          );
        }

      }
    }
  }

}

?>
<?php add_ThickBox(); ?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
    <h1><?php echo WPWHPRO()->helpers->translate( 'Authentication', 'wpwhpro-page-authentication' ); ?></h1>
    <p>
      <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_authentication' ) ) ) : ?>
        <?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_authentication' ), 'admin-settings-license' ); ?>
      <?php else : ?>
        <?php echo sprintf(WPWHPRO()->helpers->translate( 'Create your own authentication template down below. This allows you to authenticate your outgoing "Send Data" webhook triggers to a given endpoint, as well as your incoming "Receive Data" actions. For more information, please check out the authentication documentation by clicking <a class="text-secondary" title="Visit our documentation" href="%s" target="_blank" >here</a>.', 'wpwhpro-page-authentication' ), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-authentication/'); ?>
      <?php endif; ?>
    </p>
  </div>

  <div class="wpwh-table-container">
    <div class="wpwh-table-header d-flex align-items-center justify-content-between">
      <h2><?php echo WPWHPRO()->helpers->translate( 'Templates', 'wpwhpro-page-authentication' ); ?></h2>
      <a href="#" class="wpwh-btn wpwh-btn--secondary" data-toggle="modal" data-target="#addAuthTemplateModal"><?php echo WPWHPRO()->helpers->translate( 'Create Template', 'wpwhpro-page-authentication' ); ?></a>
    </div>
    <table class="wpwh-table wpwh-table--sm">
      <thead>
        <tr>
          <th class="w-10"><?php echo WPWHPRO()->helpers->translate( 'Id', 'wpwhpro-page-authentication' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Name', 'wpwhpro-page-authentication' ); ?></th>
          <th class="w-10"><?php echo WPWHPRO()->helpers->translate( 'Auth Type', 'wpwhpro-page-authentication' ); ?></th>
          <th><?php echo WPWHPRO()->helpers->translate( 'Create', 'wpwhpro-page-authentication' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Connected Triggers', 'wpwhpro-page-authentication' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Connected Actions', 'wpwhpro-page-authentication' ); ?></th>
          <th class="text-center w-10">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if( ! empty( $templates ) ) : ?>
          <?php foreach( $templates as $template ) :

            $template_id = intval( $template->id );

            $auth_name = $template->auth_type;
            if( is_array( $auth_methods ) && isset( $auth_methods[ $template->auth_type ] ) && isset( $auth_methods[ $template->auth_type ]['name'] ) ){
              $auth_name = $auth_methods[ $template->auth_type ]['name'];
            }

          ?>
            <tr>
              <td><?php echo $template_id; ?></td>
              <td><?php echo $template->name; ?></td>
              <td><?php echo $auth_name; ?></td>
              <td><?php echo date( 'F j, Y, g:i a', strtotime( $template->log_time ) ); ?></td>
              <td class="align-middle wpwh-text-left">
                <?php
                  if( ! empty( $authentication_triggers ) ){
                    $trigger_output = '';
                    foreach( $authentication_triggers as $group => $trigger_data ){
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
                  if( ! empty( $authentication_actions ) ){
                    $action_output = '';
                    foreach( $authentication_actions as $single_action_data ){
                      if( intval( $template_id ) === intval( $single_action_data['template'] ) ){
                        $action_output .= $single_action_data['name'] . '<br>';
                      }
                    }

                    echo trim( $action_output, '<br>' );
                  }
                ?>
              </td>
              <td class="align-middle text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button
                    type="button"
                    class="wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon wpwh-edit-auth-template"

                    data-wpwh-auth-id="<?php echo $template_id; ?>"
                    data-wpwh-template-name="<?php echo $template->name; ?>"
                    data-modal-id="#editAuthTemplateModal"
                    data-tippy=""
                    data-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-logs' ); ?>"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="<?php echo WPWHPRO()->helpers->translate( 'Settings', 'wpwhpro-page-logs' ); ?>">
                  </button>
                  <button
                    type="button"
                    class="wpwh-btn wpwh-btn--link px-2 py-1 wpwh-btn--icon wpwh-delete-auth-template"
                    title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-logs' ); ?>"

                    data-wpwh-auth-id="<?php echo $template_id; ?>"
                    data-tippy=""
                    data-tippy-content="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-logs' ); ?>"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td><?php echo WPWHPRO()->helpers->translate( 'No template available. Create one first.', 'wpwhpro-page-authentication' ); ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<div class="modal fade" id="addAuthTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Create Auth Template', 'wpwhpro-page-authentication' ); ?></h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="wpwh-authentication-name"><?php echo WPWHPRO()->helpers->translate( 'Template Name', 'wpwhpro-page-authentication' ); ?></label>
					<input class="wpwh-form-input w-100" type="text" id="wpwh-authentication-name" name="wpwh-authentication-name" placeholder="<?php echo WPWHPRO()->helpers->translate( 'demo-template', 'wpwhpro-page-authentication' ); ?>" />

          <label class="wpwh-form-label mt-4" for="wpwh-authentication-type"><?php echo WPWHPRO()->helpers->translate( 'Auth Type', 'wpwhpro-page-authentication' ); ?></label>
          <select class="wpwh-form-input w-100" id="wpwh-authentication-type" name="wpwh-authentication-type">
            <?php foreach( $auth_methods as $auth_type => $auth_data ) : ?>
              <option value="<?php echo $auth_type; ?>"><?php echo $auth_data['name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="modal-footer">
          <?php echo WPWHPRO()->helpers->get_nonce_field( $authentication_nonce ); ?>
					<input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo WPWHPRO()->helpers->translate( 'Create', 'wpwhpro-page-authentication' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade modal--lg" id="editAuthTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Edit Template', 'wpwhpro-page-authentication' ); ?>: <span></span></h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <div class="modal-body" id="wpwh-authentication-content-wrapper">
      </div>
      <div class="modal-footer text-center">
        <?php echo WPWHPRO()->helpers->get_nonce_field( $authentication_nonce ); ?>
        <button type="button" id="wpwh-save-auth-template-button" class="wpwh-btn wpwh-btn--secondary" data-wpwh-auth-id="1">
          <span><?php echo WPWHPRO()->helpers->translate( 'Save Template', 'wpwhpro-page-authentication' ); ?></span>
        </button>
      </div>
    </div>
  </div>
</div>