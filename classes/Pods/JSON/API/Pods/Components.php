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
			array( array( $this, 'package' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON )
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

		$access = apply_filters( 'pods_json_api_access_components_' . $method, $access, $method );
		$access = apply_filters( 'pods_json_api_access_components', $access, $method );

		return $access;

	}


}
