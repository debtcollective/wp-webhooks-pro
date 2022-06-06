<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Helpers_post_helpers' ) ) :

	/**
	 * Load the create_post action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Helpers_post_helpers {

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
					'update_post_meta' => array()
				);
			} elseif( WPWHPRO()->helpers->is_json( $manage_meta_data ) ) {
				$manage_meta_data = json_decode( $manage_meta_data, true );
				
				if( ! isset( $manage_meta_data['update_post_meta'] ) ){
					$manage_meta_data['update_post_meta'] = array();
				}
			} elseif( is_array( $manage_meta_data ) ){
				if( ! isset( $manage_meta_data['update_post_meta'] ) ){
					$manage_meta_data['update_post_meta'] = array();
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

				$manage_meta_data['update_post_meta'] = array_merge( $validated_meta_data, $manage_meta_data['update_post_meta'] );
			}

			return $manage_meta_data;
		}

		public function manage_post_meta_data( $post_id, $post_meta_data ){
			$response = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			if( ! empty( $post_meta_data ) ){

				if( WPWHPRO()->helpers->is_json( $post_meta_data ) ){
					$post_meta_data = json_decode( $post_meta_data, true );
				}

				if( is_array( $post_meta_data ) ){
					foreach( $post_meta_data as $function => $meta_data ){
						switch( $function ){
							case 'add_post_meta':
								if( ! isset( $response['data']['add_post_meta'] ) ){
									$response['data']['add_post_meta'] = array();
								}

								foreach( $meta_data as $add_row_key => $add_single_meta_data ){
									if( isset( $add_single_meta_data['meta_key'] ) && isset( $add_single_meta_data['meta_value'] ) ){

										$unique = false;
										if( isset( $add_single_meta_data['unique'] ) ){
											$unique = ( ! empty( $add_single_meta_data['unique'] ) ) ? true : false;
										}

										$add_response = add_post_meta( $post_id, $add_single_meta_data['meta_key'], $add_single_meta_data['meta_value'], $unique );

										$response['data']['add_post_meta'][] = array(
											'meta_key' => $add_single_meta_data['meta_key'],
											'meta_value' => $add_single_meta_data['meta_value'],
											'unique' => $unique,
											'response' => $add_response,
										);
									}
								}
							break;
							case 'update_post_meta':
								if( ! isset( $response['data']['update_post_meta'] ) ){
									$response['data']['update_post_meta'] = array();
								}

								foreach( $meta_data as $add_row_key => $update_single_meta_data ){
									if( isset( $update_single_meta_data['meta_key'] ) && isset( $update_single_meta_data['meta_value'] ) ){

										$prev_value = false;
										if( isset( $update_single_meta_data['prev_value'] ) ){
											$prev_value = $update_single_meta_data['prev_value'];
										}

										$update_response = update_post_meta( $post_id, $update_single_meta_data['meta_key'], $update_single_meta_data['meta_value'], $prev_value );

										$response['data']['update_post_meta'][] = array(
											'meta_key' => $update_single_meta_data['meta_key'],
											'meta_value' => $update_single_meta_data['meta_value'],
											'prev_value' => $prev_value,
											'response' => $update_response,
										);
									}
								}
							break;
							case 'delete_post_meta':
								if( ! isset( $response['data']['delete_post_meta'] ) ){
									$response['data']['delete_post_meta'] = array();
								}

								foreach( $meta_data as $add_row_key => $delete_single_meta_data ){
									if( isset( $delete_single_meta_data['meta_key'] ) ){

										$match_meta_value = '';
										if( isset( $delete_single_meta_data['meta_value'] ) ){
											$match_meta_value = $delete_single_meta_data['meta_value'];
										}

										$delete_response = delete_post_meta( $post_id, $delete_single_meta_data['meta_key'], $match_meta_value );

										$response['data']['delete_post_meta'][] = array(
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
				$response['msg'] = WPWHPRO()->helpers->translate( 'No custom post meta given.', 'manage-meta-data' );
			}

			return $response;
		}

		/**
		 * Update the post meta
		 *
		 * @param int $post_id - the post id
		 * @return void
		 */
		public function create_update_post_add_meta( $post_id ){

			$response_body = WPWHPRO()->http->get_current_request();

			$meta_input = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_input' );

			if( ! empty( $meta_input ) ){

				if( WPWHPRO()->helpers->is_json( $meta_input ) ){

					$post_meta_data = json_decode( $meta_input, true );
					foreach( $post_meta_data as $skey => $svalue ){
						if( ! empty( $skey ) ){
							if( $svalue == 'ironikus-delete' ){
								delete_post_meta( $post_id, $skey );
							} else {

								$ident = 'ironikus-serialize';
								if( is_string( $svalue ) && substr( $svalue , 0, strlen( $ident ) ) === $ident ){
									$serialized_value = trim( str_replace( $ident, '', $svalue ),' ' );

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

									update_post_meta( $post_id, $skey, $serialized_value );

								} else {
									update_post_meta( $post_id, $skey, maybe_unserialize( $svalue ) );
								}

							}
						}
					}

				} else {

					$post_meta_data = explode( ';', trim( $meta_input, ';' ) );
					foreach( $post_meta_data as $single_meta ){
						$single_meta_data   = explode( ',', $single_meta );
						$meta_key           = sanitize_text_field( $single_meta_data[0] );
						$meta_value         = $single_meta_data[1];

						if( ! empty( $meta_key ) ){
							if( $meta_value == 'ironikus-delete' ){
								delete_post_meta( $post_id, $meta_key );
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

									update_post_meta( $post_id, $meta_key, $serialized_value );

								} else {
									update_post_meta( $post_id, $meta_key, maybe_unserialize( $meta_value ) );
								}
							}
						}
					}

				}

			}

		}

    }

endif; // End if class_exists check.