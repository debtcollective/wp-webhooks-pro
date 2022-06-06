<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_get_group_leaders' ) ) :

	/**
	 * Load the ld_get_group_leaders action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_get_group_leaders {

	public function get_details(){

		$translation_ident = "action-ld_get_group_leaders-content";

			$parameter = array(
				'group_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add the group IDs of the groups you want to fetch the group leaders for. This argument accepts a comma-separated string of group IDs.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a single group id, as well as multiple group IDs, separated by commas:", $translation_ident ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['group_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_get_group_leaders</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The group leaders have been successfully fetched.',
			'data' => 
			array (
			  'leaders' => 
			  array (
				9135 => 
				array (
				  0 => 
				  array (
					'data' => 
					array (
					  'ID' => '73',
					  'user_login' => 'jondoe',
					  'user_pass' => '$P$BoI5l9XXXXXXXXXXBoJFzvkPJ71',
					  'user_nicename' => 'jondoe',
					  'user_email' => 'jondoe@test.com',
					  'user_url' => '',
					  'user_registered' => '2022-05-11 23:04:19',
					  'user_activation_key' => '',
					  'user_status' => '0',
					  'display_name' => 'Jon Doe',
					  'spam' => '0',
					  'deleted' => '0',
					),
					'ID' => 73,
					'caps' => 
					array (
					  'Subscriber' => true,
					  'subscriber' => true,
					  'group_leader' => true,
					),
					'cap_key' => 'wp_capabilities',
					'roles' => 
					array (
					  1 => 'subscriber',
					  2 => 'group_leader',
					),
					'allcaps' => 
					array (
					  'read' => true,
					  'level_0' => true,
					  'read_private_locations' => true,
					  'read_private_events' => true,
					  'manage_resumes' => true,
					  'group_leader' => true,
					  'edit_essays' => true,
					  'edit_others_essays' => true,
					  'publish_essays' => true,
					  'read_essays' => true,
					  'read_private_essays' => true,
					  'delete_essays' => true,
					  'edit_published_essays' => true,
					  'delete_others_essays' => true,
					  'delete_published_essays' => true,
					  'read_assignment' => true,
					  'edit_assignments' => true,
					  'edit_others_assignments' => true,
					  'edit_published_assignments' => true,
					  'delete_others_assignments' => true,
					  'delete_published_assignments' => true,
					  'level_1' => false,
					  'Subscriber' => true,
					  'subscriber' => true,
					),
					'filter' => NULL,
				  ),
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Get group leaders',
			'webhook_slug' => 'ld_get_group_leaders',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>group_ids</strong> argument. Set it to the id you want to fetch the group leaders for. If you want to fetch it for multiple groups, simply comma-separate the group IDs.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>group_name</strong> argument to your name of choice.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'ld_get_group_leaders', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Get group leaders', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'get all group leaders for one or multiple groups', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Get all group leaders for one or multiple groups within Learndash.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'leaders' => array(),
				),
			);

			$group_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'group_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $group_ids ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the group_ids argument.", 'action-ld_get_group_leaders-error' );
				return $return_args;
			}

			$validated_group_ids = array();
			if( is_numeric( $group_ids ) ){
				$validated_group_ids[] = intval( $group_ids );
			} else {
				$validated_group_ids = array_map( "trim", explode( ',', $group_ids ) );
			}

			$leaders = array();

			foreach( $validated_group_ids as $group_id ){

				if( ! isset( $leaders[ $group_id ] ) ){
					$leaders[ $group_id ] = learndash_get_groups_administrators( $group_id );
				}

			}

			if( ! empty( $leaders ) ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The group leaders have been successfully fetched.", 'action-ld_get_group_leaders-success' );
				$return_args['data']['leaders'] = $leaders;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "There was an issue fetching the group leaders.", 'action-ld_get_group_leaders-success' );
				$return_args['data']['leaders'] = $leaders;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.