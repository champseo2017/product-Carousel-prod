<?php
require_once plugin_dir_path( __FILE__ ) . '../models/settingsModel.php';

class SettingsController {
    private $model;
    
    public function __construct() {
        $this->model = new SettingsModel();
        add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
    }

    public function add_plugin_settings_page() {
        include plugin_dir_path( __FILE__ ) . '../views/domainView.php';
    }

    public function register_plugin_settings() {
        register_setting( 'plugin-settings-group', 'allowed_domains', array(
            'type' => 'array',
            'sanitize_callback' => array($this->model, 'sanitize_allowed_domains')
        ));
    }
}
