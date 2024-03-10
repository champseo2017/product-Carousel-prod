<?php
/**
* Plugin Name: Dynamic Showcase Carousel
* Description: Elevate your e-commerce experience with the Dynamic Showcase Carousel, designed to elegantly present your products in an interactive slider. This plugin is the perfect solution for any e-commerce site looking to enhance visual appeal and user engagement. With its sleek design and easy-to-use interface, showcasing your products has never been more attractive or effective.
* Version: 1.0
* Author: Champion | Champion of User-Centric Web Solutions
* Author URI: https://github.com/champseo2017/product-Carousel-prod
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
    $script_dir = plugin_dir_path( __FILE__ ) . 'build/static/js/';
    $style_dir = plugin_dir_path( __FILE__ ) . 'build/static/css/';

    // Enqueue JS files
    $script_files = scandir( $script_dir );
    foreach ( $script_files as $file ) {
        if ( preg_match( '/main.*\.js$/', $file ) ) {
            wp_enqueue_script( 'dynamic-showcase-carousel-' . $file, plugin_dir_url( __FILE__ ) . 'build/static/js/' . $file, array(), filemtime( $script_dir . $file ), true );
        }
    }

    // Enqueue CSS files
    $style_files = scandir( $style_dir );
    foreach ( $style_files as $file ) {
        if ( preg_match( '/main.*\.css$/', $file ) ) {
            wp_enqueue_style( 'dynamic-showcase-carousel-style-' . $file, plugin_dir_url( __FILE__ ) . 'build/static/css/' . $file, array(), filemtime( $style_dir . $file ) );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'my_react_plugin_script' );


function product_carousel_shortCode($atts) {

    static $carousel_instance = 0;
    $carousel_instance++;

    $attributes = shortcode_atts(array(
        'carousel_id' => '0',
        'product_ids' => '',
    ), $atts);

    $output = '<div id="dynamic-showcase-' . $carousel_instance . '" data-carousel-id="' . esc_attr($attributes['carousel_id']) . '" data-product-ids="' . esc_attr($attributes['product_ids']) . '"></div>';

    return $output;
}
add_shortcode('product_carousel', 'product_carousel_shortCode');