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
$carouselId = ValidationHelper::getSanitizedId('id');

$error = '';
$success = '';
$redirectUrl = '';

// Sanitize
$uri = ValidationHelper::sanitizeUriSafely('REQUEST_URI');

if (ValidationHelper::validateUri($uri)) {
    $safe_uri = ValidationHelper::escUrl($uri);
} else {
    $safe_uri = ValidationHelper::escUrl('admin.php?page=how-to-use');
}
$resultValidation = ValidationHelper::isPostRequestWithRequiredFields(['title', 'description', 'link', 'status', 'image_library_url']);
// Check if data is submitted via POST
if ($resultValidation === true) {
    if (!ValidationHelper::verifyNonce($_POST['add_product_nonce'], 'add_product_action')) {
        $error = 'Security check failed.';
    } else {
        
        $image_url = !empty($_POST['image_library_url']) ? ValidationHelper::escUrl($_POST['image_library_url']): '';

        $product_data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'link' => $_POST['link'],
            'image' => $image_url,
            'status' => $_POST['status']
        ];

        $sanitized_product_data = ValidationHelper::sanitizeProductData($product_data);
    
        if (empty($sanitized_product_data['link'])) {
            $error = 'Link is not a valid URL.';
        } else {
           
            $result = $controller->addNewProductToCarousel($carouselId, $sanitized_product_data);
    
            if (isset($result['error'])) {
                $error = $result['error'];
            } else {
                $success =  "ID: " . $result['id'] . ", Title: " . ValidationHelper::escHtml($result['title']);
                 // Set the URL for redirection and escape the URL before use
                $redirectUrl = ValidationHelper::escUrl(admin_url('admin.php?page=list-product-in-carousel&id=' . $carouselId));
            }
        }
    }
} else if (is_array($resultValidation) && isset($resultValidation['error'])) {
    $error = ValidationHelper::generateErrorMessage($resultValidation);
}

?>
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
            <?php AdminScriptStyle::performRedirection($redirectUrl, 800); ?>
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