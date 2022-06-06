<?php

use FluentCrm\App\Models\SubscriberPivot;
use FluentCrm\App\Models\Lists;
use FluentCrm\App\Models\Tag;
use FluentCrm\App\Models\Subscriber;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Helpers_fcrm_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_crm_Helpers_fcrm_helpers {

        /**
		 * The cached statuses
		 */
        private $cache_get_statuses = false;

        /**
		 * The cached lists
		 */
        private $cache_get_lists = false;

        /**
		 * The cached tags
		 */
        private $cache_get_tags = false;

		/**
         * Validate the list ids against attached list ids 
		 *
		 * @return array A list of the real list ids 
		 */
		public function validate_list_ids( $list_ids ){
		    
            if( class_exists('\FluentCrm\App\Models\SubscriberPivot') ){
                $attached_ids = SubscriberPivot::whereIn( 'id', $list_ids )->get();

                if( is_object( $attached_ids ) && ! $attached_ids->isEmpty() ) {
                    $list_ids = array();
    
                    foreach( $attached_ids as $attached_id ) {
                        $list_ids[] = ( isset( $attached_id->object_id ) ) ? $attached_id->object_id : 0;
                    }
                }
            }

		    return $list_ids;
        }

		/**
         * Validate the tag ids against attached tag ids 
		 *
		 * @return array A list of the real list ids 
		 */
		public function validate_tag_ids( $tag_ids ){  
		    
            if( class_exists('\FluentCrm\App\Models\SubscriberPivot') ){

                $attached_ids = SubscriberPivot::whereIn( 'id', $tag_ids )->get();
                if( is_object( $attached_ids ) && ! $attached_ids->isEmpty() ) {
                    $tag_ids = array();
    
                    foreach( $attached_ids as $attached_id ) {
                        $tag_ids[] = ( isset( $attached_id->object_id ) ) ? $attached_id->object_id : 0;
                    }
                }

            }

		    return $tag_ids;
        }

		/**
         * Get all FluentCRM statuses with labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_statuses(){
		    
            $validated_statuses = array();
            if( function_exists( 'fluentcrm_subscriber_statuses' ) ) {
                $statuses = fluentcrm_subscriber_statuses();

                if( ! empty( $statuses ) ) {
                    foreach ( $statuses as $status_slug ) {
                        $validated_statuses[ $status_slug ] = WPWHPRO()->helpers->translate( ucfirst( $status_slug ) );
                    }
                }
            }

		    return $validated_statuses;
        }

        public function get_query_statuses( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

			$status_items = $this->get_statuses();

			foreach( $status_items as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

        /**
         * Find a contact by given information
		 *
		 * @return array The Subscriber object
		 */
		public function get_contact( $type, $value, $first = true ){
		    
            $subscriber = null;
            if( class_exists( '\FluentCrm\App\Models\Subscriber' ) ){
                $subscribers = Subscriber::where( $type, $value );
   
                if( is_object( $subscribers ) ) {
                    if( $first ){
                        $subscriber = $subscribers->first();
                    } else {
                        $subscriber = $subscribers->get();
                    }
                    
                }
            }

		    return $subscriber;
        }

		/**
         * Get all FluentCRM lists with its labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_lists(){

            if( $this->cache_get_lists !== false ){
                return $this->cache_get_lists;
            }

            $validated_lists = array();
            if( class_exists( '\FluentCrm\App\Models\Lists' ) ){
                $lists = Lists::orderBy( 'title', 'DESC' )->get();

                   

                if( ! empty( $lists ) ) {
                    foreach( $lists as $list ) {
                        $validated_lists[ $list->id ] = esc_html( $list->title );
                    }
                }
            }

            $this->cache_get_lists = $validated_lists;

		    return $validated_lists;
        }

        public function get_query_lists( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

			$list_items = $this->get_lists();

			foreach( $list_items as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

		/**
         * Get all FluentCRM tags with its labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_tags(){

            $validated_tags = array();
            if( class_exists( '\FluentCrm\App\Models\Tag' ) ){
                $tags = Tag::orderBy( 'title', 'DESC' )->get();

                   

                if( ! empty( $tags ) ) {
                    foreach( $tags as $tag ) {
                        $validated_tags[ $tag->id ] = esc_html( $tag->title );
                    }
                }
            }

		    return $validated_tags;
        }

        public function get_query_tags( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

			$tag_items = $this->get_tags();

			foreach( $tag_items as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

        public function get_countries(){

            $countries = array();

            if( defined('FLUENTCRM') ){
                $countries_unformatted = apply_filters( FLUENTCRM . '-countries', array() );
                if( ! empty( $countries_unformatted ) ){
                    foreach( $countries_unformatted as $country ){
                        $countries[ $country['code'] ] = $country['title'];
                    }
                }
            }
            
            return $countries;
        }

	}

endif; // End if class_exists check.