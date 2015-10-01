<?php

/**
 * Manage options for the plugin including the page that allows the connection
 * settings to be configured.
 */
class GoCannonOptions {

    /**
     * Default values for all options.
     */
    private static $defaults = array(
        'host' => 'localhost',
        'port' => '8025',
        'tls' => '0',
        'username' => '',
        'password' => '',
    );

    /**
     * Obtain the current value for the specified option.
     */
    public static function get($name) {
        $o = get_option('go-cannon_settings');
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
        add_settings_section(
            'go-cannon_connection_settings',
            'Connection Settings',
            array($this, 'connection_settings_callback'),
            'go-cannon'
        );
        add_settings_field(
            'host',
            'Host',
            array($this, 'text_field_callback'),
            'go-cannon',
            'go-cannon_connection_settings',
            array('host')
        );
        add_settings_field(
            'port',
            'Port',
            array($this, 'text_field_callback'),
            'go-cannon',
            'go-cannon_connection_settings',
            array('port')
        );
        add_settings_field(
            'tls',
            'Use TLS',
            array($this, 'checkbox_field_callback'),
            'go-cannon',
            'go-cannon_connection_settings',
            array('tls')
        );
        add_settings_field(
            'username',
            'Username',
            array($this, 'text_field_callback'),
            'go-cannon',
            'go-cannon_connection_settings',
            array('username')
        );
        add_settings_field(
            'password',
            'Password',
            array($this, 'text_field_callback'),
            'go-cannon',
            'go-cannon_connection_settings',
            array('password')
        );
        register_setting(
            'go-cannon_settings',
            'go-cannon_settings'
        );
    }

    /**
     * Render the label for the connection section.
     */
    public function connection_settings_callback() {
        echo '<p>These settings are used to connect to the go-cannon client.</p>';
    }

    /**
     * Display the specified input field.
     */
    public function text_field_callback($args) {
        printf(
            '<input type="text" id="%s" name="go-cannon_settings[%s]" value="%s">',
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
            '<input type="checkbox" id="%s" name="go-cannon_settings[%s]" %s>',
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
            'go-cannon Options',
            'go-cannon',
            'manage_options',
            'go-cannon',
            array($this, 'display_options_page')
        );
    }

    /**
     * Display the page containing the options form.
     */
    public function display_options_page() {
        echo '<div class="wrap">';
        echo '<h2>go-cannon Options</h2>';
        echo '<form method="post" action="options.php">';
        settings_fields('go-cannon_settings');
        do_settings_sections('go-cannon');
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

?>
