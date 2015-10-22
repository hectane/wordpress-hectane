<?php

/*
Plugin Name: Hectane
Plugin URI: https://github.com/hectane/hectane
Description: Deliver all WordPress emails via Hectane.
Version: 0.1.2
Author: Nathan Osman
Author URI: https://quickmediasolutions.com
License: MIT
License URI: https://opensource.org/licenses/MIT
*/

require_once plugin_dir_path(__FILE__) . 'hectane_api.php';
require_once plugin_dir_path(__FILE__) . 'hectane_settings.php';

// Create the settings instance only if the admin is open
if(is_admin()) {
    new HectaneSettings();
}

// Only override the wp_mail() function if it doesn't exist
if (!function_exists('wp_mail')) {
    /**
     * Override the default implementation of wp_mail().
     *
     * This function is responsible for marshalling the parameters into the JSON
     * data that is sent to Hectane.
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
        return HectaneAPI::instance()->send($email);
    }
}

?>
