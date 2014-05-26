<?php
/**
 * BuddyPress API Blogs.
 *
 * Blogs api endpoints.
 *
 * @package BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bp_api_blogs_init function.
 *
 * initializes api class for blogs and creates endpoints
 * 
 * @access public
 * @return void
 */
function bp_api_blogs_init() {
    global $bp_api_blogs;

    $bp_api_blogs = new BP_API_Blogs();
    add_filter( 'json_endpoints', array( $bp_api_blogs, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'bp_api_blogs_init' );


/**
 * BP_API_Blogs class.
 */
class BP_API_Blogs {

    public function register_routes( $routes ) {
        $routes['/buddypress/blogs'] = array(
            array( array( $this, 'get_blogs'), WP_JSON_Server::READABLE ),
            array( array( $this, 'create_blog'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/buddypress/blogs/(?P<id>\d+)'] = array(
            array( array( $this, 'get_blog'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_blog'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_blog'), WP_JSON_Server::DELETABLE ),
        );

        return $routes;
    }
    
    
	// response methods need filling
    public function get_blogs() {
	    return 'get blogs';
    }
    
    public function get_blog() {
	    return 'get blog';
    }

    public function create_blog() {
	    return 'create blog';
    }    

    public function edit_blog() {
	    return 'edit blog';
    }   
    
    public function delete_blog() {
	    return 'delete blog';
    }    
    
}