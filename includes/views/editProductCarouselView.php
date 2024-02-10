<?php
if (!defined('ABSPATH')) {
    exit;
}
$controller = new ProductCarouselController();
$screen = get_current_screen();
// var_dump($screen->id);
    if ( $screen->id == "admin_page_edit-product-to-carousel" ) { 
        wp_enqueue_media();
    }

// ดึง ID ของ Carousel และ Product Id 
$carouselId = isset($_GET['carouselId']) ? intval($_GET['carouselId']) : 0;

$productId = isset($_GET['productId']) ? $_GET['productId'] : '';

$error = '';
$success = '';
$redirectUrl = '';
$product = [];

// สร้างตัวแปร controller และเรียกใช้งานเมธอด displayProductInCarousel
$result = $controller->displayProductInCarousel($carouselId, $productId);

// ตรวจสอบผลลัพธ์และกำหนดค่าให้กับตัวแปรสำหรับใช้งานในฟอร์ม
if (isset($result['success']) && $result['success']) {
    $product = $result['product'];
} else if (isset($result['error'])) {
    $error = $result['error'];
}

// ตรวจสอบว่ามีการส่งข้อมูลผ่าน POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['link'], $_POST['status'])) {

    // ตรวจสอบว่ามีการเลือกรูปภาพจากคลังสื่อหรือไม่ และตรวจสอบว่าสตริงไม่ว่างเปล่า
    $image_url = !empty($_POST['image_library_url']) ? $_POST['image_library_url'] : $product['image'];

    $product_data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'link' => $_POST['link'],
        'image' => $image_url,
        'status' => $_POST['status']
    ];

    // เรียกใช้เมธอด updateExistingProductInCarousel
    $result = $controller->updateExistingProductInCarousel($carouselId, $productId, $product_data);

    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        $success =  "ID: " . $result['id'] . ", Title: " . $result['title'];
         // ตั้งค่า URL สำหรับเปลี่ยนเส้นทาง
        $redirectUrl = admin_url('admin.php?page=list-product-in-carousel&id=' . $carouselId);
    }
}

?>
<head>
    <link href="<?php echo plugins_url('css/editCarouselStyles.css', __FILE__); ?>" rel="stylesheet">
</head>
    <div class="addCarousel-container">
        <h1 class="addCarousel-heading">Edit Product Carousel</h1>
        
        <?php if (!empty($error)): ?>
            <div class="addCarousel-error-container">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="addCarousel-success-container">
                <p><?php echo $success; ?></p>
            </div>
            <script type="text/javascript">
        setTimeout(function() {
            window.location.href = "<?php echo $redirectUrl; ?>";
        }, 3000);
    </script>
        <?php endif; ?>

        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data"  class="addCarousel-form-container">
        <div class="addCarousel-container-form">
                <label for="title" class="addCarousel-label">
                    ชื่อ สินค้า:
                </label>
                <input type="text" id="title" name="title" class="addCarousel-input" value="<?php echo isset($product['title']) ? $product['title'] : ''; ?>" required>
        </div>
        <div class="addCarousel-container-form">
                <label for="description" class="addCarousel-label">
                    Description:
                </label>
                <textarea rows="4" cols="50" required id="description" class="addCarousel-textarea" name="description"><?php echo isset($product['description']) ? $product['description'] : ''; ?></textarea>
        </div>
        <div class="addCarousel-container-form">
                <label for="link" class="addCarousel-label">
                    Product Link:
                </label>
                <input type="text" id="link" name="link" class="addCarousel-input" value="<?php echo isset($product['link']) ? $product['link'] : ''; ?>" required>
        </div>
        <div class="addCarousel-container-form">
            <label for="status" class="addCarousel-label">
                Status:
            </label>
            <select id="status" name="status" class="addCarousel-select">
                <option value="draft" <?php echo (isset($product['status']) && $product['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                <option value="public" <?php echo (isset($product['status']) && $product['status'] == 'public') ? 'selected' : ''; ?>>Public</option>
            </select>
        </div>
        <!-- สร้างปุ่มเพื่อเปิดคลังสื่อ WordPress -->
        <div class="addCarousel-container-form">
            <label class="addCarousel-label">Image from Library:</label>
            <button type="button" id="select-image-library" class="button">Select Image</button>
            <input type="hidden" id="image-library-url" name="image_library_url">
            <div id="image-library-preview"><?php if (isset($product['image'])): ?><img src="<?php echo $product['image']; ?>" style="max-width: 200px; max-height: 200px;"><?php endif; ?></div>
        </div>
        <div class="addCarousel-container-button">
                <button type="submit" class="addCarousel-button">แก้ไขสินค้า</button>
            </div>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        // กำหนดสถานะเมนู 'List Carousel' เป็นแอคทีฟ
        function setActiveMenu() {
            $('#toplevel_page_domain-carousel-settings').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu');
            $('#toplevel_page_domain-carousel-settings > a').addClass('wp-has-current-submenu');
            $('#toplevel_page_domain-carousel-settings li a[href$="page=list-carousel"]').parent().addClass('current');
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
