# Pods JSON API Access Filters

### Introduction
By default, all Pods-JSON-API endpoints and routes require authentication. In the `/pods/` route, access is granted automatically if the current user has the correct capabilities for the current route. For example, making a POST request to the `/pods/<pod-name>/` route to add an item requires authenticating as a user with the "pods_add_<pod-name>" capability. Currently, [this only applies to Advanced Content Type Pods](https://github.com/pods-framework/pods-json-api/issues/14).

Once a user connecting via the REST API is authenticated by one of the systems provided by [the core REST API project](https://github.com/rest-api) you can use the same permissions checks as you would in any other WordPress plugin. Because of this, you can You can grant access by route or by endpoint. You can also use your own authentication system to conditionally grant access to the routes or endpoints required for your use case.

There are two main strategies for implementing these filters:

1) Based on current user's capabilities:
    Check a current logged in user's capabilities, IE `if ( current_user_can( 'edit_posts' ) )`,  and grant access conditionally.
    
2) Globally.
    Grant or deny access to all users for specific endpoints/routes.

### The Basic Pattern
In the Pods JSON API uses a pattern of one global filter per route and one dynamically created filter for each endpoint. All of these filters return a boolean, called `$access`. If true, access is granted, if false, access is denied. 

The Pods route has a filter that applies to all endpoints in that route "pods_json_api_access_pods". The pods-api route has a similar filter "pods_json_api_access_pods_api" and the components route has the "pods_json_api_access_components" filter. So, for example, to lock out all users, authenticated or not from the pods-api route, you can use:

```php
add_filter( 'pods_json_api_access_pods_api', '__return_false' );
```

The filters mentioned above apply to all endpoints of the route. There is also one filter dynamically generated using the name of the method that is used to process the endpoint. For example, when making a GET request to the `pods/<pod-name>` endpoint, the method "get_items" is used. Therefore you can grant global access to this endpoint, for all Pods, like this:

```php
add_filter( 'pods_json_api_access_pods_get_items', '__return_true' );
```

Because these filters have the Pod name as their second argument, you can also grant or deny access by Pod. For example, to deny access to a Pod called "jedi" but grant for all others, you would use the following:

```php
add_filter( 'pods_json_api_access_pods_get_items', function( $access, $method, $pod ) {
    if ( 'jedi' == $pod ) {
        $access = false;
    }
    else {
        $access = true;
    }
    
    return $access;
 }, 10, 3 );
```

### Using Current User Permissions
As noted in the introduction, once authenticated, the current user's capabilities can be checked, just like in any other context. For this reason, <em>authentication alone does not categorically grant access to the routes added by this plugin</em>. Also, when working with standard WordPress content types, such as post types and taxonomies, the default WordPress permissions can be used.

For example, if you have a custom post type Pod, you may wish to grant global access to the Pods route, for all users who have the capability "edit_posts", like this:

```php
add_filter( 'pods_json_api_access_pods', function( $access ) {
    if ( current_user_can( 'edit_posts' ) {
        $access = true;
    }
  
    
    return $access;
 } );
```

In the Pods editor, for a custom post type Pod, you can enable the "user capabilities" option. This enables specific capabilities for the Pod. So, if your Pod was called "jedi", you could assign the "edit_jedi" capability to users. Then you can grant them 
ability to create and edit posts in the jedi post type via the API like this:

```php
add_filter( 'pods_json_api_access_pods', function( $access, $method, $pod ) {
    if ( 'jedi' == $pod && in_array( $method, array( 'save_item', 'add_item' ) ) && current_user_can( 'edit_jedi' ) {
        $access = true;
    }
  
    return $access;
 } );
```
