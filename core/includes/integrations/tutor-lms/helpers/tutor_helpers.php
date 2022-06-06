<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Helpers_tutor_helpers' ) ) :

	/**
	 * Load the WP Webhooks helpers
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tutor_lms_Helpers_tutor_helpers {

		public function get_courses() {
			$validated_courses = array();

			if( function_exists( 'tutor_utils' ) ){
				$courses = tutor_utils()->get_courses();
				if( ! empty( $courses ) ){
					foreach( $courses as $course ){
						$validated_courses[ $course->ID ] = $course->post_title;
					}
				}
			}

			return $validated_courses;
		}

		public function get_lessons() {
			$validated_lessons = array();

			$lessons = get_posts( array(
				'post_type' => apply_filters( 'tutor_lesson_post_type', 'lesson' ),
				'posts_perpage' => 999,
			) );
			if( ! empty( $lessons ) ){
				foreach( $lessons as $lesson ){
					$validated_lessons[ $lesson->ID ] = $lesson->post_title;
				}
			}

			return $validated_lessons;
		}

		public function get_quizzes() {
			$validated_quizzes = array();

			if( function_exists( 'tutor_utils' ) ){
				$quizzes = get_posts( array(
					'post_type' => apply_filters( 'tutor_quiz_post_type', 'tutor_quiz' ),
					'posts_perpage' => 999,
				) );
				if( ! empty( $quizzes ) ){
					foreach( $quizzes as $quiz ){
						$validated_quizzes[ $quiz->ID ] = $quiz->post_title;
					}
				}
			}

			return $validated_quizzes;
		}

		public function complete_course( $course_id, $user_id ){

			global $wpdb;

			$courses = array();
			$user_id = intval( $user_id );

			if( empty( $course_id ) || empty( $user_id ) ){
				return false;
			}

			if( $course_id === 'all' ){
				$raw_courses = $this->get_courses();
				foreach( $raw_courses as $single_course_id => $single_course ){
					$courses[] = intval( $single_course_id );
				}
			} else {
				$courses[] = intval( $course_id );
			}

			foreach( $courses as $course ){

				if( tutils()->is_completed_course( $course, $user_id ) ){
					continue;
				}

				//Maybe complete actions before
				$completion_process = tutils()->get_option( 'course_completion_process' );
				if( $completion_process === 'strict'){
					$this->complete_course_lessons( $course_id, $user_id );
				}

				do_action( 'tutor_course_complete_before', $course );

				$date = date( 'Y-m-d H:i:s', tutor_time() );

				// Making sure that, hash is unique
				do {
					$hash    = substr( md5( wp_generate_password( 32 ) . $date . $course . $user_id ), 0, 16 );
					$hasHash = (int) $wpdb->get_var(
						$wpdb->prepare(
							"SELECT COUNT(comment_ID) from {$wpdb->comments}
						WHERE comment_agent = 'TutorLMSPlugin' AND comment_type = 'course_completed' AND comment_content = %s ",
							$hash
						)
					);

				} while ( $hasHash > 0 );

				$data = array(
					'comment_post_ID'  => $course,
					'comment_author'   => $user_id,
					'comment_date'     => $date,
					'comment_date_gmt' => get_gmt_from_date( $date ),
					'comment_content'  => $hash, // Identification Hash
					'comment_approved' => 'approved',
					'comment_agent'    => 'TutorLMSPlugin',
					'comment_type'     => 'course_completed',
					'user_id'          => $user_id,
				);

				$wpdb->insert( $wpdb->comments, $data );

				do_action( 'tutor_course_complete_after', $course, $user_id );
			}

			return true;
		}

		public function complete_course_lessons( $course_id, $user_id ){

			if( empty( $course_id ) || empty( $user_id ) ){
				return false;
			}

			$lesson_query = tutils()->get_lesson( $course_id, -1 );
			if( $lesson_query->found_posts > 0 ){
				foreach( $lesson_query->posts as $lesson ){
					tutils()->mark_lesson_complete( $lesson->ID, $user_id );
				}
			}

			return true;
		}

		public function enroll_user_to_course( $course_id, $user_id ){

			$courses = array();
			$user_id = intval( $user_id );

			if( empty( $course_id ) || empty( $user_id ) ){
				return false;
			}

			if( $course_id === 'all' ){
				$raw_courses = $this->get_courses();
				foreach( $raw_courses as $single_course_id => $single_course ){
					$courses[] = intval( $single_course_id );
				}
			} else {
				$courses[] = intval( $course_id );
			}

			add_filter( 'tutor_enroll_data', array( $this, 'forece_enrollment_completed' ) );

			// Enroll user in courses
			foreach( $courses as $course ) {
				tutor_utils()->do_enroll( $course, 0, $user_id );
			}

			remove_filter( 'tutor_enroll_data', array( $this, 'forece_enrollment_completed' ) );

			return $courses;
		}

		public function forece_enrollment_completed( $enrollment ){

			$enrollment['status'] = 'completed';
	
			return $enrollment;
		}

	}

endif; // End if class_exists check.