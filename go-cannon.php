<?php

/*
Plugin Name: go-cannon
Plugin URI: https://github.com/nathan-osman/wordpress-go-cannon
Description: Deliver all Wordpress emails via go-cannon.
Version: 0.1.0
Author: Nathan Osman
Author URI: https://quickmediasolutions.com
License: MIT
License URI: https://opensource.org/licenses/MIT
*/

require_once plugin_dir_path(__FILE__) . 'go-cannon_settings.php';

// Add the options
new GoCannonOptions();

?>
