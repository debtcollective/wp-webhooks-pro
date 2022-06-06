<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_tag' ) ) :

	/**
	 * Load the klickt_add_tag action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_tag {

	public function get_details(){

		$translation_ident = "action-klickt_add_tag-content";

		$parameter = array(
			'username'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Username', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The username of your account to authenticate to KlickTipp.', $translation_ident ),
			),
			'password'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Password', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The password of your account to authenticate to KlickTipp.', $translation_ident ),
			),
			'name'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Tag name', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The name of the tag you want to add within KlickTipp.', $translation_ident ),
			),
			'text'		=> array(
				'label' => WPWHPRO()->helpers->translate( 'Tag text', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'Additional information about the tag.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the request.', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The tag was successfully added.',
			'data' => 
			array (
			  'tag' => '8715238',
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add tag',
			'webhook_slug' => 'klickt_add_tag',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>username</strong> argument. Please set it to the user name of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>password</strong> argument. Please set it to the password of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>name</strong> argument. Please set it to the name of the tag you want to create.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'klickt_add_tag', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Add tag', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add a tag', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add a tag within "KlickTipp".', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'klicktipp',
			'premium'	   	=> true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$username = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'username' );
			$password = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'password' );
			$name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$text = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'text' );

			if( empty( $name ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the name argument.", 'action-klickt_add_tag-error' );
				return $return_args;
            }

			if( empty( $text ) ){
				$text = '';
			}

			$klickt_helpers = WPWHPRO()->integrations->get_helper( 'klicktipp', 'klickt_helpers' );

			$connector = $klickt_helpers->get_klicktipp();

			if( ! empty( $connector ) ){

				$connector->login( $username, $password );

				$tag = $connector->tag_create( $name, $text );

				if( ! empty( $tag ) ){
					$return_args['success'] = true;
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The tag was successfully added.", 'action-klickt_add_tag-success' );
					$return_args['data']['tag'] = $tag;
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while adding the tag.", 'action-klickt_add_tag-success' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while loading the KlickTipp helper.", 'action-klickt_add_tag-success' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.