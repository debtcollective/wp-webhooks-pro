<?php

/**
 * WP_Webhooks_Pro_Logs Class
 *
 * This class contains all of the available logging functions
 *
 * @since 1.6.3
 */

/**
 * The log class of the plugin.
 *
 * @since 1.6.3
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Logs {

	/**
	 * WP_Webhooks_Pro_Logs constructor.
	 */
	public function __construct() {

		$this->log_table_data = WPWHPRO()->settings->get_log_table_data();
		$this->cache_log = array();
		$this->cache_log_count = 0;
		$this->table_exists = false;

	}

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 4.3.3
	 * @return void
	 */
	public function execute(){

		//Maintenance filter
		add_action( 'wpwh_daily_maintenance', array( $this, 'maybe_clean_logs' ), 10 );

	}

	/**
	 * Maybe clean logs based on the given maintenance scheduled event
	 *
	 * @since 4.3.3
	 * @return void
	 */
	public function maybe_clean_logs(){

		if( get_option( 'wpwhpro_autoclean_logs' ) !== 'yes' ){
			return;
		}

		$interval = apply_filters( 'wpwhpro/logs/clean_interval', 30 );
		$this->delete_log( 'daily-' . $interval );

	}

	/**
	 * Wether the log functionality is active or not
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return boolean - True if active, false if not
	 */
	public function is_active(){
		return true;
	}

	/**
	 * Init the base logging table to the database
	 *
	 * @return void
	 */
	public function maybe_setup_logs_table(){

		//shorten circle if already set up
		if( $this->table_exists ){
			return;
		}
			
		if( ! WPWHPRO()->sql->table_exists( $this->log_table_data['table_name'] ) ){
			WPWHPRO()->sql->run_dbdelta( $this->log_table_data['sql_create_table'] );
		}

		$this->table_exists = true;
		
	}

	/**
	 * Returns certain items of the logs table
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @return array - An array of the given log data
	 */
	public function get_log( $offset = 0, $limit = 10 ){

		if( ! empty( $this->cache_log ) ){
			return $this->cache_log;
		}

		$this->maybe_setup_logs_table();

		$sql = 'SELECT * FROM {prefix}' . $this->log_table_data['table_name'] . ' ORDER BY id DESC LIMIT ' . intval( $limit ) . ' OFFSET ' . intval( $offset ) . ';';
		$data = WPWHPRO()->sql->run($sql);
		$this->cache_log = $data;

		return $data;
	}

	/**
	 * Count the given log data
	 *
	 * @param integer $offset
	 * @param integer $limit
	 * @return mixed - Integer if log data found, false if not
	 */
	public function get_log_count( $offset = 0, $limit = 10 ){

		if( ! empty( $this->cache_log_count ) ){
			return intval( $this->cache_log_count );
		}

		$this->maybe_setup_logs_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->log_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if( is_array( $data ) && ! empty( $data ) ){
			$this->cache_log_count = $data[0]->{"COUNT(*)"};
			return intval( $data[0]->{"COUNT(*)"} );
		} else {
			return false;
		}

	}


	/**
	 * Add a log data item to the logs
	 *
	 * @param string $msg
	 * @param mixed $data can be everything that should be saved as log data
	 * @return bool - True if the function runs successfully
	 */
	public function add_log( $msg, $data ){

		$this->maybe_setup_logs_table();

		$sql_vals = array(
			'message' => base64_encode( $msg ),
			'content' => ( is_array( $data ) || is_object( $data ) ) ? base64_encode( json_encode( $data ) ) : base64_encode( $data ),
			'log_time' => date( 'Y-m-d H:i:s' )
		);

		// START UPDATE PRODUCT
		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ){

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->log_table_data['table_name'] . ' (' . trim($sql_keys, ', ') . ') VALUES (' . trim($sql_values, ', ') . ');';
		$id = WPWHPRO()->sql->run( $sql, OBJECT, array( 'return_id' => true ) );

		do_action( 'wpwhpro/logs/add_log', $id, $msg, $data );

		return $id;

	}

	/**
	 * Add a log data item to the logs
	 *
	 * @param string $msg
	 * @param mixed $data can be everything that should be saved as log data
	 * @return bool - True if the function runs successfully
	 */
	public function delete_log( $log = 'all' ){

		$this->maybe_setup_logs_table();

		$check = false;

		if( $log === 'all' ){
			$check = $this->delete_table();
		} else {

			$sql = '';

			$ident = 'daily-';
			if( strlen( $log ) > strlen( $ident ) && substr( $log, 0, strlen( $ident ) ) === $ident ){
				$interval = str_replace( $ident, '', $log );
				if( ! empty( $interval ) && is_numeric( $interval ) ){

					$interval = intval( $interval );

					$sql = "
						DELETE FROM {prefix}" . $this->log_table_data['table_name'] . " 
						WHERE log_time < DATE_SUB(NOW(), INTERVAL %d DAY);
					";
					$sql = WPWHPRO()->sql->prepare( $sql, array( $interval ) );
				}
			} else {
				$log = intval( $log );
				$sql = 'DELETE FROM {prefix}' . $this->log_table_data['table_name'] . ' WHERE id = "' . $log . '";';
			}
			
			if( ! empty( $sql ) ){
				$check = WPWHPRO()->sql->run($sql);
			}
			
		}

		return $check;

	}

	/**
	 * Delete the log data table
	 *
	 * @return bool - True if the log table was deleted successfully
	 */
	public function delete_table(){

		$check = true;

		if( WPWHPRO()->sql->table_exists( $this->log_table_data['table_name'] ) ){
			$check = WPWHPRO()->sql->run( $this->log_table_data['sql_drop_table'] );
		}

		$this->table_exists = false;

		return $check;
	}

	/**
	 * Sanitize the values of a given content array to prevent the log oview from breaking
	 */
	public function sanitize_array_object_values( $array ){

		if( is_array( $array ) ){
			foreach( $array as $key => $val ){
				if( is_string( $val ) ){
					$array[ $key ] = htmlspecialchars( str_replace( '"', '&quot', $val ) );
				} else {
					$array[ $key ] = $this->sanitize_array_object_values( $val );
				}
			}
		} elseif( is_object( $array ) ){
			foreach( $array as $key => $val ){
				if( is_string( $val ) ){
					$array->{$key} = htmlspecialchars( str_replace( '"', '&quot', $val ) );
				} else {
					$array->{$key} = $this->sanitize_array_object_values( $val );
				}
			}
		}

		return $array;

	}

	public function pagination( $args = array() ) {
	 
		$per_page = isset( $args['per_page'] ) ? intval( $args['per_page'] ) : 10;
		$page = isset( $args['log_page'] ) ? intval( $args['log_page'] ) : 1;

		$page_counter = 1;
		$log_count = $this->get_log_count();
		$total_pages = ceil( $log_count / $per_page );
		$current_url = WPWHPRO()->helpers->get_current_url( false, true );

		if( $page > $total_pages ){
			$page = $total_pages;
		}

		if( $page < 1 ){
			$page = 1;
		}

		$pagination_links_out = array();

		$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 1, ) ) ) . '" class="wpwh-btn--sm mr-1">' . WPWHPRO()->helpers->translate( '<<', 'logs' ) . '</a>';

		if( $page <= 1 ){
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 1, ) ) ) . '" class="wpwh-btn--sm mr-1">' . WPWHPRO()->helpers->translate( '<', 'logs' ) . '</a>';
		} else {
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => ($page-1), ) ) ) . '" class="wpwh-btn--sm mr-1">' . WPWHPRO()->helpers->translate( '<', 'logs' ) . '</a>';
		}
		

		if( $total_pages > 3 ){
			
			
			if( $page === 1 ){
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 1, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . 1 . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 2, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . 2 . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => 3, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . 3 . '</a>';
			} elseif( $page >= $total_pages ){
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page-2, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-2) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page-1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-1) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page . '</a>';
			} else {
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page-1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page-1) . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page . '</a>';
				$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page+1, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . ($page+1) . '</a>';
			}

			
			
		} else {
			$page_counter = 1;
			$total_pages_tmp = $total_pages;
			while( $total_pages_tmp > 0 ){

				if( $page_counter === $page ){
					$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page_counter, ) ) ) . '" class="wpwh-btn wpwh-btn--primary wpwh-btn--sm mr-1">' . $page_counter . '</a>';
				} else {
					$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $page_counter, ) ) ) . '" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1">' . $page_counter . '</a>';
				}
				
				$page_counter++;
				$total_pages_tmp--;
			}
		}
		
		if( $page >= $total_pages ){
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $total_pages, ) ) ) . '" class="wpwh-btn--sm mr-1">' . WPWHPRO()->helpers->translate( '>', 'logs' ) . '</a>';
		} else {
			$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => ($page+1), ) ) ) . '" class="wpwh-btn--sm mr-1">' . WPWHPRO()->helpers->translate( '>', 'logs' ) . '</a>';
		}

		$pagination_links_out[] = '<a href="' . WPWHPRO()->helpers->built_url( $current_url, array_merge( $_GET, array( 'item_count' => $per_page, 'log_page' => $total_pages, ) ) ) . '" class="wpwh-btn--sm mr-1">' . WPWHPRO()->helpers->translate( '>>', 'logs' ) . '</a>';

		return implode( '', $pagination_links_out );
	}

}
