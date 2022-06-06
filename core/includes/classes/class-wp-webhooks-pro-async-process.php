<?php

/**
 * WP_Webhooks_Pro_Async_Process Class
 *
 * This class contains all of the available api functions
 *
 * @since 4.3.0
 */

/**
 * The async class of the plugin.
 *
 * @since 4.3.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Async_Process extends WP_Background_Process {

	public function __construct( $args = array() ) {
		/** We need to set the prefix and the identifier before constructing the parent class `WP_Async_Request` */
		$this->prefix = isset( $args['prefix'] ) ? $args['prefix'] : 'wpwh';
		$this->action = isset( $args['action'] ) ? $args['action'] : 'wpwh_default_process';

		parent::__construct();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item_data Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item_data ) {
		$retry = false;

		if( is_array( $item_data ) ){

			$item_data = apply_filters( 'wpwhpro/async/process/' . $this->action, $item_data );

			if( isset( $item_data['set_class_data'] ) && is_array( $item_data['set_class_data'] ) && ! empty( $item_data['set_class_data'] ) ){
				foreach( $item_data['set_class_data'] as $data_key => $data_value ){
					$this->{ $data_key } = $data_value;
				}
			}

			if( isset( $item_data['retry'] ) && $item_data['retry'] === true ){
				$retry = true;
			}

		}

		if( $retry ){
			return $item_data;
		} else {
			return false;
		}
		
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		do_action( 'wpwhpro/async/process/completed/' . $this->action, $this );

		parent::complete();
	}

}
