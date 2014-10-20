<?php
/**
 * Class Pods_JSON_API_Pods_API
 */
class Pods_JSON_API_Pods_API {

	/**
	 * Register endpoints for JSON REST API.
	 *
	 * @param array $routes
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function register_routes( $routes ) {

		$routes[ '/pods-api' ] = array(
			array( array( $this, 'get_pods' ), WP_JSON_Server::READABLE ),
			array( array( $this, 'add_pod' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
		);

		$routes[ '/pods-api/(?P<pod>[\w\-\_]+)' ] = array(
			array( array( $this, 'get_pod' ), WP_JSON_Server::READABLE ),
			array( array( $this, 'delete_pod' ), WP_JSON_Server::DELETABLE ),

		);

		$routes[ '/pods-api/(?P<pod>[\w\-\_]+)/duplicate' ] = array(
			array( array( $this, 'duplicate_pod' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON )
		);

		$routes[ '/pods-api/(?P<pod>[\w\-\_]+)/reset' ] = array(
			array( array( $this, 'reset' ), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON )
		);

		$routes[ '/pods-api/update_rel' ] = array(
			array( array( $this, 'update_rel' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON )
		);



		return $routes;

	}

	/**
	 * Get All Pods
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function get_pods() {

		if ( !$this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		$api = pods_api();
		$api->display_errors = false;

		$all_pods = $api->load_pods();

		$pods_to_list = array();

		foreach ( $all_pods as $pod ) {
			$pods_to_list[] = get_object_vars( $this->cleanup_pod( $pod, false ) );
		}

		return $pods_to_list;

	}

	/**
	 * Add a Pod
	 *
	 * @param array $data Pod data. Can contain:
	 *  - name
	 *  - type
	 *  - <field>=<value>
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function add_pod( $data ) {

		if ( !$this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		if ( isset( $data[ 'id' ] ) ) {
			unset( $data[ 'id' ] );
		}

		try {
			$api = pods_api();
			$api->display_errors = false;

			$id = $api->save_pod( $data );
		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $pod = $this->get_pod( $id ) );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods-api/' . $pod[ 'name' ] ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error adding pod', 'pods-json-api' ) );
		}

	}

	/**
	 * Get a Pod
	 *
	 * @param string|int $pod Pod name or ID (if typed as integer)
	 *
	 * @return object|WP_Error
	 *
	 * @access public
	 */
	public function get_pod( $pod ) {

		if ( !$this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$api = pods_api();
			$api->display_errors = false;

			$params = array();

			if ( is_int( $pod ) ) {
				$params[ 'id' ] = $pod;
			}
			else {
				$params[ 'name' ] = $pod;
			}

			$pod = $api->load_pod( $params );
		}
		catch ( Exception $e ) {
			$pod = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $pod instanceof WP_Error ) {
			return $pod;
		}
		elseif ( $pod ) {
			return get_object_vars( $this->cleanup_pod( $pod ) );
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error getting pod', 'pods-json-api' ) );
		}

	}

	/**
	 * Save a Pod
	 *
	 * @param string|int $pod Pod name or ID (if typed as integer)
	 * @param array $data Pod data. Can contain:
	 *  - <field>=<value>
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function save_pod( $pod, $data ) {

		if ( !$this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$api = pods_api();
			$api->display_errors = false;

			if ( is_int( $pod ) ) {
				$data[ 'id' ] = $pod;
			}
			else {
				$data[ 'name' ] = $pod;
			}

			$id = $api->save_pod( $data );
		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $pod = $this->get_pod( $id ) );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods-api/' . $pod[ 'name' ] ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error saving pod', 'pods-json-api' ) );
		}

	}

	/**
	 * Delete a Pod
	 *
	 * @param string|int $pod Pod name or ID (if typed as integer)
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function delete_pod( $pod ) {

		if ( !$this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$api = pods_api();
			$api->display_errors = false;

			$params = array();

			if ( is_int( $pod ) ) {
				$params[ 'id' ] = $pod;
			}
			else {
				$params[ 'name' ] = $pod;
			}

			$deleted = $api->delete_pod( $params );
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
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error deleting pod', 'pods-json-api' ) );
		}

	}

	/**
	 * Duplicate a Pod
	 *
	 * @param string|int $pod Pod name or ID (if typed as integer)
	 * @param array $data Pod data. Can contain:
	 *  - new_name
	 *  - <field>=<value>
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function duplicate_pod( $pod, $data = array() ) {

		if ( !$this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$api = pods_api();
			$api->display_errors = false;

			if ( is_int( $pod ) ) {
				$data[ 'id' ] = $pod;
			}
			else {
				$data[ 'name' ] = $pod;
			}

			$id = $api->duplicate_pod( $data );
		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $pod = $this->get_pod( $id ) );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods-api/' . $pod[ 'name' ] ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error duplicating pod', 'pods-json-api' ) );
		}

	}

	/**
	 * Reset a Pod's contents
	 *
	 * @param string|int $pod Pod name or ID (if typed as integer)
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function reset_pod( $pod ) {

		if ( !$this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		try {
			$api = pods_api();
			$api->display_errors = false;

			$params = array();

			if ( is_int( $pod ) ) {
				$params[ 'id' ] = $pod;
			}
			else {
				$params[ 'name' ] = $pod;
			}

			$reset = $api->reset_pod( $params );
		}
		catch ( Exception $e ) {
			$reset = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $reset instanceof WP_Error ) {
			return $reset;
		}
		elseif ( $reset ) {
			return true;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error resetting pod', 'pods-json-api' ) );
		}

	}

	/**
	 * Update bi-directional relationships to correct sister IDs.
	 *
	 * @see Readme for structure of data.
	 *
	 * @param int|string $pod Pod name or Pod ID.
	 * @param array $data Array of relationships for site
	 *
	 *
	 * @access public
	 *
	 * @return WP_Error|WP_JSON_ResponseInterface
	 */
	function update_rel( $data = array() ) {
		if ( ! $this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}
		try {
			$id = $other_pods = $fields_updated = false;
			$api = pods_api();
			$api->display_errors = false;

			$pod_names = $api->load_pods( array( 'names' => true ) );
			if ( ! empty( $pod_names ) ) {
				$pod_names = array_flip( (array) $pod_names );
			}

			foreach ( $data as $relates ) {
				$pod = $relates[ 'from' ][ 'pod_name' ];
				$this_pod = $api->load_pod( array( 'name' => $pod ) );
				$pod_id = $id = pods_v( 'id', $this_pod );
				$fields = pods_v( 'fields', $this_pod );
				unset( $this_pod );

				$other_pods = $fields_updated = array ();
				foreach( $fields as $field ) {
					if ( 'pick' == pods_v( 'type', $field ) ) {
						$field_id = pods_v( 'id', $field );
						$field_name = pods_v( 'name', $field );
						$to_pod = pods_v( 'pick_val', $field );

						if ( in_array( $to_pod, $pod_names ) ) {

							$relationship = pods_v( $pod . '_' . pods_v( 'name', $field ), $data );
							$to_pod = $relationship[ 'to' ][ 'pod_name' ];
							$to_field = $relationship[ 'to' ][ 'field_name' ];

							if ( is_null( $related_pod = pods_v( $to_pod, $other_pods ) ) ) {
								$params = array( 'name' => $to_pod );
								$related_pod =  $api->load_pod( $params );
								$related_pod = pods_v( 'fields', $related_pod );
								if ( is_object( $related_pod ) && ! empty ( $related_pod ) ) {
									$other_pods[ $to_pod ] = $related_pod;
								}

								$field = pods_v( $to_field, $related_pod );
								$sister_id = pods_v( 'id', $field  );

								if ( ! is_null( $pod_id ) && ! is_null( $field_id ) && ! is_null( $sister_id ) && ! is_null( $field_name ) ) {
									$params = array (
										'pod_id'    => $pod_id,
										'id'        => $field_id,
										'sister_id' => $sister_id,
										'name'      => $field_name,
									);
								}

								$fields_updated[] = $api->save_field( $params );

							}

						}

					}

				}
			}
		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif( isset( $fields_updated ) && is_array( $fields_updated ) ) {
			return $fields_updated;
			$response = json_ensure_response( $fields_updated );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods-api/' . $pod[ 'name' ] . '/' . __FUNCTION__ ) );

			return $response;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $pod = $this->get_pod( $id ) );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods-api/' . $pod[ 'name' ] . '/' . __FUNCTION__ ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error updating relationship', 'pods-json-api' ) );
		}

	}

	/**
	 * Import a Pods Migrate package
	 *
	 * Pods Package component must be active on site receiving data or an error will be returned.
	 *
	 * @param array $data Pods Migrate Package.
	 *
	 * @return boolean|WP_Error
	 *
	 * @access public
	 */
	public function package( $data ) {
		return new WP_Error( 'pods_json_api_error_' . __FUNCTION__ . 'no_package',  __( 'This endpoint requires activating the Pods Packages component on the site receiving the package.', 'pods-json-api' ) );
		if ( ! $this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		if ( ! class_exists(  'Pods_Migrate_Packages' ) ) {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__ . 'no_package',  __( 'This endpoint requires activating the Pods Packages component on the site receiving the package.', 'pods-json-api' ) );
		}

		try {

			$id = Pods_Migrate_Packages::import( $data );

		}
		catch ( Exception $e ) {
			$id = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $id instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $id;
		}
		elseif ( 0 < $id ) {
			$response = json_ensure_response( $id );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods-api/package' ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error importing package.', 'pods-json-api' ) );
		}

	}

	/**
	 * Check if user has access to endpoint
	 *
	 * @param string $method Method name
	 *
	 * @return boolean If user has access
	 *
	 * @access protected
	 */
	protected function check_access( $method ) {

		$access = pods_is_admin( array( 'pods' ) );

		$access = apply_filters( 'pods_json_api_access_api_' . $method, $access, $method );
		$access = apply_filters( 'pods_json_api_access_api', $access, $method );

		return $access;

	}

	/**
	 * Cleanup Pod data for return
	 *
	 * @param array $pod Pod array
	 * @param boolean $fields Include fields in pod
	 *
	 * @return object
	 *
	 * @access protected
	 */
	function cleanup_pod( $pod, $fields = true ) {

		$options_ignore = array(
			'pod_id',
			'old_name',
			'object_type',
			'object_name',
			'object_hierarchical',
			'table',
			'meta_table',
			'pod_table',
			'field_id',
			'field_index',
			'field_slug',
			'field_type',
			'field_parent',
			'field_parent_select',
			'meta_field_id',
			'meta_field_index',
			'meta_field_value',
			'pod_field_id',
			'pod_field_index',
			'object_fields',
			'join',
			'where',
			'where_default',
			'orderby',
			'pod',
			'recurse',
			'table_info',
			'attributes',
			'group',
			'grouped',
			'developer_mode',
			'dependency',
			'depends-on',
			'excludes-on'
		);

		$empties = array(
			'description',
			'alias',
			'help',
			'class',
			'pick_object',
			'pick_val',
			'sister_id',
			'required',
			'unique',
			'admin_only',
			'restrict_role',
			'restrict_capability',
			'hidden',
			'read_only',
			'object',
			'label_singular'
		);

		if ( isset( $pod[ 'options' ] ) ) {
			$pod = array_merge( $pod, $pod[ 'options' ] );

			unset( $pod[ 'options' ] );
		}

		foreach ( $pod as $option => $option_value ) {
			if ( in_array( $option, $options_ignore ) || null === $option_value ) {
				unset( $pod[ $option ] );
			}
			elseif ( in_array( $option, $empties ) && ( empty( $option_value ) || '0' == $option_value ) ) {
				if ( 'restrict_role' == $option && isset( $pod[ 'roles_allowed' ] ) ) {
					unset( $pod[ 'roles_allowed' ] );
				}
				elseif ( 'restrict_capability' == $option && isset( $pod[ 'capabilities_allowed' ] ) ) {
					unset( $pod[ 'capabilities_allowed' ] );
				}

				unset( $pod[ $option ] );
			}
		}

		if ( $fields ) {
			$pods_form = pods_form();
			$field_types = $pods_form::field_types();

			$field_type_options = array();

			foreach ( $field_types as $type => $field_type_data ) {
				$field_type_options[ $type ] = $pods_form::ui_options( $type );
			}

			foreach ( $pod[ 'fields' ] as &$field ) {
				if ( isset( $field[ 'options' ] ) ) {
					$field = array_merge( $field, $field[ 'options' ] );

					unset( $field[ 'options' ] );
				}

				foreach ( $field as $option => $option_value ) {
					if ( in_array( $option, $options_ignore ) || null === $option_value ) {
						unset( $field[ $option ] );
					}
					elseif ( in_array( $option, $empties ) && ( empty( $option_value ) || '0' == $option_value ) ) {
						if ( 'restrict_role' == $option && isset( $field[ 'roles_allowed' ] ) ) {
							unset( $field[ 'roles_allowed' ] );
						}
						elseif ( 'restrict_capability' == $option && isset( $field[ 'capabilities_allowed' ] ) ) {
							unset( $field[ 'capabilities_allowed' ] );
						}

						unset( $field[ $option ] );
					}
				}

				foreach ( $field_type_options as $type => $options ) {
					if ( $type == pods_v( 'type', $field ) ) {
						continue;
					}

					foreach ( $options as $option_data ) {
						if ( isset( $option_data[ 'group' ] ) && is_array( $option_data[ 'group' ] ) && !empty( $option_data[ 'group' ] ) ) {
							if ( isset( $field[ $option_data[ 'name' ] ] ) ) {
								unset( $field[ $option_data[ 'name' ] ] );
							}

							foreach ( $option_data[ 'group' ] as $group_option_data ) {
								if ( isset( $field[ $group_option_data[ 'name' ] ] ) ) {
									unset( $field[ $group_option_data[ 'name' ] ] );
								}
							}
						}
						elseif ( isset( $field[ $option_data[ 'name' ] ] ) ) {
							unset( $field[ $option_data[ 'name' ] ] );
						}
					}
				}
			}
		}
		else {
			unset( $pod[ 'fields' ] );
		}

		return (object) $pod;

	}

}
