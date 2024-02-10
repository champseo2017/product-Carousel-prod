<?php
if (!defined('ABSPATH')) {
    exit;
}
$controller = new ProductCarouselController();

// ดึง ID ของ Carousel
$carouselId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$carouselData = [];

$error = '';
$success = '';
function fetchCarouselData($controller, $carouselId) {
    $carouselData = $controller->handleGetCarouselData($carouselId);
    if (isset($carouselData['error'])) {
        $error = $carouselData['error'];
        return [];
    }
    return $carouselData;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // จัดการการอัปเดต Carousel
    $result = $controller->updateCarousel($_POST['carouselId'], $_POST['title'], $_POST['language'], $_POST['status']);
    if (isset($result['error'])) {
        $error = $result['error'];
    } else if (isset($result['success'])) {
        $success = $result['success'];
        $carouselId = $_POST['carouselId'];
    }
}

// Fetch Carousel data after successful update or if carouselId is set
$carouselData = fetchCarouselData($controller, $carouselId);

// Check for error after fetching data
if (isset($carouselData['error'])) {
    $error = $carouselData['error'];
}

// ตรวจสอบและเตรียมข้อมูลสำหรับฟอร์ม
$title = isset($carouselData['data']['post_title']) ? $carouselData['data']['post_title'] : '';
$language = isset($carouselData['data']['language']) ? $carouselData['data']['language'] : '';
$status = isset($carouselData['data']['post_status']) ? $carouselData['data']['post_status'] : '';

?>
<head>
    <link href="<?php echo plugins_url('css/addCarouselStyles.css', __FILE__); ?>" rel="stylesheet">
</head>
    <div class="addCarousel-container">
        <h1 class="addCarousel-heading">แก้ไข Product Carousel</h1>
        
        <?php if (!empty($error)): ?>
            <div class="addCarousel-error-container">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="addCarousel-success-container">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="addCarousel-form-container">
            <input type="hidden" name="carouselId" value="<?php echo $carouselId; ?>">
            <div class="addCarousel-container-form">
                <label for="title" class="addCarousel-label">ชื่อ Carousel:</label>
                <input type="text" id="title" name="title" class="addCarousel-input" value="<?php echo $title; ?>" required>
            </div>


            <div class="addCarousel-container-form">
                <label for="status" class="addCarousel-label">สถานะ:</label>
                <select id="status" name="status" class="addCarousel-select">
                    <option value="draft" <?php echo $status == 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="public" <?php echo $status == 'public' ? 'selected' : ''; ?>>Public</option>
                </select>
            </div>

            <div class="addCarousel-container-form">
                <label for="language" class="addCarousel-label">ภาษา:</label>
                <select id="language" name="language" class="addCarousel-select">
                    <option value="th" <?php echo $language == 'th' ? 'selected' : ''; ?>>ไทย</option>
                    <option value="en" <?php echo $language == 'en' ? 'selected' : ''; ?>>อังกฤษ</option>
                    <option value="zh" <?php echo $language == 'zh' ? 'selected' : ''; ?>>จีน</option>
                </select>
            </div>

            <div class="addCarousel-container-button">
                <button type="submit" class="addCarousel-button">แก้ไข Carousel</button>
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
    });
    </script>
