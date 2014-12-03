Pods JSON API
===========

This is a plugin that implements the `pods` and `pods-api` routes for [WP-API](https://github.com/WP-API/WP-API).

It provides access to various methods in the Pods and Pods API classes in the [Pods Framework](http://pods.io).

[Slides from PodsCamp DFW 2014 presentation: "Introduction To The Pods JSON API"](http://www.slideshare.net/podsframework/introduction-to-the-pods-json-api)

### Requirements

* [WP-API](https://github.com/WP-API/WP-API) 1.0 or newer
* [PHP](http://php.net/) 5.3 or newer
* [WordPress](http://wordpress.org/) 3.9 or newer
* [Pods Framework](http://Pods.io)

### Resources
* [Authentication Filters For The Pods JSON API](https://github.com/pods-framework/pods-json-api/blob/master/authentication-filters.MD)
* [WP-API Getting Started Guide](https://github.com/WP-API/WP-API/blob/master/docs/guides/getting-started.md)
* [WP-API Docs](https://github.com/WP-API/WP-API/blob/master/docs/)
* [WP-API Console](https://github.com/WP-API/api-console)
* [oAuth Authentication](https://github.com/WP-API/OAuth1) Recomended For Production
* [Basic Authentication](https://github.com/WP-API/Basic-Auth) Recommended For Testing & Debugging

### Endpoints

#### Pods Data

`/pods/<pod>`

* `get_items` (READABLE | ACCEPT_JSON)
 * Get items from a Pod using find()
 * Uses JSON object as an array of find() $params
 * Returns an array of objects of data
* `add_item` (CREATABLE | ACCEPT_JSON)
 * Add an item to a Pod
 * Uses JSON object as an array of data to save
 * Success sends you to `get_item` URL

`/pods/<pod>/<item>`

* `get_item` (READABLE)
 * Get an item from a Pod
 * Returns object of data
* `save_item` (EDITABLE | ACCEPT_JSON)
 * Save an item from a Pod
 * Uses JSON object as an array of data to save
 * Success sends you to `get_item` URL
* `delete_item` (DELETABLE)
 * Delete an item from a Pod

`/pods/<pod>/<item>/duplicate`

* `duplicate_item` (CREATABLE | ACCEPT_JSON)
 * Duplicate an item from a Pod
 * Uses JSON object as an array of data to override save on the new item
 * Success sends you to `get_item` URL

#### Pods API

`/pods-api`

* `get_pods` (READABLE)
 * Get Pods registered, excluding fields
 * Returns an array of objects of data
* `add_pod` (CREATABLE | ACCEPT_JSON)
 * Add a Pod
 * Uses JSON object as an array of data to save

`/pods-api/<pod>`

* `get_pod` (READABLE)
 * Get a Pod, including fields
 * Returns object of data
* `save_pod` (EDITABLE | ACCEPT_JSON)
 * Save a Pod
 * Uses JSON object as an array of data to save
 * Success sends you to `get_pod` URL
* `delete_pod` (DELETABLE)
 * Delete a Pod

`/pods-api/<pod>/duplicate`

* `duplicate_pod` (CREATABLE | ACCEPT_JSON)
 * Duplicate a Pod
 * Uses JSON object as an array of data to override save on the new Pod
 * Success sends you to `get_pod` URL
* `reset_pod` (DELETABLE)
 * Reset a Pod's contents

`/pods-api/<pod>/update_rel`

* `update_rel` (EDITABLE | ACCEPT_JSON)
 * Update bi-directional relationships to correct sister IDs.
 * Uses JSON object as an array of data to set relationships. Must be in form documented below.
 * Success returns to a list of field IDs that were updated.

#### Pods Components
`/pods-components?package`

 * `package` (CREATABLE | ACCEPT_JSON)
  * Import a Pods Package
  * Requires that the Pods Migrate Package component be active on remote site.
  * Success returns true. Failure false.


`/pods-components?activate_components`

* `activate_components` (EDITABLE | ACCEPT_JSON)
 * Bulk activates and/ or deactivates components.
 * Takes a multi-dimensional array containing a 'activate' and or 'deactivate' key, each of which should contain an array of Pods Component names.
 * Use PUT request.
 * Success returns true. Failure false.

`/pods-components/activate/<component>`

*`activate`  (EDITABLE)
 * Activate a single component
 * Use POST request.
 * Success returns true. Failure false.

`/pods-components/activate/<component>`

*`deactivate`  (DELETABLE)
 * Deactivate a single component
 * Use DELETE request.
 * Success returns true. Failure false.

### Using AJAX
* Example code is being migrated to our code-library repository. 

See https://github.com/pods-framework/pods-code-library/tree/master/example/misc/json-api/examples for examples using AJAX.

### Passing Parameters To Methods
You can pass the same parameters to each method as you usually would in the methods `$parameters` array, when using the method via PHP, by appending variables to the URL.

##### Querying For Pods Items With GET Requests

For example, to do a `Pods::find()` query on the Pod 'soup' that was the equivalent of:

```php
    $params = array(
       'where' => 't.is_spicy = 1',
       'limit' => '7',
    );
$pods = pods( 'soup', $params );
```

You would use the url encoded equivalent of `soup/find?where=t.is_spicy%3D1&limit=7` by passing the values through `urlencode()`.

You can convert a PHP array, designed to be passed to `Pods::find()` or another method, to an encoded string, by first passing it through a foreach loop and encoding the values. For example, to create a long query, without manually encoding URLs, you could do:

```php
    $params = array(
        'where' => 'serves.meta_value = "four or more"',
        'limit' => '7',
        'orderby' => 't.post_title ASC'
    );

    $url = json_url( 'pods/soup?find=' );
    
    $url = http_build_query( $params );
```
The variable, `$url` now has the url to query the 'soup' Pod, using `Pods::find()` by the parameters set in the array.

In the above example, the method find is used to illustrate the point that all methods of the Pods and Pods_API class that are accessible via this plugin can be specified this way. It is not actually needed as find is the default method for GET requests, while add is the default method for PUT requests.

All that is actually needed to create a find request is:

```php
    $params = array(
        'where' => 'serves.meta_value = "four or more"',
        'limit' => '7',
        'orderby' => 't.post_title ASC'
    );

    $url = json_url( 'pods/soup' );

    $url = add_query_arg( $params, $url );
```

##### Updating Items With Post Requests
By default POST requests, sent to a Pods class endpoint will default to save_item. This allows for creating new items or updating existing items. In this example, a custom field--"home planet"--in anexisting item--Pod name "jedi", post ID 9--is being updated:

```php
    $data = array( 'home_planet' => 'Alderann' );
    $url = json_url( 'pods/jedi/9' );
    
    //This example uses the basic authentication plugin for authentication
    $headers    = array (
        'Authorization' => 'Basic ' . base64_encode( 'username' . ':' . 'password' ),
    );
 
    $response = wp_remote_post( $url, array (
                        'method' => 'POST',
						'headers'     => $headers,
                        'body' => json_encode( $data )
        )
    );
    
    //make sure response isn't an error
    if ( ! is_wp_error( $response )  ) {
    
        //show the updated post item
        var_dump( wp_remote_retrieve_body( $response ) );
    }
```

### Using The `update_rel` Endpoint
*Added in version 0.2

This endpoint is designed to address an issue that can occur when using the `add_pod` or `save_pod` methods with bi-directional relationship fields.

When these types of fields are updated/ created via the API, at the time of field creation/edit the sister IDs--the ID of the field in related Pod--can not be set if the related field does not yet exist and therefore does not have an ID. In addition, if the configuration is being copied from a remote site, the field IDs will be diffrent.

This endpoint is designed to be used to correct these errors <em>after</em> all Pods and Pods Fields are created. The data passed to it should be a multi-dimensional array, with each field represented like this:

    ```
    [pod_name_field_name] =>
        [from] =>
            [field_name] => 'field_name',
            [pod_name] => 'pod_name',
        [to] =>
             [field_name] => 'field_name',
             [pod_name] => 'pod_name',
    ```

### Importing a Pods Migrate Package
*Added in version 0.2

The pods-components endpoint allows for running a Pods Package import via the API. It can be used to import Pods, Pods Templates, Pods Pages and Pods Helpers. It requires that the Pods Migrate Package component be active on the remote site.

You can pass the Pods Package data--which is already in JSON format--to the body of a POST request.


### Activating & Deactivating Components
*Added in version 0.2
You may bulk activate and/ or deactivate components with a PUT request to the pods-packages endpoint. You may also activate and/ or deactivate components individually using the `pods-components/activate/<component>` endpoint, using a POST request to activate, or a DELETE request to deactivate.

The activate/deactivate methods return true, with a status of 201 if the option that stores active components was updated and false, with a status of 500, if it was not. You can get a list of all components and their activation status by making a GET request to the pods-component endpoint.

Get a list of all components and whether they are active or not:
```
        $url = json_url( 'pods-components' );

        //This example uses the basic authentication plugin for authentication
        $headers    = array (
            'Authorization' => 'Basic ' . base64_encode( 'admin' . ':' . 'password' ),
        );

        $response = wp_remote_post( $url, array (
                            'method'    => 'GET',
                            'headers'   => $headers,
            )
        );
```

Activate and/ or deactivate multiple components at once:
```
    $data = array(  'deactivate' => array( 'templates', 'table-storage' ), 'activate' => array( 'pages' ) );

	$url = json_url( 'pods-components?activate_components' );

    //This example uses the basic authentication plugin for authentication
    $headers    = array (
        'Authorization' => 'Basic ' . base64_encode( 'admin' . ':' . 'password' ),
    );


    $response = wp_remote_post( $url, array (
                        'method'    => 'PUT',
                        'headers'   => $headers,
						'body'      => json_encode( $data )
        )
    );
```
Activate one component:

```
    $url = json_url( 'pods-components/activate/table-storage' );

    //This example uses the basic authentication plugin for authentication
    $headers    = array (
        'Authorization' => 'Basic ' . base64_encode( 'admin' . ':' . 'password' ),
    );

    $response = wp_remote_post( $url, array (
                        'method'    => 'POST',
                        'headers'   => $headers,
        )
    );
```

Deactivate one component:

```
    $url = json_url( 'pods-components/activate/templates' );

    //This example uses the basic authentication plugin for authentication
    $headers    = array (
        'Authorization' => 'Basic ' . base64_encode( 'admin' . ':' . 'password' ),
    );

    $response = wp_remote_post( $url, array (
                        'method'    => 'DELETE',
                        'headers'   => $headers,
        )
    );
```
