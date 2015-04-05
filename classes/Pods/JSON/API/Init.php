<?php
/**
 * Class Pods_JSON_API_Init
 */
class Pods_JSON_API_Init {

	/**
	 * Array of endpoints to setup
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	private static $endpoints = array(
		'Pods_JSON_API_Pods',
		'Pods_JSON_API_Pods_API',
		'Pods_JSON_API_Pods_Components'
	);

	/**
	 * Public endpoint class objects
	 *
	 * @var array
	 * @access public
	 * @static
	 */
	public static $endpoint_objects = array();

	/**
	 * Include endpoints
	 *
	 * @access public
	 * @static
	 */
	public static function include_endpoints() {

		if ( !self::is_pods_installed() ) {
			add_action( 'all_admin_notices', 'pods_json_api_pods_required' );

			return;
		}

		foreach ( self::$endpoints as $class ) {
			include_once PODS_JSON_API_DIR . 'classes/' . str_replace( '_', '/', $class ) . '.php';
		}

	}

	/**
	 * Setup endpoints
	 *
	 * @access public
	 * @static
	 */
	public static function add_endpoints() {

		if ( !self::is_pods_installed() ) {
			return;
		}

		foreach ( self::$endpoints as $class ) {
			self::$endpoint_objects[ $class ] = new $class;

			add_filter( 'json_endpoints', array( self::$endpoint_objects[ $class ], 'register_routes' ) );
		}

	}

	/**
	 * Things to do when activating plugin
	 *
	 * @access public
	 * @static
	 */
	public static function activate() {



	}

	/**
	 * Things to do when deactivating plugin
	 *
	 * @access public
	 * @static
	 */
	public static function deactivate() {



	}

	/**
	 * Check if Pods is installed
	 *
	 * @return bool
	 *
	 * @access public
	 * @static
	 */
	public static function is_pods_installed() {

		// Check if Pods is active
		if ( defined( 'PODS_VERSION' ) ) {
			return true;
		}

		return false;
		
	}

}
