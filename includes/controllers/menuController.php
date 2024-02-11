<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class MenuController {
    private $settings;
    public function __construct() {
        $this->settings = new SettingsController();
        $this->productCarousel = new ProductCarouselController();
        // ลงทะเบียน action ที่จะเพิ่มหน้าเมนูในแอดมิน gg
        add_action( 'admin_menu', array( $this, 'add_plugin_settings_page' ) );
    }

    public function add_plugin_settings_page() {
        // เพิ่มหน้าเมนูใหม่ในแอดมินของ WordPress
        add_menu_page(
            'How to Use', // ชื่อหน้าเมนูที่จะแสดงในแอดมิน
            'Product Carousel', // ข้อความที่แสดงในแท็บเมนู
            'manage_options', // ความสามารถที่ผู้ใช้ต้องมีเพื่อเข้าถึงหน้าเมนูนี้
            'how-to-use', // ชื่อเฉพาะของหน้าเมนู (slug)
            array( $this, 'display_how_to_use_page' ), // ฟังก์ชันที่จะเรียกเมื่อหน้าเมนูถูกแสดง
            'dashicons-admin-generic', // ไอคอนที่จะแสดงในเมนู
            20  // ตำแหน่งของเมนูในแอดมิน
        );
        add_submenu_page(
            'how-to-use',
            'Product Carousel Settings',
            'Product Carousel Settings',
            'manage_options',
            'domain-carousel-settings',
            array( $this, 'display_domain_settings_page' ),
        );
        // เพิ่ม submenu ที่ชื่อว่า "addNewCarousel"
        add_submenu_page(
            'how-to-use', // ชื่อ slug ของเมนูหลัก
            'Add New Carousel',        // ชื่อหน้าของ submenu
            'Add New Carousel',        // ข้อความที่แสดงในเมนู
            'manage_options',          // สิทธิ์ที่จำเป็นในการเข้าถึง submenu นี้
            'add-new-carousel',        // ชื่อ slug ของ submenu
            array( $this, 'display_add_new_carousel_page' ) // ฟังก์ชันที่จะเรียกเมื่อหน้า submenu ถูกแสดง
        );
         // เพิ่ม submenu สำหรับการแสดงรายการ Carousel
        add_submenu_page(
            'how-to-use',
            'List Carousel',
            'List Carousel',
            'manage_options',
            'list-carousel',
            array( $this, 'listCarouselsPage' )
        );
        // เพิ่ม submenu 'Update Carousel'
        add_submenu_page(
            null, // ทำให้ submenu นี้ไม่แสดงในเมนูแอดมิน
            'Update Carousel',  // ชื่อหน้าของ submenu
            'Update Carousel',   // ข้อความที่แสดงในเมนู (จะไม่ถูกแสดง)
            'manage_options',   // สิทธิ์ที่จำเป็นในการเข้าถึง submenu นี้
            'update-carousel', // ชื่อ slug ของ submenu
            array( $this, 'updateCarousel' ) // ฟังก์ชันที่จะเรียกเมื่อหน้า submenu ถูกแสดง
        );
        // เพิ่ม submenu 'Add product'
        add_submenu_page(
            null, // ทำให้ submenu นี้ไม่แสดงในเมนูแอดมิน
            'Add product',  // ชื่อหน้าของ submenu
            'Add product',   // ข้อความที่แสดงในเมนู (จะไม่ถูกแสดง)
            'manage_options',   // สิทธิ์ที่จำเป็นในการเข้าถึง submenu นี้
            'add-product-to-carousel', // ชื่อ slug ของ submenu
            array( $this, 'addProductToCarousel' ) // ฟังก์ชันที่จะเรียกเมื่อหน้า submenu ถูกแสดง
        );
        // เพิ่ม submenu 'Add product'
        add_submenu_page(
            null, // ทำให้ submenu นี้ไม่แสดงในเมนูแอดมิน
            'Edit product',  // ชื่อหน้าของ submenu
            'Edit product',   // ข้อความที่แสดงในเมนู (จะไม่ถูกแสดง)
            'manage_options',   // สิทธิ์ที่จำเป็นในการเข้าถึง submenu นี้
            'edit-product-to-carousel', // ชื่อ slug ของ submenu
            array( $this, 'editProductToCarousel' ) // ฟังก์ชันที่จะเรียกเมื่อหน้า submenu ถูกแสดง
        );
        // เพิ่ม submenu 'List product in carousel'
        add_submenu_page(
            null, // ทำให้ submenu นี้ไม่แสดงในเมนูแอดมิน
            'List product carousel',  // ชื่อหน้าของ submenu
            'List product carousel',   // ข้อความที่แสดงในเมนู (จะไม่ถูกแสดง)
            'manage_options',   // สิทธิ์ที่จำเป็นในการเข้าถึง submenu นี้
            'list-product-in-carousel', // ชื่อ slug ของ submenu
            array( $this, 'viewProductInCarousels' ) // ฟังก์ชันที่จะเรียกเมื่อหน้า submenu ถูกแสดง
        );
    }

    public function display_domain_settings_page() {
       return $this->settings->add_plugin_settings_page();
    }

    public function display_how_to_use_page() {
        return $this->settings->howToUsePage();
     }

    public function display_add_new_carousel_page() {
        return $this->productCarousel->addNewCarouselPage();
     }

     public function listCarouselsPage() {
        return $this->productCarousel->listPage();
     }

     public function updateCarousel() {
        return $this->productCarousel->updateCarouselPage();
     }

     public function addProductToCarousel() {
        return $this->productCarousel->addProductCarouselPage();
     }

     public function editProductToCarousel() {
        return $this->productCarousel->editProductCarouselPage();
     }

     public function viewProductInCarousels() {
        return $this->productCarousel->viewProductInCarousels();
     }
      
}
