<?php

require_once plugin_dir_path(__FILE__) . 'hectane_settings.php';

/**
 * Facilitate communication with the Hectane API.
 */
class HectaneAPI {

    /**
     * Global instance of the API.
     */
    private static $instance;

    /**
     * Retrieve the global instance, creating it if necessary.
     */
    public static function instance() {
        if(self::$instance === null) {
            self::$instance = new HectaneAPI();
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
            HectaneSettings::get('tls') ? 's' : '',
            HectaneSettings::get('host'),
            HectaneSettings::get('port'),
            $method
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $u = HectaneSettings::get('username');
        $p = HectaneSettings::get('password');
        if($u !== '' && $p !== '') {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$u:$p");
        }
        if(HectaneSettings::get('tls_ignore')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
        echo '<p>The cURL PHP extension is required to use Hectane.</p>';
        echo '</div>';
    }

    /**
     * Attempt to send the specified email with Hectane.
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
     * Retrieve the current version of Hectane.
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
