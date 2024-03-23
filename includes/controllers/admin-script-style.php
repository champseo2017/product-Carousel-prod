<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AdminScriptStyle {
    public function __construct() {
        // Hook into the admin_enqueue_scripts action to add custom styles and scripts
        add_action('admin_enqueue_scripts', array($this, 'addCarouselAssets'));
    }

    public function addCarouselAssets() {
        global $pagenow;
        // Target Page
        $targetPageAddProduct = 'add-product-to-carousel';
        $targetPageAddNewCarousel = 'add-new-carousel';
        // CSS file
        $cssFilePathAddProduct = 'views/css/addCarouselStyles.css';
        $cssFilePathAddNewCarousel = 'views/css/addCarouselStyles.css';
        // JavaScript file
        $jsFilePathAddProduct = 'views/js/addCarousel-script.js';


        $textDomain = 'text-domain'; // Text domain for localization

        // Localization data to pass to the script
        $localizationData = array(
            'imageSelectTitle' => __('Select or Upload Image', $textDomain),
            'imageUseButton' => __('Use this image', $textDomain),
        );

        // Check if we are on the 'admin.php' page and if the 'page' query parameter matches the target page
        if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] === $targetPageAddProduct) {
            // Enqueue the CSS file
            wp_enqueue_style('addCarouselStyles', plugins_url($cssFilePathAddProduct, dirname(__FILE__)));

            // Enqueue the JavaScript file
            wp_enqueue_script('addCarouselScript', plugins_url($jsFilePathAddProduct, dirname(__FILE__)), array('jquery'), null, true);

            // Optionally, pass PHP data to the script for localization
            wp_localize_script('addCarouselScript', 'adminScriptData', $localizationData);
        }
        // AddNewCarousel
        if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] === $targetPageAddNewCarousel) {
            // Enqueue the CSS file
            wp_enqueue_style('addCarouselStyles', plugins_url($cssFilePathAddNewCarousel, dirname(__FILE__)));
        }
    }

    public static function performRedirection($redirectUrl, $delay = 800) {
        echo "<script type='text/javascript'>
                window.setTimeout(function() {
                    window.location.href = '" . $redirectUrl . "';
                }, " . intval($delay) . ");
              </script>";
    }
}