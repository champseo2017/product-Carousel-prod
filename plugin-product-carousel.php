<?php
/**
* Plugin Name: Dynamic Showcase Carousel
* Description: Elevate your e-commerce experience with the Dynamic Showcase Carousel, designed to elegantly present your products in an interactive slider. This plugin is the perfect solution for any e-commerce site looking to enhance visual appeal and user engagement. With its sleek design and easy-to-use interface, showcasing your products has never been more attractive or effective.
* Version: 1.0
* Author: Champion | Champion of User-Centric Web Solutions
* Author URI: https://github.com/champseo2017/dynamic-showcase-carousel
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
            wp_enqueue_script( 'dynamic-showcase-carousel-' . $file, plugin_dir_url( __FILE__ ) . 'dist/assets/' . $file, array( 'wp-element' ), filemtime( $dir . $file ), true );
        }
    }

    foreach ( $script_files as $file ) {
        if ( preg_match( '/index.*\.css$/', $file ) ) {
            wp_enqueue_style( 'dynamic-showcase-carousel-style-' . $file, plugin_dir_url( __FILE__ ) . 'dist/assets/' . $file, array(), filemtime( $dir . $file ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'my_react_plugin_script' );


function product_carousel_shortCode($atts) {

    static $carousel_instance = 0;
    $carousel_instance++;

    $attributes = shortcode_atts(array(
        'carousel_id' => '0',
    ), $atts);

    $output = '<div id="my-react-app-' . $carousel_instance . '" data-carousel-id="' . esc_attr($attributes['carousel_id']) . '"></div>';

    return $output;
}
add_shortcode('product_carousel', 'product_carousel_shortCode');