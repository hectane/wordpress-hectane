<?php

require_once plugin_dir_path(__FILE__) . 'go-cannon_settings.php';

/**
 * Facilitate communication with the go-cannon API.
 */
class GoCannonAPI {

    /**
     * Global instance of the API.
     */
    private static $instance;

    /**
     * Retrieve the global instance, creating it if necessary.
     */
    public static function instance() {
        if(self::$instance === null) {
            self::$instance = new GoCannonAPI();
        }
        return self::$instance;
    }

    /**
     * Remember if the cURL PHP extension is installed.
     */
    private $have_curl = false;

    /**
     * Build a cURL handle for the specified API method.
     */
    private function build($method) {
        $url = sprintf(
            'http%s://%s:%s/v1%s',
            GoCannonOptions::get('secure') ? 's' : '',
            GoCannonOptions::get('host'),
            GoCannonOptions::get('port'),
            $method
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $u = GoCannonOptions::get('username');
        $p = GoCannonOptions::get('password');
        if($u !== '' && $p !== '') {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$u:$p");
        }
        return $ch;
    }

    /**
     * Check for the cURL extension.
     */
    public function __construct() {
        if(!extension_loaded('curl')) {
            add_action('admin_notices', array($this, 'show_error'));
        } else {
            $this->have_curl = true;
        }
    }

    /**
     * Show an error message for the cURL extension.
     */
    public function show_error() {
        echo '<div class="error">';
        echo '<p>The cURL PHP extension is required to use go-cannon.</p>';
        echo '</div>';
    }

    /**
     * Attempt to send the specified email with go-cannon.
     *
     * The $email parameter is expected to be an array with the same parameters
     * that /v1/send expects. A boolean is returned indicating success or
     * failure.
     */
    public function send($email) {
        if(!$this->have_curl) {
            return false;
        }
        $ch = $this->build('/send');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type' => 'application/json',
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email));
        $result = json_decode(curl_exec($ch), true);
        return $result !== null && is_array($result) && !isset($result['error']);
    }

    /**
     * Retrieve the current version of go-cannon.
     *
     * The version is returned as a string or the value "false" if an error
     * occurs while making the API call.
     */
    public function version() {
        if(!$this->have_curl) {
            return false;
        }
        $result = json_decode(curl_exec($this->build('/version')), true);
        if($result !== null && is_array($result) && isset($result['version'])) {
            return $result['version'];
        } else {
            return false;
        }
    }
}

?>
