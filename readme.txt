=== Swarm API ===
Contributors: Swarmforce
Tags: swarms, Swarmforce, Swarm API
Donate link: 
Requires at least: 2.0.2
Tested up to: 2.0.2
Stable tag: 2.0.2
Plugin Name: Swarm API
Plugin URI: http://swarmapi.pbworks.com/WordPress-Plugin
Author: Swarmforce
Version: 1.0
Author URI: http://swarmapi.pbworks.com

Plugin is used for pulling swarm data. This data can be easily accessed outside of wordpress. You can use the REST interface for the Swarm API directly. See swarmapi.pbworks.com for documentation and code samples.



== Description ==

Allows for developers to easily pull swarm data from a designated swarm. Swarm data can currently be defined as tweets, photos, members, and links. For examples of what swarm data looks like, and how to use it see http://swarmapi.pbworks.com/Swarm-API-Examples



== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the entire swarmforce directory to the /wp-content/plugins/ directory
2. Log in as blog admin
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings > Swarm API to specify a swarm ID and other configuration options



== WP Plugin Code Sample ==

The plugin operates by swapping out a specific tag with the requested HTML. Specific tags include:

[sf:tweetview]
[sf:memberview]
[sf:linkview]
[sf:photoview]

The tweetview module is a current list of the most recent tweets in your swarm.
The memberview module is a current list of the top contributors in the swarm.
The linkview module is a list of current links in the swarm.
The photoview module returns a set of the top photos in the swarm.

So, to see swarm data in your blog, follow the installation instructions above, then create a new page, and try the following code:

[sf:tweetview]

This should get the tweet view for your designated swarm.



== Custom CSS ==

When viewing the the settings for the Swarm API, you'll see a "Custom CSS" option. This option specifies a path to a custom css style sheet, so you can style the divs returned by the Swarm API however you see fit. This path is relative to the /wp-content/plugins/ directory.



== Change Log ==

= 1.0 =
First release of WordPress Plugin. Got feedback? Please share: http://swarmapi.pbworks.com/Support