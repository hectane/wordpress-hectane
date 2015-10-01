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

/**
 * Override the default implementation of wp_mail().
 *
 * This function is responsible for marshalling the parameters into the JSON
 * data that is sent to go-cannon.
 */
function wp_mail($to, $subject, $message, $headers='', $attachments=array()) {

    $options = GoCannonOptions::get();

    // Build the JSON payload
    if(!is_array($to)) {
        $to = array($to);
    }
    $payload = json_encode(array(
        'from' => sprintf('Wordpress <wordpress@%s>', $_SERVER['SERVER_NAME']),
        'to' => is_array($to) ? $to : array($to),
        'subject' => $subject,
        'text' => $message,
    ));

    // Build the URL for the go-cannon API
    $u = $options->get_option('username');
    $p = $options->get_option('password');
    $url = sprintf(
        'http%s://%s%s:%s/v1/send',
        $options->get_option('tls') ? 's' : '',
        ($u && $p) ? ($u . ':' . $p . '@') : '',
        $options->get_option('host'),
        $options->get_option('port')
    );

    // Send the request and check the return code
    $context = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $payload,
        ),
    ));
    $result = json_decode(file_get_contents($url, false, $context), true);

    // Check the return value
    return $result !== null && is_array($result) && !isset($result['error']);
}

?>
