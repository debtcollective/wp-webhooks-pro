<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Actions_ld_delete_quiz_progress' ) ) :

	/**
	 * Load the ld_delete_quiz_progress action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Actions_ld_delete_quiz_progress {

	public function get_details(){

		$translation_ident = "action-ld_delete_quiz_progress-content";

			$parameter = array(
				'user_id'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user id (or user email) of the user you want to remove the quiz progress for.', $translation_ident ) ),
				'quiz_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add the quiz IDs of the quizzes you want to remove the progress for. This argument accepts a single quiz id, or a comma-separated string of quiz IDs.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a single quiz id, as well as multiple quiz ids, separated by commas:", $translation_ident ); ?>
<pre>124,5741,23</pre>
		<?php
		$parameter['quiz_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ld_delete_quiz_progress</strong> action was fired.", $translation_ident ); ?>
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
			'msg' => 'The quiz progress has been successfully deleted.',
			'data' => 
			array (
			  'user_id' => 73,
			  'quiz_ids' => array(
				  895
			  ),
			  'deleted_progress' => 
			  array (
				895 => 
				array (
				  'success' => true,
				  'user_id' => 73,
				  'quiz_id' => 895,
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Delete quiz progress',
			'webhook_slug' => 'ld_delete_quiz_progress',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user_id</strong> argument. You can either set it to the user id or the user email of which you want to remove the quiz access for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>quiz_ids</strong> argument to one or multiple ids of the quizzes you want to remove access from the given user.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'ld_delete_quiz_progress', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Delete quiz progress', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'delete quiz progress from a user', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Delete quiz progress from a user within Learndash.', $translation_ident ),
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
					'user_id' => 0,
					'quiz_ids' => 0,
					'deleted_progress' => false,
				),
			);

			$user_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$quiz_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'quiz_ids' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) || empty( $quiz_ids ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user_id and quiz_ids arguments.", 'action-ld_delete_quiz_progress-error' );
				return $return_args;
			}

			if( is_numeric( $user_id ) ){
				$user_id = intval( $user_id );
			} elseif( ! empty( $user_id ) && is_email( $user_id ) ) {
				$user_data = get_user_by( 'email', $user_id );
				if( ! empty( $user_data ) && isset( $user_data->ID ) ){
					$user_id = $user_data->ID;
				}
			}

			$deleted_progress = array();
			$user_quizzes_array = array_map( "trim", explode( ',', $quiz_ids ) );
			$user_quizzes = array();
			foreach( $user_quizzes_array as $sugk => $sugv ){
				$user_quizzes[ $sugk ] = intval( $sugv );
			}

			foreach( $user_quizzes as $quiz_id ){

				if( ! is_numeric( $quiz_id ) ){
					continue;
				}

				learndash_delete_quiz_progress( $user_id, $quiz_id );

				$deleted_progress[ $quiz_id ] = array(
					'success' => true,
					'user_id' => $user_id,
					'quiz_id' => $quiz_id,
				);
			}

			if( ! empty( $deleted_progress ) ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The quiz progress has been successfully deleted.", 'action-ld_delete_quiz_progress-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['quiz_ids'] = $user_quizzes;
				$return_args['data']['deleted_progress'] = $deleted_progress;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "No quiz progress has been deleted for the given user within Learndash.", 'action-ld_delete_quiz_progress-success' );
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['quiz_ids'] = $user_quizzes;
				$return_args['data']['deleted_progress'] = $deleted_progress;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.