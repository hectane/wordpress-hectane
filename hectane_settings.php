<?php

/**
 * Manage settings for the plugin including the page that allows the connection
 * settings to be configured.
 */
class HectaneSettings {

    /**
     * Default values for all options.
     */
    private static $defaults = array(
        'host' => 'localhost',
        'port' => '8025',
        'tls' => '0',
        'tls_ignore' => '0',
        'username' => '',
        'password' => '',
    );

    /**
     * Obtain the current value for the specified option.
     */
    public static function get($name) {
        $o = get_option('hectane_settings');
        return isset($o[$name]) ? $o[$name] : self::$defaults[$name];
    }

    /**
     * Initialize the plugin by setting up hooks.
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_options_page'));
    }

    /**
     * Register the section and individual settings.
     */
    public function register_settings() {

        // Connection settings
        add_settings_section(
            'hectane_connection_settings',
            'Connection Settings',
            array($this, 'connection_settings_callback'),
            'hectane'
        );
        add_settings_field(
            'host',
            'Host',
            array($this, 'text_field_callback'),
            'hectane',
            'hectane_connection_settings',
            array('host')
        );
        add_settings_field(
            'port',
            'Port',
            array($this, 'text_field_callback'),
            'hectane',
            'hectane_connection_settings',
            array('port')
        );
        add_settings_field(
            'tls',
            'Use TLS',
            array($this, 'checkbox_field_callback'),
            'hectane',
            'hectane_connection_settings',
            array('tls')
        );

        // Authentication settings
        add_settings_section(
            'hectane_auth_settings',
            'Authentication Settings',
            array($this, 'auth_settings_callback'),
            'hectane'
        );
        add_settings_field(
            'username',
            'Username',
            array($this, 'text_field_callback'),
            'hectane',
            'hectane_auth_settings',
            array('username')
        );
        add_settings_field(
            'password',
            'Password',
            array($this, 'text_field_callback'),
            'hectane',
            'hectane_auth_settings',
            array('password')
        );


        // Behavior settings
        add_settings_section(
            'hectane_behavior_settings',
            'Behavior Settings',
            array($this, 'behavior_settings_callback'),
            'hectane'
        );
        add_settings_field(
            'tls_ignore',
            'Ignore TLS errors',
            array($this, 'checkbox_field_callback'),
            'hectane',
            'hectane_behavior_settings',
            array('tls_ignore')
        );


        register_setting(
            'hectane_settings',
            'hectane_settings'
        );
    }

    /**
     * Render the label for the connection section.
     */
    public function connection_settings_callback() {
        echo '<p>These settings are used to connect to the Hectane client.</p>';
    }

    /**
     * Render the label for the auth section.
     */
    public function auth_settings_callback() {
        echo '<p>These settings are used to authenticate with the Hectane server.</p>';
    }

    /**
     * Render the label for the behavior section.
     */
    public function behavior_settings_callback() {
        echo '<p>These settings are used to adjust behavior during transmission.</p>';
    }

    /**
     * Display the specified input field.
     */
    public function text_field_callback($args) {
        printf(
            '<input type="text" id="%s" name="hectane_settings[%s]" value="%s">',
            esc_attr($args[0]),
            esc_attr($args[0]),
            esc_attr(self::get($args[0]))
        );
    }

    /**
     * Display the specified checkbox field.
     */
    public function checkbox_field_callback($args) {
        printf(
            '<input type="checkbox" id="%s" name="hectane_settings[%s]" %s>',
            esc_attr($args[0]),
            esc_attr($args[0]),
            self::get($args[0]) ? 'checked="checked"' : ''
        );
    }

    /**
     * Add the options page to the admin menu.
     */
    public function add_options_page() {
        add_options_page(
            'Hectane Options',
            'Hectane',
            'manage_options',
            'hectane',
            array($this, 'display_options_page')
        );
    }

    /**
     * Display the page containing the options form.
     */
    public function display_options_page() {
        echo '<div class="wrap">';
        echo '<h2>Hectane Options</h2>';
        echo '<form method="post" action="options.php">';
        settings_fields('hectane_settings');
        do_settings_sections('hectane');
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

?>
