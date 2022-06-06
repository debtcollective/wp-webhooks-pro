<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_wc_create_coupon' ) ) :

	/**
	 * Load the wc_create_coupon action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_wc_create_coupon {

	public function get_details(){

		$translation_ident = "action-wc_create_coupon-content";

			$parameter = array(
				'coupon_code' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The code that your customers can use during checkout.', $translation_ident ) ),
				'type'	=> array( 
					'short_description' => WPWHPRO()->helpers->translate( 'The coupon type.', $translation_ident ),
					'type' => 'select',
					'multiple' => false,
					'choices' => ( function_exists( 'wc_get_coupon_types' ) ) ? wc_get_coupon_types() : array(),
				),
				'amount'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The discount amount of the coupon code, based on the selected type.', $translation_ident ) ),
				'individual_use'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this to yes if the coupon cannot be used in conjunction with other coupons. Default: no', $translation_ident ) ),
				'product_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add IDs of products in case you want to apply the coupon code only to specific products. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'exclude_product_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add IDs of products that you want to exclude from the coupon code. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'usage_limit'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set a number if you want to limit the coupon to be used a limited amount of times only. Default: 0 (unlimited)', $translation_ident ) ),
				'usage_limit_per_user'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set a number to define whether a user can use the coupon code multiple times. Default: 0 (unlimited)', $translation_ident ) ),
				'limit_usage_to_x_items'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Define a maximum number of products this coupon can be applied to for each cart. Default: 0 (unlimited)', $translation_ident ) ),
				'usage_count'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A number that shows how often the code has been already used.', $translation_ident ) ),
				'expiry_date'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A date when the coupon code expires. Leave empty to never expire it.', $translation_ident ) ),
				'enable_free_shipping'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this to yes to enable free shipping. Default: no', $translation_ident ) ),
				'product_category_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add the category IDs you want to add to the coupon. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'exclude_product_category_ids'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add the category IDs you want to exclude to the coupon. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'exclude_sale_items'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this to yes to prevent the coupon being applied to items on sale. Default: no', $translation_ident ) ),
				'minimum_amount'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set a minimum amount that need to be reached before the coupon can be applied.', $translation_ident ) ),
				'maximum_amount'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set a maximum amount of which the coupon can be applied to.', $translation_ident ) ),
				'coupon_emails'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add one or multiple emails to the coupon. Only the users with those emails can redeem the code. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'description'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Add a coupon author. This field accepts a user id.', $translation_ident ) ),
				'replace'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this to yes to replace the existing user_ids. If set to no, the user_ids are appended to the existing ones. Default: no', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple emails via user IDs to the coupon, you can either comma-separate them like <code>33,421</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  423,
  532,
  44
}</pre>
		<?php
		$parameter['type']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to allow the coupon to be applied only on specific products, you can either comma-separate them like <code>33,421</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  423,
  532,
  44
}</pre>
		<?php
		$parameter['product_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to exclude specific products from the coupon, you can either comma-separate them like <code>33,421</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  423,
  532,
  44
}</pre>
		<?php
		$parameter['exclude_product_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple product categories via category IDs to the coupon, you can either comma-separate them like <code>33,421</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  423,
  532,
  44
}</pre>
		<?php
		$parameter['product_category_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple excluded product categories via category IDs to the coupon, you can either comma-separate them like <code>33,421</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  423,
  532,
  44
}</pre>
		<?php
		$parameter['exclude_product_category_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple emails to the coupon, you can either comma-separate them like <code>jon@doe.test,test@email.test</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  jon@doe.test,
  test@email.test
}</pre>
		<?php
		$parameter['coupon_emails']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wc_create_coupon</strong> action was fired.", $translation_ident ); ?>
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
			'msg' => 'The coupon has been successfully added.',
			'data' => 
			array (
			  'coupon_id' => 9139,
			  'coupon_data' => 
			  array (
				'type' => 'fixed_cart',
				'amount' => '10',
				'individual_use' => true,
				'product_ids' => 
				array (
				  0 => '658',
				  1 => '659',
				),
				'exclude_product_ids' => 
				array (
				  0 => 155,
				),
				'usage_limit' => '10',
				'usage_limit_per_user' => '1',
				'limit_usage_to_x_items' => '2',
				'usage_count' => '1',
				'expiry_date' => '2022-11-22 10:15:00',
				'enable_free_shipping' => true,
				'product_category_ids' => 
				array (
				  0 => 79,
				),
				'exclude_product_category_ids' => 
				array (
				  0 => 80,
				),
				'exclude_sale_items' => true,
				'minimum_amount' => '3',
				'maximum_amount' => '300',
				'customer_emails' => 
				array (
				  0 => 'demo@test.test',
				),
				'description' => 'This is a demo description',
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Create coupon',
			'webhook_slug' => 'wc_create_coupon',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>coupon_code</strong> argument. Please set it to the coupon code you want your customers to enter during checkout.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'wc_create_coupon', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Create coupon', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'create a coupon', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Create a coupon within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'coupon_id' => 0,
					'coupon_data' => array(),
				)
			);

			$coupon_code = wc_format_coupon_code( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'coupon_code' ) );
			$type = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'type' );
			$amount = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'amount' );
			$individual_use = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'individual_use' ) === 'yes' ) ? true : false;
			$product_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_ids' );
			$exclude_product_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'exclude_product_ids' );
			$usage_limit = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'usage_limit' );
			$usage_limit_per_user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'usage_limit_per_user' );
			$limit_usage_to_x_items = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'limit_usage_to_x_items' );
			$usage_count = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'usage_count' );
			$expiry_date = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiry_date' );
			$enable_free_shipping = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'enable_free_shipping' ) === 'yes' ) ? true : false;
			$product_category_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_category_ids' );
			$exclude_product_category_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'exclude_product_category_ids' );
			$exclude_sale_items = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'exclude_sale_items' ) === 'yes' ) ? true : false;
			$minimum_amount = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'minimum_amount' );
			$maximum_amount = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'maximum_amount' );
			$coupon_emails = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'coupon_emails' );
			$description = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$coupon_author = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'coupon_author' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $coupon_code ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the coupon_code argument.", 'action-wc_create_coupon-error' );
				return $return_args;
			}

			$id_from_code = wc_get_coupon_id_by_code( $coupon_code );

			if( $id_from_code ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The coupon code already exists", 'action-wc_create_coupon-error' );
				$return_args['coupon_id'] = $id_from_code;
				return $return_args;
			}

			$data = array();

			if( ! empty( $type ) ){
				$data['type'] = $type;
			}

			if( ! empty( $amount ) ){
				$data['amount'] = $amount;
			}

			if( ! empty( $individual_use ) ){
				$data['individual_use'] = $individual_use;
			}

			if( ! empty( $product_ids ) ){
				
				$validated_product_ids = array();
				if( WPWHPRO()->helpers->is_json( $product_ids ) ){
					$validated_product_ids = json_decode( $product_ids, true );
				} elseif( is_array( $product_ids ) || is_object( $product_ids ) ) {
					$validated_product_ids = json_decode( json_encode( $product_ids ), true );
				} else {
					$validated_product_ids = explode( ',', $product_ids );
				}
	
				if( ! is_array( $validated_product_ids ) && ! empty( $validated_product_ids ) ){
					$validated_product_ids = array( $validated_product_ids );
				}

				$data['product_ids'] = $validated_product_ids;
			}

			if( ! empty( $exclude_product_ids ) ){
				
				$validated_excluded_product_ids = array();
				if( WPWHPRO()->helpers->is_json( $exclude_product_ids ) ){
					$validated_excluded_product_ids = json_decode( $exclude_product_ids, true );
				} elseif( is_array( $exclude_product_ids ) || is_object( $exclude_product_ids ) ) {
					$validated_excluded_product_ids = json_decode( json_encode( $exclude_product_ids ), true );
				} else {
					$validated_excluded_product_ids = explode( ',', $exclude_product_ids );
				}
	
				if( ! is_array( $validated_excluded_product_ids ) && ! empty( $validated_excluded_product_ids ) ){
					$validated_excluded_product_ids = array( $validated_excluded_product_ids );
				}

				$data['exclude_product_ids'] = $validated_excluded_product_ids;
			}

			if( ! empty( $usage_limit ) ){
				$data['usage_limit'] = $usage_limit;
			}

			if( ! empty( $usage_limit_per_user ) ){
				$data['usage_limit_per_user'] = $usage_limit_per_user;
			}

			if( ! empty( $limit_usage_to_x_items ) ){
				$data['limit_usage_to_x_items'] = $limit_usage_to_x_items;
			}

			if( ! empty( $usage_count ) ){
				$data['usage_count'] = $usage_count;
			}

			if( ! empty( $expiry_date ) ){
				$data['expiry_date'] = WPWHPRO()->helpers->get_formatted_date( $expiry_date );
			}

			if( ! empty( $enable_free_shipping ) ){
				$data['enable_free_shipping'] = $enable_free_shipping;
			}

			if( ! empty( $product_category_ids ) ){
				
				$validated_product_category_ids = array();
				if( WPWHPRO()->helpers->is_json( $product_category_ids ) ){
					$validated_product_category_ids = json_decode( $product_category_ids, true );
				} elseif( is_array( $product_category_ids ) || is_object( $product_category_ids ) ) {
					$validated_product_category_ids = json_decode( json_encode( $product_category_ids ), true );
				} else {
					$validated_product_category_ids = explode( ',', $product_category_ids );
				}
	
				if( ! is_array( $validated_product_category_ids ) && ! empty( $validated_product_category_ids ) ){
					$validated_product_category_ids = array( $validated_product_category_ids );
				}

				$data['product_category_ids'] = $validated_product_category_ids;
			}

			if( ! empty( $exclude_product_category_ids ) ){
				
				$validated_exclude_product_category_ids = array();
				if( WPWHPRO()->helpers->is_json( $exclude_product_category_ids ) ){
					$validated_exclude_product_category_ids = json_decode( $exclude_product_category_ids, true );
				} elseif( is_array( $exclude_product_category_ids ) || is_object( $exclude_product_category_ids ) ) {
					$validated_exclude_product_category_ids = json_decode( json_encode( $exclude_product_category_ids ), true );
				} else {
					$validated_exclude_product_category_ids = explode( ',', $exclude_product_category_ids );
				}
	
				if( ! is_array( $validated_exclude_product_category_ids ) && ! empty( $validated_exclude_product_category_ids ) ){
					$validated_exclude_product_category_ids = array( $validated_exclude_product_category_ids );
				}

				$data['exclude_product_category_ids'] = $validated_exclude_product_category_ids;
			}

			if( ! empty( $exclude_sale_items ) ){
				$data['exclude_sale_items'] = $exclude_sale_items;
			}

			if( ! empty( $minimum_amount ) ){
				$data['minimum_amount'] = $minimum_amount;
			}

			if( ! empty( $maximum_amount ) ){
				$data['maximum_amount'] = $maximum_amount;
			}

			if( ! empty( $coupon_emails ) ){
				$validated_emails = array();
				if( WPWHPRO()->helpers->is_json( $coupon_emails ) ){
					$validated_emails = json_decode( $coupon_emails, true );
				} elseif( is_array( $coupon_emails ) || is_object( $coupon_emails ) ) {
					$validated_emails = json_decode( json_encode( $coupon_emails ), true );
				} else {
					$validated_emails = explode( ',', $coupon_emails );
				}
	
				if( ! is_array( $validated_emails ) && ! empty( $validated_emails ) ){
					$validated_emails = array( $validated_emails );
				}
	
				$asterisk_replacement = "wpwhasteriskreplacement";
				foreach( $validated_emails as $ek => $ev ){
	
					$temp_email = str_replace( '*', $asterisk_replacement, $ev );
	
					if( is_email( $temp_email ) ){
						$validated_emails[ $ek ] = str_replace( $asterisk_replacement, '*', sanitize_email( $ev ) );
					} else {
						unset( $validated_emails[ $ek ] );
					}
				}
	
				$data['customer_emails'] = $validated_emails;
			}

			if( ! empty( $description ) ){
				$data['description'] = $description;
			}

			$defaults = array(
				'type'                         => 'fixed_cart',
				'amount'                       => 0,
				'individual_use'               => false,
				'product_ids'                  => array(),
				'exclude_product_ids'          => array(),
				'usage_limit'                  => '',
				'usage_limit_per_user'         => '',
				'limit_usage_to_x_items'       => '',
				'usage_count'                  => '',
				'expiry_date'                  => '',
				'enable_free_shipping'         => false,
				'product_category_ids'         => array(),
				'exclude_product_category_ids' => array(),
				'exclude_sale_items'           => false,
				'minimum_amount'               => '',
				'maximum_amount'               => '',
				'customer_emails'              => array(),
				'description'                  => '',
			);

			$coupon_data = wp_parse_args( $data, $defaults );

			// Validate coupon types
			if ( ! in_array( wc_clean( $coupon_data['type'] ), array_keys( wc_get_coupon_types() ) ) ) {
				$return_args['msg'] = sprintf( WPWHPRO()->helpers->translate( 'Invalid coupon type - the coupon type must be any of these: %s', 'action-wc_create_coupon-error' ), implode( ', ', array_keys( wc_get_coupon_types() ) ) );
				return $return_args;
			}

			$new_coupon = array(
				'post_title'   => $coupon_code,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'shop_coupon',
				'post_excerpt' => $coupon_data['description'],
	 		);

			 if( ! empty( $coupon_author ) ){
				 $new_coupon['post_author'] = $coupon_author;
			 }

			$coupon_id = wp_insert_post( $new_coupon, true );

			if ( is_wp_error( $coupon_id ) ) {
				$return_args['msg'] = $coupon_id->get_error_message();
				return $return_args;
			}
			
			if( $coupon_id ){

				// Set coupon meta
				update_post_meta( $coupon_id, 'discount_type', $coupon_data['type'] );
				update_post_meta( $coupon_id, 'coupon_amount', wc_format_decimal( $coupon_data['amount'] ) );
				update_post_meta( $coupon_id, 'individual_use', ( true === $coupon_data['individual_use'] ) ? 'yes' : 'no' );
				update_post_meta( $coupon_id, 'product_ids', implode( ',', array_filter( array_map( 'intval', $coupon_data['product_ids'] ) ) ) );
				update_post_meta( $coupon_id, 'exclude_product_ids', implode( ',', array_filter( array_map( 'intval', $coupon_data['exclude_product_ids'] ) ) ) );
				update_post_meta( $coupon_id, 'usage_limit', absint( $coupon_data['usage_limit'] ) );
				update_post_meta( $coupon_id, 'usage_limit_per_user', absint( $coupon_data['usage_limit_per_user'] ) );
				update_post_meta( $coupon_id, 'limit_usage_to_x_items', absint( $coupon_data['limit_usage_to_x_items'] ) );
				update_post_meta( $coupon_id, 'usage_count', absint( $coupon_data['usage_count'] ) );
				update_post_meta( $coupon_id, 'expiry_date', $coupon_data['expiry_date'] );
				update_post_meta( $coupon_id, 'date_expires', $coupon_data['expiry_date'] );
				update_post_meta( $coupon_id, 'free_shipping', ( true === $coupon_data['enable_free_shipping'] ) ? 'yes' : 'no' );
				update_post_meta( $coupon_id, 'product_categories', array_filter( array_map( 'intval', $coupon_data['product_category_ids'] ) ) );
				update_post_meta( $coupon_id, 'exclude_product_categories', array_filter( array_map( 'intval', $coupon_data['exclude_product_category_ids'] ) ) );
				update_post_meta( $coupon_id, 'exclude_sale_items', ( true === $coupon_data['exclude_sale_items'] ) ? 'yes' : 'no' );
				update_post_meta( $coupon_id, 'minimum_amount', wc_format_decimal( $coupon_data['minimum_amount'] ) );
				update_post_meta( $coupon_id, 'maximum_amount', wc_format_decimal( $coupon_data['maximum_amount'] ) );
				update_post_meta( $coupon_id, 'customer_email', array_filter( array_map( 'sanitize_email', $coupon_data['customer_emails'] ) ) );

				do_action( 'woocommerce_new_coupon', $coupon_id );

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The coupon has been successfully added.", 'action-wc_create_coupon-success' );
				$return_args['data']['coupon_id'] = $coupon_id;
				$return_args['data']['coupon_data'] = $coupon_data;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while creating the coupon.", 'action-wc_create_coupon-success' );
				$return_args['data']['coupon_data'] = $coupon_data;
			}
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.