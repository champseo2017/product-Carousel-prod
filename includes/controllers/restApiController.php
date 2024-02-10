<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class RestApiController {
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route( 'product-carousel/api/v1', '/testGet', array(
            'methods' => 'GET',
            'callback' => array( $this, 'handle_request' ),
            'permission_callback' => array( $this, 'permissions_check' )
        ));
    }

    public function handle_request($request) {
        return new WP_REST_Response('Hello World', 200);
    }

    public function permissions_check( $request ) {
        // ตรวจสอบสิทธิ์สำหรับคำขอ
        return true;
    }
}
