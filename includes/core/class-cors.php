<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . './class-logger.php';
class Plugin_CORS {
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'handle_cors' ), 15 );
    }

    public function handle_cors() {
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        add_filter( 'rest_pre_serve_request', array( $this, 'cors_headers' ) );
    }

    public function cors_headers($value) {
        $allowed_domains = get_option('allowed_domains', []);
        
        // Determine the scheme (http or https) based on the presence of HTTPS in the server variables
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        
        // Prepare the current origin for comparison, adding scheme to HTTP_HOST
        $current_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : $scheme . $_SERVER['HTTP_HOST'];
        
        // Normalize allowed domains to include protocol for accurate comparison
        $normalized_allowed_domains = array_map(function($domain) use ($scheme) {
            if (!preg_match("/^http[s]?:\/\//", $domain)) {
                $domain = $scheme . $domain; // Default to using the detected scheme if no protocol is specified
            }
            return $domain;
        }, $allowed_domains);
        
        // Check if the current origin is in the list of allowed domains
        if (!in_array($current_origin, $normalized_allowed_domains)) {
            header('HTTP/1.1 401 Unauthorized');
            exit; // Stop further processing
        }
        
        // If in the list, set CORS headers
        header('Access-Control-Allow-Origin: ' . $current_origin);
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization');
        
        return $value;
    }
    
}
