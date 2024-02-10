<?php
if (!defined('ABSPATH')) {
    exit;
}

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new ProductCarouselController();
    $result = $controller->addNewCarousel($_POST['title'], $_POST['language']);

    if (isset($result['error'])) {
        $error = $result['error'];
    } else if (isset($result['success'])) {
        $success = $result['success'];
    }
}
?>
<head>
    <link href="<?php echo plugins_url('css/addCarouselStyles.css', __FILE__); ?>" rel="stylesheet">
</head>
    <div class="addCarousel-container">
        <h1 class="addCarousel-heading">เพิ่ม Product Carousel ใหม่</h1>
        
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
            <div class="addCarousel-container-form">
                <label for="title" class="addCarousel-label">
                    ชื่อ Carousel:
                </label>
                <input type="text" id="title" name="title" class="addCarousel-input" required>
            </div>
            <div class="addCarousel-container-form">
                <label for="language" class="addCarousel-label">
                    ภาษา:
                </label>
                <select id="language" name="language" class="addCarousel-select">
                    <option value="th">ไทย</option>
                    <option value="en">อังกฤษ</option>
                    <option value="zh">จีน</option>
                </select>
            </div>
            <div class="addCarousel-container-button">
                <button type="submit" class="addCarousel-button">
                    เพิ่ม Carousel
                </button>
            </div>
        </form>
    </div>
