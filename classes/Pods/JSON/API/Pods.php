<?php
/**
 * Class Pods_JSON_API_Pods
 */
class Pods_JSON_API_Pods {

	/**
	 * Register endpoints for JSON REST API.
	 *
	 * @param array $routes
	 *
	 * @return array
	 *
	 *
	 * @access public
	 */
	public function register_routes( $routes ) {

		$routes[ '/pods/(?P<pod>[\w\-\_]+)' ] = array(
			array( array( $this, 'get_items' ), WP_JSON_Server::READABLE | WP_JSON_Server::ACCEPT_JSON ),
			array( array( $this, 'add_item' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON )
		);

		$routes[ '/pods/(?P<pod>[\w\-\_]+)/(?P<item>[\w\-\_]+)' ] = array(
			array( array( $this, 'get_item' ), WP_JSON_Server::READABLE ),
			array( array( $this, 'save_item' ), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
			array( array( $this, 'delete_item' ), WP_JSON_Server::DELETABLE )
		);

		$routes[ '/pods/(?P<pod>[\w\-\_]+)/(?P<id>\d+)/duplicate' ] = array(
			array( array( $this, 'duplicate_item' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON )
		);

		return $routes;

	}

	/**
	 * List Pod items
	 *
	 * @param string $pod Pod name
	 * @param array $data find() parameters
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function get_items( $pod, $data = array() ) {

		if ( !$this->check_access( __FUNCTION__, $pod ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$params = pods_sanitize( $data );

			// Force limited $params if not admin
			if ( !$this->check_access( false ) ) {
				$safe_params = array();

				if ( isset( $params[ 'limit' ] ) ) {
					$safe_params[ 'limit' ] = (int) $params[ 'limit' ];
				}

				if ( isset( $params[ 'page' ] ) ) {
					$safe_params[ 'page' ] = (int) $params[ 'page' ];
				}

				if ( isset( $params[ 'offset' ] ) ) {
					$safe_params[ 'offset' ] = (int) $params[ 'offset' ];
				}

				$params = $safe_params;
			}

			$pod_object = pods( $pod );

			$params = apply_filters( 'pods_json_api_pods_get_items_params', $params, $pod, $data, $pod_object );

			$pod_object->find( $params );

			$items = $pod_object->export_data();

			$items = apply_filters( 'pods_json_api_pods_get_items', $items, $pod, $params, $data, $pod_object );

			/**
			 * Optionally do not return user email and password for user pod
			 *
			 * If false, user's email and their <em>hashed</em> password will not be returned.
			 *
			 * @since 0.2.1
			 *
			 * @param bool True to not return email/password
			 */
			if ( $pod === 'user' && apply_filters( 'pods_json_api_disable_sensitive_user_data', true ) ) {
				if ( isset( $items[ 'user_email' ] ) ) {
					unset( $items[ 'user_email' ] );
				}

				if ( isset( $items[ 'user_pass' ] ) ) {
					unset( $items[ 'user_pass' ] );
				}

			}

		}
		catch ( Exception $e ) {
			$items = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		return $items;

	}

	/**
	 * Add a Pod item
	 *
	 * @param string $pod Pod name
	 * @param array $data Pod item data
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function add_item( $pod, $data ) {

		if ( !$this->check_access( __FUNCTION__, $pod ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		if ( isset( $data[ 'id' ] ) ) {
			unset( $data[ 'id' ] );
		}

		try {
			$id = pods( $pod )->save( $data );
		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $this->get_item( $pod, $id ) );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods/' . $pod . '/' . $id ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error adding pod item', 'pods-json-api' ) );
		}

	}

	/**
	 * Get a Pod item
	 *
	 * @param string $pod Pod name
	 * @param int|string $item Item ID or slug
	 *
	 * @return object|WP_Error
	 *
	 * @access public
	 */
	public function get_item( $pod, $item ) {

		if ( !$this->check_access( __FUNCTION__, $pod, $item ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$pod_object = pods( $pod, $item );

			$data = $pod_object->export();

			$data = apply_filters( 'pods_json_api_pods_' . __FUNCTION__, $data, $pod, $item, $data, $pod_object );

			/**
			 * pods_json_api_disable_sensitive_user_data filter is documented in the get_items method of this class.
			 */
			if ( $pod === 'user' && apply_filters( 'pods_json_api_disable_sensitive_user_data', true ) ) {
				if ( isset( $items[ 'user_email' ] ) ) {
					unset( $items[ 'user_email' ] );
				}

				if ( isset( $items[ 'user_pass' ] ) ) {
					unset( $items[ 'user_pass' ] );
				}

			}
		}
		catch ( Exception $e ) {
			$data = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		return $data;

	}

	/**
	 * Save a Pod item
	 *
	 * @param string $pod Pod name
	 * @param int|string $item Item ID or slug
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function save_item( $pod, $item, $data ) {

		if ( !$this->check_access( __FUNCTION__, $pod, $item ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$id = pods( $pod, $item )->save( $data );
		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $this->get_item( $pod, $id ) );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods/' . $pod . '/' . $id ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error saving pod item', 'pods-json-api' ) );
		}

	}

	/**
	 * Delete a Pod item
	 *
	 * @param string $pod Pod name
	 * @param int|string $item Item ID or slug
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function delete_item( $pod, $item ) {

		if ( !$this->check_access( __FUNCTION__, $pod, $item ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$deleted = pods( $pod, $item )->delete();
		}
		catch ( Exception $e ) {
			$deleted = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $deleted instanceof WP_Error ) {
			return $deleted;
		}
		elseif ( $deleted ) {
			return true;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error deleting pod item', 'pods-json-api' ) );
		}

	}

	/**
	 * Duplicate a Pod item
	 *
	 * @param string $pod Pod name
	 * @param int|string $item Item ID or slug
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function duplicate_item( $pod, $item ) {

		if ( !$this->check_access( __FUNCTION__, $pod, $item ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$id = pods( $pod, $item )->duplicate();
		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $this->get_item( $pod, $id ) );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods/' . $pod . '/' . $id ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error duplicating pod item', 'pods-json-api' ) );
		}

	}

	/**
	 * Check if user has access to endpoint
	 *
	 * @param string $method Method name
	 * @param string $pod Pod name
	 * @param int|string $item Item ID or slug
	 *
	 * @return boolean If user has access
	 *
	 * @access protected
	 */
	protected function check_access( $method, $pod = null, $item = 0 ) {

		$access_caps = array(
			'pods'
		);

		if ( $method ) {
			$access_caps[] = 'pods_content';
		}

		if ( $pod ) {
			if ( in_array( $method, array( 'add_item', 'duplicate_item' ) ) ) {
				$access_caps[] = 'pods_add_' . $pod;
			}
			elseif ( 'save_item' == $method ) {
				$access_caps[] = 'pods_edit_' . $pod;
			}
			elseif ( 'delete_item' == $method ) {
				$access_caps[] = 'pods_delete_' . $pod;
			}
		}

		$access = pods_is_admin( $access_caps );

		$access = apply_filters( 'pods_json_api_access_pods_' . $method, $access, $method, $pod, $item );
		$access = apply_filters( 'pods_json_api_access_pods', $access, $method, $pod, $item );

		return $access;

	}

}
