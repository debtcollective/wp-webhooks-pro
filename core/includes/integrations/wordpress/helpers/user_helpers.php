<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Helpers_user_helpers' ) ) :

	/**
	 * Load the create_user action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Helpers_user_helpers {

		/**
		 * Merge the repeater meta data with the manage_meta_data argument
		 * 
		 * @since 5.2
		 *
		 * @param mixed $manage_meta_data
		 * @param mixed $meta_data
		 * @return array
		 */
		public function merge_repeater_meta_data( $manage_meta_data, $meta_data ){

			if( empty( $manage_meta_data ) ){
				$manage_meta_data = array(
					'update_user_meta' => array()
				);
			} elseif( WPWHPRO()->helpers->is_json( $manage_meta_data ) ) {
				$manage_meta_data = json_decode( $manage_meta_data, true );
				
				if( ! isset( $manage_meta_data['update_user_meta'] ) ){
					$manage_meta_data['update_user_meta'] = array();
				}
			} elseif( is_array( $manage_meta_data ) ){
				if( ! isset( $manage_meta_data['update_user_meta'] ) ){
					$manage_meta_data['update_user_meta'] = array();
				}
			} else {

				//Don't merge anything if we cannot determine the format
				return $manage_meta_data;
			}

			if( WPWHPRO()->helpers->is_json( $meta_data ) ){
				$meta_data = json_decode( $meta_data, true );
			}
			
			if( ! empty( $meta_data ) && is_array( $meta_data ) ){

				$validated_meta_data = array();

				//Prepare format
				foreach( $meta_data as $meta_key => $meta_value ){
					$validated_meta_data[] = array(
						'meta_key' => $meta_key,
						'meta_value' => $meta_value,
					);
				}

				$manage_meta_data['update_user_meta'] = array_merge( $validated_meta_data, $manage_meta_data['update_user_meta'] );
			}

			return $manage_meta_data;
		}

		public function manage_user_meta_data( $user_id, $user_meta_data ){
			$response = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			if( ! empty( $user_meta_data ) ){

				if( WPWHPRO()->helpers->is_json( $user_meta_data ) ){
					$user_meta_data = json_decode( $user_meta_data, true );
				}

				if( is_array( $user_meta_data ) ){
					foreach( $user_meta_data as $function => $meta_data ){
						switch( $function ){
							case 'add_user_meta':
								if( ! isset( $response['data']['add_user_meta'] ) ){
									$response['data']['add_user_meta'] = array();
								}

								foreach( $meta_data as $add_row_key => $add_single_meta_data ){
									if( isset( $add_single_meta_data['meta_key'] ) && isset( $add_single_meta_data['meta_value'] ) ){

										$unique = false;
										if( isset( $add_single_meta_data['unique'] ) ){
											$unique = ( ! empty( $add_single_meta_data['unique'] ) ) ? true : false;
										}

										$add_response = add_user_meta( $user_id, $add_single_meta_data['meta_key'], $add_single_meta_data['meta_value'], $unique );

										$response['data']['add_user_meta'][] = array(
											'meta_key' => $add_single_meta_data['meta_key'],
											'meta_value' => $add_single_meta_data['meta_value'],
											'unique' => $unique,
											'response' => $add_response,
										);
									}
								}
							break;
							case 'update_user_meta':
								if( ! isset( $response['data']['update_user_meta'] ) ){
									$response['data']['update_user_meta'] = array();
								}

								foreach( $meta_data as $add_row_key => $update_single_meta_data ){
									if( isset( $update_single_meta_data['meta_key'] ) && isset( $update_single_meta_data['meta_value'] ) ){

										$prev_value = false;
										if( isset( $update_single_meta_data['prev_value'] ) ){
											$prev_value = $update_single_meta_data['prev_value'];
										}

										$update_response = update_user_meta( $user_id, $update_single_meta_data['meta_key'], $update_single_meta_data['meta_value'], $prev_value );

										$response['data']['update_user_meta'][] = array(
											'meta_key' => $update_single_meta_data['meta_key'],
											'meta_value' => $update_single_meta_data['meta_value'],
											'prev_value' => $prev_value,
											'response' => $update_response,
										);
									}
								}
							break;
							case 'delete_user_meta':
								if( ! isset( $response['data']['delete_user_meta'] ) ){
									$response['data']['delete_user_meta'] = array();
								}

								foreach( $meta_data as $add_row_key => $delete_single_meta_data ){
									if( isset( $delete_single_meta_data['meta_key'] ) ){

										$match_meta_value = '';
										if( isset( $delete_single_meta_data['meta_value'] ) ){
											$match_meta_value = $delete_single_meta_data['meta_value'];
										}

										$delete_response = delete_user_meta( $user_id, $delete_single_meta_data['meta_key'], $match_meta_value );

										$response['data']['delete_user_meta'][] = array(
											'meta_key' => $delete_single_meta_data['meta_key'],
											'meta_value' => $match_meta_value,
											'response' => $delete_response,
										);
									}
								}
							break;
						}
					}

					$response['success'] = true;
					$response['msg'] = WPWHPRO()->helpers->translate( 'The meta data was successfully executed.', 'manage-meta-data' );
				} else {
					$response['msg'] = WPWHPRO()->helpers->translate( 'Could not decode the meta data.', 'manage-meta-data' );
				}
			} else {
				$response['msg'] = WPWHPRO()->helpers->translate( 'No custom user meta given.', 'manage-meta-data' );
			}

			return $response;
		}

		/**
		 * Get's called before a user is created using the webhook above
		 *
		 * OLD VERSION
		 *
		 * @param array $meta - an array of the curretnly available meta data
		 * @param object $user - the user object
		 * @return array $meta - the metadata
		 */
		public function create_update_user_add_user_meta( $meta, $user ){

			$response_body = WPWHPRO()->http->get_current_request();

			$user_meta = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_meta' );

			// Manage user meta
			if( ! empty( $user_meta ) ){

				if( WPWHPRO()->helpers->is_json( $user_meta ) ){

					$user_meta_data = json_decode( $user_meta, true );
					foreach( $user_meta_data as $skey => $sval ){

						if( ! empty( $skey ) ){
							if( $sval == 'ironikus-delete' ){

								delete_user_meta( $user->data->ID, $skey );

							} else {

								$ident = 'ironikus-serialize';
								if( is_string( $sval ) && substr( $sval , 0, strlen( $ident ) ) === $ident ){
									$serialized_value = trim( str_replace( $ident, '', $sval ),' ' );

									//Allow array validation
									$sa_ident = '-array';
									if( is_string( $serialized_value ) && substr( $serialized_value , 0, strlen( $sa_ident ) ) === $sa_ident ){
										$serialized_value = trim( str_replace( $sa_ident, '', $serialized_value ),' ' );

										if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
											$serialized_value = json_decode( $serialized_value, true );
										}
									} else {
										if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
											$serialized_value = json_decode( $serialized_value );
										}
									}

									$meta[ $skey ] = $serialized_value;

								} else {
									$meta[ $skey ] = maybe_unserialize( $sval );
								}
							}
						}
					}

				} else {

					$user_meta_data = explode( ';', trim( $user_meta, ';' ) );
					foreach( $user_meta_data as $single_meta ){
						$single_meta_data   = explode( ',', $single_meta );
						$meta_key           = sanitize_text_field( $single_meta_data[0] );
						$meta_value         = $single_meta_data[1];

						if( ! empty( $meta_key ) ){
							if( $meta_value == 'ironikus-delete' ){
								delete_user_meta( $user->data->ID, $meta_key );
							} else {

								$ident = 'ironikus-serialize';
								if( substr( $meta_value , 0, strlen( $ident ) ) === $ident ){
									$serialized_value = trim( str_replace( $ident, '', $meta_value ),' ' );

									//Allow array validation
									$sa_ident = '-array';
									if( is_string( $serialized_value ) && substr( $serialized_value , 0, strlen( $sa_ident ) ) === $sa_ident ){
										$serialized_value = trim( str_replace( $sa_ident, '', $serialized_value ),' ' );

										if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
											$serialized_value = json_decode( $serialized_value, true );
										}
									} else {
										if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
											$serialized_value = json_decode( $serialized_value );
										}
									}

									$meta[ $meta_key ] = $serialized_value;

								} else {
									$meta[ $meta_key ] = maybe_unserialize( $meta_value );
								}

							}
						}
					}

				}

			}

			return $meta;

		}

	}

endif; // End if class_exists check.