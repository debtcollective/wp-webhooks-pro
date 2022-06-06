<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wpwh_shortcode' ) ) :

	/**
	 * Load the wpwh_shortcode trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wpwh_shortcode {

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function get_callbacks(){

            return array(
                array(
                    'type' => 'shortcode',
                    'hook' => 'wpwh_shortcode',
                    'callback' => array( $this, 'ironikus_trigger_wpwh_shortcode' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => false,
                ),
            );

		}

        /*
        * Register the user login trigger as an element
        */
        public function get_details(){

            $translation_ident = "trigger-wpwh_shortcode-description";

            $parameter = array(
                'custom_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'Your custom data construct build out of the shortcode arguments, as well as the data mapping.', $translation_ident ) ),
            );

            ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "The trigger will be fired whenever the following shortcode is called: <code>[wpwh_shortcode]</code>", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "While the shortcode itself does not do much except of firing the trigger, you might want to add some data to it. To do that, you have two different ways of doing so:", $translation_ident ); ?>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "You can add the data using the data mapping feature ba assigning a data mapping template to your webhook URL.", $translation_ident ); ?></li>
    <li>
        <?php echo WPWHPRO()->helpers->translate( "You can also add the data using the shortcode parameters. E.g. <code>[wpwh_shortcode param=\"some value\"]</code>", $translation_ident ); ?>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "While <strong>param</strong> is the key within the data response, <strong>some value</strong> is the value. The example above will cause an output similar to:", $translation_ident ); ?>
        <pre>
{
    "param": "some value"
}
</pre>
        <?php echo WPWHPRO()->helpers->translate( "We do also support custom tags, meaning you can add dynamic values from the currently given data. E.g. <code>email=\"%user_email%\"</code> - This will add the email of the currently logged in user. For a full list of the dynamic arguments, please take a look at the list down below.", $translation_ident ); ?>

        <table class="wpwh-table wpwh-text-small">
            <thead>
                <tr>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag name", $translation_ident ); ?></td>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag description", $translation_ident ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->get_shortcode_tags() as $tag ) : 
                
                if( ! isset( $tag['tag_name'] ) ){
                    continue;
                }

                $title = '';
                if( isset( $tag['title'] ) ){
                    $title = '<strong>' . $tag['title'] . '</strong><br>';
                }

                $description = '';
                if( isset( $tag['description'] ) ){
                    $description = $tag['description'];
                }
                
                ?>
                <tr>
                    <td><?php echo '%' . $tag['tag_name'] . '%'; ?></td>
                    <td><?php echo $title . $description; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </li>
</ol>
<?php
            $how_to = ob_get_clean();

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Shortcode called',
				'webhook_slug' => 'wpwh_shortcode',
				'post_delay' => false,
				'how_to' => $how_to,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'add_shortcode',
                        'url' => 'https://developer.wordpress.org/reference/functions/add_shortcode/',
                    ),
				)
			) );

            return array(
                'trigger'           => 'wpwh_shortcode',
                'name'              => WPWHPRO()->helpers->translate( 'Shortcode called', 'trigger-login-user-content' ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a shortcode was called', 'trigger-login-user-content' ),
                'parameter'         => $parameter,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as the [wpwh_shortcode] shortcode was triggered.', 'trigger-login-user-content' ),
                'description'       => $description,
                'callback'          => 'test_wpwh_shortcode',
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wpwh_shortcode( $attr = array(), $content = '' ){
   
            $response_data = array();
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpwh_shortcode' );
            $special_arguments = array(
                'wpwh_trigger_names' => 'all',
                'wpwh_debug' => 'no',
            );

            foreach( $special_arguments as $ak => $dv ){
                if( isset( $attr[ $ak ] ) ){
                    $special_arguments[ $ak ] = $attr[ $ak ];
                    unset( $attr[ $ak ] );
                }
            }

            $shortcode_tags = $this->get_shortcode_tags();
            $attr_validated = $this->validate_data( $attr, $shortcode_tags );

            $trigger_name_whitelist = array();
            if( $special_arguments['wpwh_trigger_names'] !== 'all' ){
                $trigger_names_array = explode( ',', $special_arguments['wpwh_trigger_names'] );
                if( is_array( $trigger_names_array ) ){
                    foreach( $trigger_names_array as $single_trigger ){
                        $trigger_name_whitelist[] = trim( $single_trigger );
                    }
                }
            } 

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                
                if( ! empty( $trigger_name_whitelist ) && ! in_array( $webhook_url_name, $trigger_name_whitelist ) ){
                    continue;
                }

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                }

            }

            do_action( 'wpwhpro/webhooks/wpwh_shortcode', $attr_validated, $attr, $response_data );

            if( $special_arguments['wpwh_debug'] === 'yes' ){
                return $response_data;
            } else {
                return '';
            }
            
        }

        public function validate_data( $attr, $shortcode_tags ){

            if( is_array( $attr ) ){
                foreach( $attr as $ak => $av ){
                    $attr[ $ak ] = call_user_func( array( $this, 'validate_data' ), $av, $shortcode_tags );
                }
            } elseif( is_string( $attr ) ) {
                $attr = $this->validate_shortcode_tags( $attr, $shortcode_tags );
            }

            return $attr;
        }

        /**
         * This function validates all necessary tags for the shortcode.
         *
         * @param $content - The validated content
         * @since 1.4
         * @return mixed
         */
        public function validate_shortcode_tags( $content, $shortcode_tags ){
            $tags = array();
            $values = array();

            foreach( $shortcode_tags as $st ){
                if( isset( $st['tag_name'] ) && isset( $st['value'] ) ){
                    $fulltag = '%' . $st['tag_name'] . '%';
                    $tvalue = ( is_array( $st['value'] ) ) ? call_user_func_array( $st['value'], array( 'content' => $content ) ) : $st['value'];

                    //pre-return single content tags to also allow arrays and objects
                    if( strlen( str_replace( $fulltag, '', $content  ) ) === 0 ){
                        return $tvalue;
                    }

                    //Make sure to only allow strings here
                    if( is_string( $tvalue ) ){
                        $tags[] = $fulltag ;
                        $values[] = $tvalue;
                    }
                    
                }
            }

            $content = str_replace(
                $tags,
                $values,
                $content
            );

            return $content;
        }

        public function get_shortcode_tags(){
            $tags = array(

                'home_url' => array(
                    'tag_name' => 'home_url',
                    'title' => WPWHPRO()->helpers->translate( 'Home URL', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the home URL of the website.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_home_url' ),
                ),

                'admin_url' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Admin URL', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the admin URL of the website.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_admin_url' ),
                ),

                'date' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Date and Time', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The date and time in mySQL format: Y-m-d H:i:s', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_date' ),
                ),

                'user_id' => array(
                    'tag_name' => 'user_id',
                    'title' => WPWHPRO()->helpers->translate( 'User ID', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The ID of the currenty logged in user. 0 if none.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_id' ),
                ),

                'user' => array(
                    'tag_name' => 'user',
                    'title' => WPWHPRO()->helpers->translate( 'Full User', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user data of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user' ),
                ),

                'user_email' => array(
                    'tag_name' => 'user_email',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_email' ),
                ),

                'display_name' => array(
                    'tag_name' => 'display_name',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_display_name' ),
                ),

                'user_login' => array(
                    'tag_name' => 'user_login',
                    'title' => WPWHPRO()->helpers->translate( 'User Login Name', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The login name of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_login' ),
                ),

                'user_nicename' => array(
                    'tag_name' => 'user_nicename',
                    'title' => WPWHPRO()->helpers->translate( 'User Nicename', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The nicename of the currently logged in user.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_nicename' ),
                ),

                'user_roles' => array(
                    'tag_name' => 'user_roles',
                    'title' => WPWHPRO()->helpers->translate( 'User Roles', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The roles of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_roles' ),
                ),

                'user_meta' => array(
                    'tag_name' => 'user_meta',
                    'title' => WPWHPRO()->helpers->translate( 'User Meta', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user meta of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_user_meta' ),
                ),

                'post_id' => array(
                    'tag_name' => 'post_id',
                    'title' => WPWHPRO()->helpers->translate( 'Post ID', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post id of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_id' ),
                ),

                'post' => array(
                    'tag_name' => 'post',
                    'title' => WPWHPRO()->helpers->translate( 'Post Data', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post data of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post' ),
                ),

                'post_title' => array(
                    'tag_name' => 'post_title',
                    'title' => WPWHPRO()->helpers->translate( 'Post Title', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post title of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_title' ),
                ),

                'post_excerpt' => array(
                    'tag_name' => 'post_excerpt',
                    'title' => WPWHPRO()->helpers->translate( 'Post Excerpt', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post excerpt of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_excerpt' ),
                ),

                'post_content' => array(
                    'tag_name' => 'post_content',
                    'title' => WPWHPRO()->helpers->translate( 'Post Content', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post content of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_content' ),
                ),

                'post_author' => array(
                    'tag_name' => 'post_author',
                    'title' => WPWHPRO()->helpers->translate( 'Post Author', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post author of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_author' ),
                ),

                'post_type' => array(
                    'tag_name' => 'post_type',
                    'title' => WPWHPRO()->helpers->translate( 'Post Type', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post type of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_type' ),
                ),

                'post_status' => array(
                    'tag_name' => 'post_status',
                    'title' => WPWHPRO()->helpers->translate( 'Post Status', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post status of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_status' ),
                ),

                'post_date' => array(
                    'tag_name' => 'post_date',
                    'title' => WPWHPRO()->helpers->translate( 'Post Date', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post date of the currently given post.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_date' ),
                ),

                'post_meta' => array(
                    'tag_name' => 'post_meta',
                    'title' => WPWHPRO()->helpers->translate( 'Post Meta', 'trigger-wpwh_shortcode' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post meta of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_shortcode' ),
                    'value' => array( $this, 'tag_get_post_meta' ),
                ),

            );

            return apply_filters( 'wpwhpro/triggers/wpwh_shortcode/tags', $tags );
        }

        public function tag_get_home_url( $content = '' ){
            return home_url();
        }

        public function tag_get_admin_url( $content = '' ){
            return admin_url();
        }

        public function tag_get_date( $content = '' ){
            return date("Y-m-d H:i:s");
        }

        public function tag_get_user_id( $content = '' ){
            return get_current_user_id();
        }

        public function tag_get_user( $content = '' ){
            return $this->get_user();
        }

        public function tag_get_user_email( $content = '' ){
            return $this->get_user('user_email');
        }

        public function tag_get_display_name( $content = '' ){
            return $this->get_user('display_name');
        }

        public function tag_get_user_login( $content = '' ){
            return $this->get_user('user_login');
        }

        public function tag_get_user_nicename( $content = '' ){
            return $this->get_user('user_nicename');
        }

        public function tag_get_user_roles( $content = '' ){
            return $this->get_user('user_roles');
        }

        public function tag_get_user_meta( $content = '' ){
            $return = array();
            $user_id = get_current_user_id();

            if( ! empty( $user_id ) ){
                $return = get_user_meta( $user_id );
            }
            
            return $return;
        }

        public function get_user( $single_val = false ){

            $return = false;
            $user = get_user_by( 'id', get_current_user_id() );

            if( $single_val && ! empty( $user ) ){

                switch( $single_val ){
                    case 'user_email': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_email ) ){
                            $return = $user->data->user_email;
                        }
                        break;
                    case 'display_name': 
                        if( ! empty( $user->data ) && ! empty( $user->data->display_name ) ){
                            $return = $user->data->display_name;
                        }
                        break;
                    case 'user_login': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_login ) ){
                            $return = $user->data->user_login;
                        }
                        break;
                    case 'user_nicename': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_nicename ) ){
                            $return = $user->data->user_nicename;
                        }
                        break;
                    case 'user_roles': 
                        if( isset( $user->roles ) ){
                            $return = $user->data->user_nicename;
                        }
                        break;
                }
                
            } else {
                $return = $user;
            }

            return $return;
        }

        public function tag_get_post_id( $content = '' ){
            return get_the_ID();
        }

        public function tag_get_post( $content = '' ){
            return $this->get_post();
        }

        public function tag_get_post_title( $content = '' ){
            return $this->get_post('post_title');
        }

        public function tag_get_post_excerpt( $content = '' ){
            return $this->get_post('post_excerpt');
        }
        
        public function tag_get_post_content( $content = '' ){
            return $this->get_post('post_content');
        }

        public function tag_get_post_author( $content = '' ){
            return $this->get_post('post_author');
        }

        public function tag_get_post_type( $content = '' ){
            return $this->get_post('post_type');
        }

        public function tag_get_post_status( $content = '' ){
            return $this->get_post('post_status');
        }

        public function tag_get_post_date( $content = '' ){
            return $this->get_post('post_date');
        }

        public function tag_get_post_meta( $content = '' ){
            $return = array();
            $post_id = get_the_ID();

            if( ! empty( $post_id ) ){
                $return = get_post_meta( $post_id );
            }
            
            return $return;
        }

        public function get_post( $single_val = false ){

            $return = false;
            $post = get_post( get_the_ID() );

            if( $single_val && ! empty( $post ) ){

                switch( $single_val ){
                    case 'post_title': 
                        if( isset( $post->post_title ) && ! empty( $post->post_title ) ){
                            $return = $post->post_title;
                        }
                        break;
                    case 'post_excerpt': 
                        if( isset( $post->post_excerpt ) && ! empty( $post->post_excerpt ) ){
                            $return = $post->post_excerpt;
                        }
                        break;
                    case 'post_content': 
                        if( isset( $post->post_content ) && ! empty( $post->post_content ) ){
                            $return = $post->post_content;
                        }
                        break;
                    case 'post_author': 
                        if( isset( $post->post_author ) && ! empty( $post->post_author ) ){
                            $return = $post->post_author;
                        }
                        break;
                    case 'post_type': 
                        if( isset( $post->post_type ) && ! empty( $post->post_type ) ){
                            $return = $post->post_type;
                        }
                        break;
                    case 'post_status': 
                        if( isset( $post->post_status ) && ! empty( $post->post_status ) ){
                            $return = $post->post_status;
                        }
                        break;
                    case 'post_date': 
                        if( isset( $post->post_date ) && ! empty( $post->post_date ) ){
                            $return = $post->post_date;
                        }
                        break;
                }
                
            } else {
                $return = $post;
            }

            return $return;
        }

        /*
        * Register the demo data response
        *
        * @param $data - The default data
        * @param $webhook - The current webhook
        * @param $webhook_group - The current trigger this webhook belongs to
        *
        * @return array - The demo data
        */
        public function get_demo( $options = array() ){

            $data = array (
                'your custom data'
            );

            return $data;
        }

    }

endif; // End if class_exists check.