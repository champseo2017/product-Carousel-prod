<?php
if (!defined('ABSPATH')) {
    exit;
}

$controller = new ProductCarouselController();
$error = '';
$success = '';
$formData = ValidationHelper::isPostRequestWithRequiredFields(['title', 'language']);

$uri = ValidationHelper::sanitizeUriSafely('REQUEST_URI');
if (ValidationHelper::validateUri($uri)) {
    $safe_uri = ValidationHelper::escUrl($uri);
} else {
    $safe_uri = ValidationHelper::escUrl('admin.php?page=how-to-use');
}

if ($formData === true) {
    if (!ValidationHelper::verifyNonce($_POST['add_carousel_nonce'], 'add_carousel_action')) {
        $error = 'Security check failed.';
    } else {
        $title = ValidationHelper::sanitizeTextField($_POST['title']);
        $language = ValidationHelper::sanitizeTextField($_POST['language']);
        $result = $controller->addNewCarousel($title, $language);

        if (isset($result['error'])) {
            $error = $result['error'];
        } else if (isset($result['success'])) {
            $success = $result['success'];
            $redirectUrl = ValidationHelper::escUrl(admin_url('admin.php?page=list-carousel'));
        }

    }
} else if (is_array($formData) && isset($formData['error'])) {
    $error = ValidationHelper::generateErrorMessage($formData);
}
?>
    <div class="addCarousel-container">
        <h1 class="addCarousel-heading">Add New Product Carousel</h1>
        
        <?php if (!empty($error)): ?>
            <div class="addCarousel-error-container">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="addCarousel-success-container">
                <p><?php echo $success; ?></p>
            </div>
            <?php AdminScriptStyle::performRedirection($redirectUrl, 800); ?>
        <?php endif; ?>

        <form action="<?php echo $safe_uri; ?>" method="post" class="addCarousel-form-container">
        <?php wp_nonce_field('add_carousel_action','add_carousel_nonce'); ?>
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
