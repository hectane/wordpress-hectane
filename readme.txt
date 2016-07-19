=== Hectane ===
Contributors: george_edison
Tags: mail, email
Requires at least: 3.0.1
Tested up to: 4.5.3
Stable tag: 0.1.6
License: MIT
License URI: https://opensource.org/licenses/MIT

Deliver all WordPress emails via Hectane.

== Description ==

This plugin provides an extremely easy way to route WordPress emails through [Hectane](https://github.com/hectane/hectane). After installing and configuring the plugin, all calls to `wp_mail()` will be intercepted and sent to Hectane which will take care of delivery.

== Installation ==

1. Upload the directory `hectane` to the `wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure the plugin through the 'Hectane' item in the Settings' menu.

== Changelog ==

= 0.1.6 =
* Split up settings
* Fixed header bug

= 0.1.5 =
* Additional MIME headers are passed to Hectane

= 0.1.4 =
* Extract "From" field from headers if present

= 0.1.3 =
* Plugin now auto-detects HTML

= 0.1.2 =
* Fixed bug preventing TLS from working
* Added option for ignoring TLS errors

= 0.1.1 =
* Plugin renamed to "Hectane" due to upstream change of name

= 0.1.0 =
* Initial release of plugin
