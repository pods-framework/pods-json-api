<?php
/**
 * Class Pods_JSON_API_Pods_Components
 */
class Pods_JSON_API_Pods_Components {

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

		$routes[ '/pods-components' ] = array(
			array( array( $this, 'get_components' ), WP_JSON_Server::READABLE ),
			array( array( $this, 'package' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			array( array( $this, 'activate_components' ), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),


		);

		$routes[ '/pods-components/activate/(?P<component>[\w\-\_]+)' ] = array(
			array( array( $this, 'activate' ),      WP_JSON_Server::EDITABLE ),
			array( array( $this, 'deactivate' ),    WP_JSON_Server::DELETABLE ),
		);

		return $routes;

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
			$response->header( 'Location', json_url( '/pods-components?package' ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error importing package.', 'pods-json-api' ) );
		}

	}

	/**
	 * Activate a single component
	 *
	 * @param $component
	 *
	 * @since 0.2.0
	 *
	 * @return WP_Error|WP_JSON_ResponseInterface
	 */
	function activate( $component ) {

		if ( ! $this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}
		try {


			$components = array( 0 => $component );

			$id = $this->enable_components( $components, true );

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
			$response->header( 'Location', json_url( '/pods-components/activate/' . $component ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error activating component.', 'pods-json-api' ) );
		}

	}

	/**
	 * Deactivate a single component
	 *
	 * @param $component
	 *
	 * @since 0.2.0
	 *
	 * @return WP_Error|WP_JSON_ResponseInterface
	 */
	function deactivate( $component ) {
		if ( ! $this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}
		try {
			$components = array( 0 => $component );
			$id = $this->enable_components( $components, false );

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
			$response->header( 'Location', json_url( '/pods-components/activate/' . $component ) );

			return $response;
		}
		else {
			return new WP_Error( 'pods_json_api_error_' . __FUNCTION__,  __( 'Error deactivating components.', 'pods-json-api' ) );
		}

	}

	/**
	 * Activate and/or deactivate components
	 *
	 * @param array $data A multi-dimensional array containing a 'activate' and or 'deactivate' key, each of which should contain an array of Pods Component names.
	 *
	 * @since 0.2
	 *
	 * @return WP_Error|WP_JSON_ResponseInterface
	 */
	function activate_components( $data ) {

		if ( ! $this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}
		try {
			$id = 0;

			//activate components
			if ( ! is_null( $components = pods_v( 'activate', $data ) ) && is_array( $components ) ) {

				$id = $this->enable_components( $components, true );

			}

			//deactivate components
			if ( ! is_null( $components = pods_v( 'deactivate', $data ) ) && is_array( $components ) ) {

				$id = $this->enable_components( $data, false );

			}


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
			$response->header( 'Location', json_url( '/pods-components?activate_components' ) );

			return $response;
		}
		else {
			return null;
		}
	}

	/**
	 * Returns an array of all components. Key is component ID, value is true if active, false if inactive.
	 *
	 * @since 0.2.0
	 *
	 * @return mixed
	 */
	function get_components() {
		if ( ! $this->check_access( __FUNCTION__ ) ) {
			return new WP_Error( 'pods_json_api_restricted_error_' . __FUNCTION__, __( 'Sorry, you do not have access to this endpoint.', 'pods-json-api' ) );
		}

		$components = new PodsComponents();
		$components = $components->get_components();
		$components = array_keys( wp_list_pluck( $components, 'ID'  ));
		$active_components = get_option( 'pods_component_settings' );
		$active_components =  json_decode( $active_components );
		$active_components = pods_v( 'components', $active_components );
		if ( $active_components ) {
			$active_components = array_keys( (array) $active_components );
		}

		try {
			if ( ! is_null( $active_components ) ) {
				foreach ( $components as $component ) {

					if ( in_array( $component, $active_components ) ) {
						$response[ $component ] = true;
					} else {
						$response[ $component ] = false;
					}
				}
			} else {
				$response = 0;
			}

		}
		catch ( Exception $e ) {
			$response = new WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( $response instanceof WP_Error || !function_exists( 'json_ensure_response' ) ) {
			return $response;
		}
		elseif ( 0 < $response ) {
			$response = json_ensure_response( $response );
			$response->set_status( 201 );
			$response->header( 'Location', json_url( '/pods-components?get_components' ) );

			return $response;
		}
		else {

			return null;

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

		$access = apply_filters( 'pods_json_api_access_components_' . $method, $access, $method );
		$access = apply_filters( 'pods_json_api_access_components', $access, $method );

		return $access;

	}

	/**
	 * Enable or disable a component
	 *
	 * @since 0.2
	 *
	 * @access private
	 *
	 * @return bool True if option updated, false if not.
	 */
	private function enable_components( $components, $activate = true ) {
		$component_settings = PodsInit::$components->settings;
		if ( ! is_array( $components ) ) {
			return;
		}

		if ( ! isset( $component_settings[ 'components' ] ) ) {
			$component_settings = array( 'components' );
		}

		foreach( $components as $component ) {

			if ( $activate ) {
				$component_settings[ 'components' ][ $component ] = array ();
			}
			else {
				if ( is_array( $component ) ) {
					foreach ( $component as $c  ) {
						if ( isset( $component_settings[ 'components' ][ $c ] ) ) {
							unset( $component_settings[ 'components' ][ $c ] );
						}
					}
				}
				else {
					if ( isset( $component_settings[ 'components' ][ $component ] ) ) {
						unset( $component_settings[ 'components' ][ $component ] );
					}
				}



			}

		}

		return update_option( 'pods_component_settings', json_encode( $component_settings ) );

	}


}
