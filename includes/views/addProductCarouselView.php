<?php
if (!defined('ABSPATH')) {
    exit;
}
$controller = new ProductCarouselController();
// $inspector = new ClassInspector('ValidationHelper');
// $inspector->printMethods(); // Display all methods of the ValidationHelper class
$screen = get_current_screen();
if ( $screen->id == "admin_page_add-product-to-carousel" ) { 
    wp_enqueue_media();
}

// Retrieve the ID of the Carousel
$carouselId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$error = '';
$success = '';
$redirectUrl = '';

// Sanitize
$uri = ValidationHelper::sanitizeUri('REQUEST_URI');

if (ValidationHelper::validateUri($uri)) {
    $safe_uri = ValidationHelper::escUrl($uri);
} else {
    $safe_uri = ValidationHelper::escUrl('admin.php?page=how-to-use');
}
$resultValidation = ValidationHelper::isPostRequestWithRequiredFields(['title', 'description', 'link', 'status', 'image_library_url']);
// Check if data is submitted via POST
if ($resultValidation === true) {
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['add_product_nonce'])), 'add_product_action')) {
        $error = 'Security check failed.';
    } else {
        
        $image_url = !empty($_POST['image_library_url']) ? esc_url_raw($_POST['image_library_url']) : '';

        $product_data = [
            // Sanitize และ Escape ข้อมูลที่รับมาเพื่อใช้ใน HTML หรือ Database
            'title' => sanitize_text_field($_POST['title']),
            'description' => sanitize_textarea_field($_POST['description']),
            'link' => esc_url_raw($_POST['link']),
            'image' => $image_url,
            'status' => sanitize_key($_POST['status']) // ใช้สำหรับค่าที่คาดว่าจะเป็นอักขระ, ตัวเลข, หรือ _
        ];
    
        // Validate ข้อมูลที่จำเป็นต้องมีความถูกต้องเฉพาะเจาะจง เช่น ตรวจสอบ URL หรือตัวเลข
        // ตัวอย่างการตรวจสอบ URL ให้เป็น URL ที่ถูกต้อง
        echo $product_data['link'];
        if (!filter_var($product_data['link'], FILTER_VALIDATE_URL)) {
            $error = 'Link is not a valid URL.';
            
        } else {
            // เรียกใช้เมธอด addNewProductToCarousel
            $result = $controller->addNewProductToCarousel($carouselId, $product_data);
    
            if (isset($result['error'])) {
                $error = $result['error'];
            } else {
                $success =  "ID: " . $result['id'] . ", Title: " . esc_html($result['title']);
                 // ตั้งค่า URL สำหรับเปลี่ยนเส้นทาง และ Escape URL ก่อนใช้งาน
                $redirectUrl = esc_url_raw(admin_url('admin.php?page=list-product-in-carousel&id=' . $carouselId));
            }
        }
    }
} else if (is_array($resultValidation) && isset($resultValidation['error'])) {
    if (!empty($resultValidation['missing_fields'])) {
        $error = 'Missing fields: ' . implode(', ', $resultValidation['missing_fields']) . "<br>";
    }
    if (!empty($resultValidation['empty_fields'])) {
        $error = 'Fields with empty values: ' . implode(', ', $resultValidation['empty_fields']) . "<br>";
    }
}

?>
<head>
    <link href="<?php echo plugins_url('css/addCarouselStyles.css', __FILE__); ?>" rel="stylesheet">
</head>
    <div class="addCarousel-container">
        <h1 class="addCarousel-heading">Add Product Carousel</h1>
        
        <?php if (!empty($error)): ?>
            <div class="addCarousel-error-container">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="addCarousel-success-container">
                <p><?php echo $success; ?></p>
            </div>
            <!-- <script type="text/javascript">
                setTimeout(function() {
                    window.location.href = "<?php echo $redirectUrl; ?>";
                }, 1200);
            </script> -->
        <?php endif; ?>

        <form action="<?php echo $safe_uri; ?>" method="post" enctype="multipart/form-data"  class="addCarousel-form-container">
        <?php wp_nonce_field('add_product_action','add_product_nonce'); ?>
        <div class="addCarousel-container-form">
                <label for="title" class="addCarousel-label">
                    ชื่อ สินค้า:
                </label>
                <input type="text" id="title" name="title" class="addCarousel-input" required>
        </div>
        <div class="addCarousel-container-form">
                <label for="description" class="addCarousel-label">
                    Description:
                </label>
                <textarea required id="description" class="addCarousel-textarea" name="description"></textarea>
        </div>
        <div class="addCarousel-container-form">
                <label for="link" class="addCarousel-label">
                    Product Link:
                </label>
                <input type="text" id="link" name="link" class="addCarousel-input" required>
        </div>
        <div class="addCarousel-container-form">
            <label for="status" class="addCarousel-label">
                Status:
            </label>
            <select id="status" name="status" class="addCarousel-select">
                <option value="draft" selected>Draft</option>
                <option value="public">Public</option>
            </select>
        </div>
        <!-- สร้างปุ่มเพื่อเปิดคลังสื่อ WordPress -->
        <div class="addCarousel-container-form">
            <label class="addCarousel-label">Image from Library:</label>
            <button type="button" id="select-image-library" class="button">Select Image</button>
            <input type="hidden" id="image-library-url" name="image_library_url">
            <div id="image-library-preview"></div>
        </div>
        <div class="addCarousel-container-button">
                <button type="submit" class="addCarousel-button">เพิ่ม สินค้า</button>
            </div>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        // กำหนดสถานะเมนู 'List Carousel' เป็นแอคทีฟ
        function setActiveMenu() {
            $('#toplevel_page_how-to-use').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
            $('#toplevel_page_how-to-use > a').addClass('wp-has-current-submenu');
            $('#toplevel_page_how-to-use li a[href$="page=list-carousel"]').parent().addClass('current');
        }

        setActiveMenu();

         // เปิดคลังสื่อ WordPress
        var frame;
        $('#select-image-library').click(function(e) {
                e.preventDefault();

                // สร้าง instance ของ media frame หากยังไม่มี
                if (!frame) {
                    frame = wp.media({
                        title: 'Select or Upload Image',
                        button: {
                            text: 'Use this image'
                        },
                        multiple: false  // อนุญาตให้เลือกรูปภาพได้เพียงอันเดียว
                    });

                    // จัดการเมื่อมีการเลือกรูปภาพ
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#image-library-url').val(attachment.url); // บันทึก URL ของรูปภาพที่เลือกไว้ในฟอร์ม
                        $('#image-library-preview').html('<img src="' + attachment.url + '" style="max-width: 200px; max-height: 200px;">'); // แสดงพรีวิว
                    });
                }

                // เปิดคลังสื่อ
                frame.open();
            });
    });

    </script>
