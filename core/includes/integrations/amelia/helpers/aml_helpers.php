<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_amelia_Helpers_aml_helpers' ) ) :

	/**
	 * Load the Amelia helpers
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_amelia_Helpers_aml_helpers {

		/**
		 * Get all Amelia webhook types
		 *
		 * @return array A list of the available types 
		 */
		public function get_types(){

			$types = array(
				'appointment' => WPWHPRO()->helpers->translate( 'Appointment', 'trigger-aml_helpers-get_types' ),
				'event' => WPWHPRO()->helpers->translate( 'Event', 'trigger-aml_helpers-get_types' ),
			);

			return apply_filters( 'wpwhpro/webhooks/aml_helpers/get_types', $types );
		}

		public function get_amelia_types( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

			$integration_items = $this->get_types();

			foreach( $integration_items as $name => $title ){

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

		public function get_amelia_statuses( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

			$integration_items = $this->get_statuses();

			foreach( $integration_items as $name => $title ){

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
		 * Get all Amelia booking statuses
		 *
		 * @return array A list of the available statuses 
		 */
		public function get_statuses(){

			$types = array(
				'canceled' => WPWHPRO()->helpers->translate( 'Canceled', 'trigger-aml_helpers-get_statuses' ),
				'approved' => WPWHPRO()->helpers->translate( 'Approved', 'trigger-aml_helpers-get_statuses' ),
				'pending' => WPWHPRO()->helpers->translate( 'Pending', 'trigger-aml_helpers-get_statuses' ),
				'rejected' => WPWHPRO()->helpers->translate( 'Rejected', 'trigger-aml_helpers-get_statuses' ),
			);

			return apply_filters( 'wpwhpro/webhooks/aml_helpers/get_statuses', $types );
		}

		/**
		 * Verify the payload of the incoming data of Amelia 
		 * webhook requests to the same notation as amelia
		 * 
		 * This notation is extracted from 
		 * ameliabooking/src/Domain/Entity/Entities.php
		 *
		 * @param array $reservation
		 * @param array $bookings
		 * @param object $service
		 * @return array - The payload
		 */
		public function process_webhook_data( $reservation, $bookings, $service ){

			$payload_data = array();
			$entity_bookings = ( class_exists( 'Entities' ) && method_exists( 'Entities', 'BOOKINGS' ) ) ? Entities::BOOKINGS : 'bookings';
			$entity_appointment = ( class_exists( 'Entities' ) && method_exists( 'Entities', 'APPOINTMENT' ) ) ? Entities::APPOINTMENT : 'appointment';
			$entity_event = ( class_exists( 'Entities' ) && method_exists( 'Entities', 'EVENT' ) ) ? Entities::EVENT : 'event';
			$action_url = ( defined( 'AMELIA_ACTION_URL' ) ) ? AMELIA_ACTION_URL : admin_url('admin-ajax.php', '') . '?action=wpamelia_api&call=';

			/** @var SettingsService $settingsService */
			$settingsService = $service->get('domain.settings.service');
			/** @var BookingApplicationService $bookingApplicationService */
			$bookingApplicationService = $service->get('application.booking.booking.service');
	
			/** @var HelperService $helperService */
			$helperService = $service->get('application.helper.service');

			$reservationEntity = $bookingApplicationService->getReservationEntity($reservation);

			$affectedBookingEntitiesArray = [];

			foreach ($bookings as $booking) {
				/** @var CustomerBooking $bookingEntity */
				$bookingEntity = $bookingApplicationService->getBookingEntity($booking);

				$bookingEntityArray = $bookingEntity->toArray();

				if (isset($booking['isRecurringBooking'])) {
					$bookingEntityArray['isRecurringBooking'] = $booking['isRecurringBooking'];

					$bookingEntityArray['isPackageBooking'] = $booking['isPackageBooking'];
				}

				$affectedBookingEntitiesArray[] = $bookingEntityArray;
			}

			$reservationEntityArray = $reservationEntity->toArray();

			switch ($reservation['type']) {
				case $entity_appointment:
					if (isset($reservationEntityArray['provider']['googleCalendar']['token'])) {
						unset($reservationEntityArray['provider']['googleCalendar']['token']);
					}

					if (isset($reservationEntityArray['provider']['outlookCalendar']['token'])) {
						unset($reservationEntityArray['provider']['outlookCalendar']['token']);
					}

					break;

				case $entity_event:
					break;
			}

			foreach ($affectedBookingEntitiesArray as $key => $booking) {
				if ($booking['customFields'] && json_decode($booking['customFields'], true) !== null) {
					$affectedBookingEntitiesArray[$key]['customFields'] = json_decode($booking['customFields'], true);
				}

				$affectedBookingEntitiesArray[$key]['cancelUrl'] = !empty($booking['token']) ?
					$action_url .
					'/bookings/cancel/' . $booking['id'] .
					'&token=' . $booking['token'] .
					"&type={$reservation['type']}" : '';

				$info = !empty($booking['info']) ?
					json_decode($booking['info'], true) : null;

				$affectedBookingEntitiesArray[$key]['customerPanelUrl'] = $helperService->getCustomerCabinetUrl(
					$booking['customer']['email'],
					'email',
					null,
					null,
					isset($info['locale']) ? $info['locale'] : ''
				);

				$affectedBookingEntitiesArray[$key]['infoArray'] = $info;
			}

			foreach ($reservationEntityArray['bookings'] as $key => $booking) {
				if ($booking['customFields'] && json_decode($booking['customFields'], true) !== null) {
					$reservationEntityArray['bookings'][$key]['customFields'] = json_decode(
						$booking['customFields'],
						true
					);
				}

				$reservationEntityArray['bookings'][$key]['cancelUrl'] = !empty($booking['token']) ?
					$action_url .
					'/bookings/cancel/' . $booking['id'] .
					'&token=' . $booking['token'] .
					"&type={$reservation['type']}" : '';

				$info = !empty($booking['info']) ?
					json_decode($booking['info'], true) : null;

				$reservationEntityArray['bookings'][$key]['customerPanelUrl'] = $helperService->getCustomerCabinetUrl(
					$booking['customer']['email'],
					'email',
					null,
					null,
					isset($info['locale']) ? $info['locale'] : ''
				);

				$reservationEntityArray['bookings'][$key]['infoArray'] = $info;
			}

			$payload_data = array(
				$reservationEntity->getType()->getValue()	=> $reservationEntityArray,
				$entity_bookings	                        => $affectedBookingEntitiesArray
			);

			return apply_filters( 'wpwhpro/webhooks/aml_helpers/process_webhook_data', $payload_data, $reservation, $bookings, $service );
		}

	}

endif; // End if class_exists check.