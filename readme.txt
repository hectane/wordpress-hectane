=== go-cannon ===
Contributors: george_edison
Tags: mail, email
Requires at least: 3.0.1
Tested up to: 4.3.1
Stable tag: trunk
License: MIT
License URI: https://opensource.org/licenses/MIT

Deliver all WordPress emails via go-cannon.

== Description ==

This plugin provides an extremely easy way to route WordPress emails through [go-cannon](https://github.com/nathan-osman/go-cannon). After installing and configuring the plugin, all calls to `wp_mail()` will be intercepted and sent to go-cannon which will take care of delivery.

== Installation ==

1. Upload the directory `go-cannon` to the `wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure the plugin through the 'go-cannon' item in the Settings' menu.

== Changelog ==

= 0.1.0 =
* Initial release of plugin
