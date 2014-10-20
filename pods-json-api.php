<?php
/*
Plugin Name: Pods JSON API
Plugin URI: http://pods.io/
Description: JSON REST API for Pods
Version: 0.2.3
Author: Pods Framework Team
Author URI: http://pods.io/about/
Text Domain: pods-json-api
Domain Path: /languages/

Copyright 2009-2014  Pods Foundation, Inc  (email : contact@podsfoundation.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// API embedded in core (at some point)
if ( defined( 'PODS_JSON_API_VERSION' ) ) {
	return;
}

define( 'PODS_JSON_API_VERSION', '0.2.3' );
define( 'PODS_JSON_API_DIR', plugin_dir_path( __FILE__ ) );

// Include main class
include_once PODS_JSON_API_DIR . 'classes/Pods/JSON/API/Init.php';

// Include endpoints
add_action( 'init', array( 'Pods_JSON_API_Init', 'include_endpoints' ) );

// Setup endpoints
add_action( 'wp_json_server_before_serve', array( 'Pods_JSON_API_Init', 'add_endpoints' ) );

// Register activation/deactivation hooks
register_activation_hook( __FILE__, array( 'Pods_JSON_API_Init', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Pods_JSON_API_Init', 'deactivate' ) );

/**
 * pods_json_api_pods_required function.
 *
 * @access public
 * @return void
 */
function pods_json_api_pods_required() {

	echo sprintf( '<div id="message" class="error"><p>%s</p></div>',
		sprintf(
			__( '%1$s requires the Pods plugin to be installed/activated.', 'pods-json-api' ),
			'Pods JSON API' )
	);

}
