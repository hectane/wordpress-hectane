<?php

/*
Plugin Name: go-cannon
Plugin URI: https://github.com/nathan-osman/wordpress-go-cannon
Description: Deliver all WordPress emails via go-cannon.
Version: 0.1.0
Author: Nathan Osman
Author URI: https://quickmediasolutions.com
License: MIT
License URI: https://opensource.org/licenses/MIT
*/

require_once plugin_dir_path(__FILE__) . 'go-cannon_api.php';
require_once plugin_dir_path(__FILE__) . 'go-cannon_settings.php';

// Create the options instance only if the admin is open
if(is_admin()) {
    new GoCannonOptions();
}

// Only override the wp_mail() function if it doesn't exist
if (!function_exists('wp_mail')) {
    /**
     * Override the default implementation of wp_mail().
     *
     * This function is responsible for marshalling the parameters into the JSON
     * data that is sent to go-cannon.
     */
    function wp_mail($to, $subject, $message, $headers='', $attachments=array()) {
        if(!is_array($to)) {
            $to = array($to);
        }
        $email = array(
            'from' => sprintf('WordPress <wordpress@%s>', $_SERVER['SERVER_NAME']),
            'to' => is_array($to) ? $to : array($to),
            'subject' => $subject,
            'text' => $message,
        );
        return GoCannonAPI::instance()->send($email);
    }
}

?>
