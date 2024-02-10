<?php
/**
* Plugin Name: Product Carousel
* Description: An elegant and dynamic product carousel plugin for showcasing items in an interactive slider format. Ideal for e-commerce websites, this plugin offers a sleek and user-friendly way to display products attractively.
* Version: 1.0
* Author: Champion | Champion of User-Centric Web Solutions
* Author URI: https://github.com/champseo2017/product-Carousel-prod
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('plugins_loaded', function() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/core/class-cors.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/controllers/menuController.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/controllers/productCarouselController.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/models/ProductCarouselModel.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/controllers/settingsController.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/helper/validation/carousel/carouselValidation.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/controllers/restApiController.php';

    $cors = new Plugin_CORS();
    $rest_api = new RestApiController();
    $menuPage = new MenuController();
});

function my_react_plugin_script() {
    $dir = plugin_dir_path( __FILE__ ) . 'dist/assets/';

    $script_files = scandir( $dir );
    foreach ( $script_files as $file ) {
        if ( preg_match( '/index.*\.js$/', $file ) ) {
            wp_enqueue_script( 'my-react-plugin-' . $file, plugin_dir_url( __FILE__ ) . 'dist/assets/' . $file, array( 'wp-element' ), filemtime( $dir . $file ), true );
        }
    }

    foreach ( $script_files as $file ) {
        if ( preg_match( '/index.*\.css$/', $file ) ) {
            wp_enqueue_style( 'my-react-plugin-style-' . $file, plugin_dir_url( __FILE__ ) . 'dist/assets/' . $file, array(), filemtime( $dir . $file ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'my_react_plugin_script' );


function product_carousel_shortCode($atts) {
    // Plugin_Logger::log_to_debug("product_carousel_shortCode" . json_encode($atts));
    $attributes = shortcode_atts(array(
        'carousel_id' => '0',
    ), $atts);

    return '<div id="my-react-app" data-carousel-id="' . esc_attr($attributes['carousel_id']) . '"></div>';
}
add_shortcode('product_carousel', 'product_carousel_shortCode');