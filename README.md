Pods JSON API
===========

This is a plugin that implements the `pods` and `pods-api` routes for [WP-API](https://github.com/WP-API/WP-API).

It provides access to various methods in the Pods and Pods API classes in the [Pods Framework](http://pods.io).

### Requirements

* [WP-API](https://github.com/WP-API/WP-API) 1.0 or newer
* [PHP](http://php.net/) 5.3 or newer
* [WordPress](http://wordpress.org/) 3.9 or newer
* [Pods Framework](http://Pods.io)

### Resources
* [WP-API Getting Started Guide](https://github.com/WP-API/WP-API/blob/master/docs/guides/getting-started.md)
* [WP-API Docs](https://github.com/WP-API/WP-API/blob/master/docs/)
* [WP-API Console](https://github.com/WP-API/api-console)
* [oAuth Authentication](https://github.com/WP-API/OAuth1) Recomended For Production
* [Basic Authentication](https://github.com/WP-API/Basic-Auth) Recomended For Testing & Debugging

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

 ### Passing Parameters To Methods
 You can pass the same parameters to each method as you usually would in the methods `$parameters` array, when using the method via PHP, by appending variables to the URL.

 For example, to do a `Pods::find()` query on the Pod 'soup' that was the equivalent of:

 ```php
     $params = array(
        'where' => 't.is_spicy = 1',
        'limit' => '7',
     );
 $pods = pods( 'soup', $params );
 ```

 You would use the url encoded equivalent of `soup/find?where="t.is_spicy=1"&limit="7"`. By passing the values through `urlencode()` we get `soup/find?where=t.is_spicy%3D1&limit=7`

You can convert a PHP array, designed to be passed to `Pods::find()` or another method, to an encoded string, by first passing it through a foreach loop and encoding the values. For example, to create a long query, without manually encoding URLs, you could do:

```php
    $params = array(
        'where' => 'serves.meta_value = "four or more"',
        'limit' => '7',
        'orderby' => 't.post_title ASC'
    );

    $url = site_url('/wp-json/soup/pods/find?');
    $count = count( $params );

    $i = 1;
    foreach( $params as $key => $value ) {
        $url .= $key.'='.urlencode( $value );
        if ( $i < $count ) {
            $url .= '&';
        }
        $i++;
    }
```
The variable, `$url` now has the url to query the 'soup' Pod, using `Pods::find()` by the parameters set in the array.
