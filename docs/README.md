#API Documentation

BuddyPress json API will not work on it's own. You need to have https://github.com/WP-API/WP-API/ as well as BuddyPress installed and activated. Please read all documentation on WP-API as we will follow the same conventions outlined by this API.

Since BuddyPress objects (groups, activity, messages etc.) are not WordPress custom post types we can't use some of the methods to retrieve data. BuddyPress json API is located at /wp-json/buddypress. All calls to return BuddyPress data must begin with this. 

#GET BuddyPress data 


/wp-json/buddypress/activity will return an unfiltered stream of BuddyPress activity.  
/wp-json/buddypress/activity/34 will return a single activity item.  
/wp-json/buddypress/activity/34/delete will delete this item.  


