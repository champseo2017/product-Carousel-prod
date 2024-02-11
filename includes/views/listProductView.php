<?php
if (!defined('ABSPATH')) {
    exit;
}

$controller = new ProductCarouselController();
$successDelete = '';
$errorDelete = '';
$nonce = wp_create_nonce('delete_product_in_carousel_action');

// ตรวจสอบและจัดการคำขอ POST สำหรับการลบ Carousel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'deleteProductInCarousel') {
    // ตรวจสอบ nonce
    if (!isset($_POST['delete_nonce']) || !wp_verify_nonce($_POST['delete_nonce'], 'delete_product_in_carousel_action')) {
        die('Nonce validation failed');
    }
    if (isset($_POST['id']) && isset($_POST['carouselId'])) {
        $deleteResult = $controller->deleteProductInCarousel($_POST['carouselId'], $_POST['id']);
        if (isset($deleteResult['error'])) {
            $errorDelete = $deleteResult['error'];
        } else {
            $successDelete = $deleteResult['success'];
        }
    }
}

$carouselId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = max(1, isset($_GET['pg']) ? (int)$_GET['pg'] : 1);
$perPage = max(10, isset($_GET['totalPage']) ? (int)$_GET['totalPage'] : 10);

$result = $controller->listProductInCarouselsPage();
$jsonResult = json_encode($result);
$htmlContent = '';


if ($jsonResult !== false) {
    // แปลงกลับเป็นอาร์เรย์
    $decodedJson = json_decode($jsonResult, true);
    

    // var_dump($jsonResult);

    if (!empty($decodedJson['data'])) {
        foreach ($decodedJson['data'] as $item) {
            $htmlContent .= "<div class='card'>";
            $htmlContent .= "<h2> Title: " . htmlspecialchars($item['title']) . "</h2>";
            $htmlContent .= "<p>Language: " . strtoupper(htmlspecialchars($item['language'])) . "</p>";
            $htmlContent .= "<p>Status: " . htmlspecialchars($item['status']) . "</p>";
            $htmlContent .= "</div>";
        }
    }
}

$data = $result['data'];
$total = $result['total'];
$currentPage = $result['currentPage'];
$lastPage = $result['lastPage'];
$totalPages = ceil($total / $perPage);
$rowNumber = ($currentPage - 1) * $perPage + 1;
?>
<head>
    <link href="<?php echo plugins_url('css/lestCarouselStyles.css', __FILE__); ?>" rel="stylesheet">
    <link href="<?php echo plugins_url('css/global.css', __FILE__); ?>" rel="stylesheet">
</head>
    <div class="global-container">
        <h1 class="global-heading">List Product Carousel</h1>
        <?php if (!empty($errorDelete)): ?>
            <div class="global-error-container">
                <p><?php echo $errorDelete; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($successDelete)): ?>
            <div class="global-success-container">
                <p><?php echo $successDelete; ?></p>
            </div>
        <?php endif; ?>

        <div class="global-card-container">
            <div class="global-label">
                <?php echo isset($htmlContent) ? $htmlContent : ''; ?>
            </div>
            <div class="global-label">
                 <!-- ปุ่ม Add Product ที่แต่ละ Carousel -->
                 <a href="admin.php?page=add-product-to-carousel&id=<?php echo htmlspecialchars($carouselId); ?>" class="lestCarousel-btn-add">
                    Add Product
                  </a>
            </div>
                <!-- ส่วนแสดงข้อมูลตาราง -->
                <table class="lestCarousel-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Product Link</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data[0]['product_details'])): ?>
                            <tr>
                                <td colspan="8" class="data-not-found">Data not found</td>
                            </tr>
                        <?php else: ?>
                            <?php $rowNumber = ($currentPage - 1) * $perPage + 1; ?> <!-- คำนวณหมายเลขแถวเริ่มต้น -->
                            <?php foreach ($data as $item): ?>
                                <?php foreach ($item['product_details'] as $product): ?>
                                <tr>
                                    <td><?php echo $rowNumber++; ?></td> <!-- แสดงหมายเลขแถวและเพิ่มขึ้นทีละหนึ่ง -->
                                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td style="width: 10%;"><?php echo htmlspecialchars($product['title']); ?></td>
                                    <td style="width: 10%;"><?php echo htmlspecialchars($product['description']); ?></td>
                                    <td style="width: 10%;"><?php echo htmlspecialchars($product['link']); ?></td>
                                    <td><?php echo htmlspecialchars(strtoupper($product['status'])); ?></td>
                                    <td style="text-align: center;">
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" style="max-width: 100px;">
                                    </td>
                                    <td>
                                        <!-- ปุ่ม Add Product ที่แต่ละ Carousel -->
                                        <!-- <a href="admin.php?page=add-product-to-carousel&id=<?php echo htmlspecialchars($item['id']); ?>" class="lestCarousel-btn-add">
                                        Add Product
                                        </a> -->
                                       <!-- ปุ่ม Edit ที่แต่ละ Carousel -->
                                        <a href="admin.php?page=edit-product-to-carousel&carouselId=<?php echo htmlspecialchars($carouselId); ?>&productId=<?php echo htmlspecialchars($product['id']); ?>" class="lestCarousel-btn-edit">Edit</a>
                                        <!-- ปุ่ม Delete ที่แต่ละ Carousel -->
                                        <button class="lestCarousel-btn-delete"
                                        <?php echo ($product['status'] == 'public') ? 'disabled' : ''; ?>
                                        onclick="showDeleteConfirmation('<?php echo htmlspecialchars($carouselId); ?>', '<?php echo htmlspecialchars($product['id']); ?>', '<?php echo htmlspecialchars($product['title']); ?>')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if (!empty($data)): ?>
                    <div class="pagination-container">
                        <div class="pagination-total">
                            <!-- เพิ่มตัวเลือกสำหรับ totalPage -->
                            <div class="total-page-selector">
                                <select name="totalPage" onchange="window.location.href='?page=list-carousel&pg=1&totalPage=' + this.value;" class="page-size-selector">
                                    <option value="10" <?php echo $perPage == 10 ? 'selected' : ''; ?>>10 per page</option>
                                    <option value="25" <?php echo $perPage == 25 ? 'selected' : ''; ?>>25 per page</option>
                                    <option value="50" <?php echo $perPage == 50 ? 'selected' : ''; ?>>50 per page</option>
                                    <option value="100" <?php echo $perPage == 100 ? 'selected' : ''; ?>>100 per page</option>
                                </select>
                            </div>
                            <div class="total-count">Total Items: <?php echo $total; ?></div>             
                        </div>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=list-carousel&pg=<?php echo $currentPage - 1; ?>&totalPage=<?php echo $perPage; ?>" class="page-link">Previous</a>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $currentPage - 5);
                            $endPage = min($totalPages, $currentPage + 4);

                            if ($startPage > 1) {
                                echo '<a href="?page=list-carousel&pg=1&totalPage='.$perPage.'" class="page-link">1</a>';
                                echo '<span class="page-link">...</span>';
                            }

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                echo '<a href="?page=list-carousel&pg='.$i.'&totalPage='.$perPage.'" class="page-link '.($i == $currentPage ? 'active' : '').'">'.$i.'</a>';
                            }

                            if ($endPage < $totalPages) {
                                echo '<span class="page-link">...</span>';
                                echo '<a href="?page=list-carousel&pg='.$totalPages.'&totalPage='.$perPage.'" class="page-link">'.$totalPages.'</a>';
                            }
                            ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=list-carousel&pg=<?php echo $currentPage + 1; ?>&totalPage=<?php echo $perPage; ?>" class="page-link">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                 <?php endif; ?>
            
        </div>
    </div>
    <!-- Dialog ยืนยันการลบ -->
    <div id="deleteConfirmation" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteConfirmation()">&times;</span>
            <p>คุณแน่ใจหรือไม่ที่จะลบ Product นี้?</p>
            <div id="productInCarouselDetails"></div> <!-- เพิ่มส่วนแสดงรายละเอียด carousel -->
            <form method="post" id="deleteForm">
                <input type="hidden" name="delete_nonce" value="<?php echo $nonce; ?>">
                <input type="hidden" name="action" value="deleteProductInCarousel">
                <input type="hidden" name="carouselId" id="carouselIdToDelete">
                <input type="hidden" name="id" id="productInCarouselIdToDelete">
                <button type="button" onclick="submitDeleteForm()" class="delete">Yes, Delete</button>
                <button type="button" onclick="closeDeleteConfirmation()" class="cancel">Cancel</button>
            </form>
        </div>
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
        });

        function showDeleteConfirmation(carouselId, productId, title) {
            document.getElementById('carouselIdToDelete').value = carouselId;
            document.getElementById('productInCarouselIdToDelete').value = productId;
            document.getElementById('productInCarouselDetails').innerHTML = `<p>ID: ${productId}</p><p>Title: ${title}</p>`;
            document.getElementById('deleteConfirmation').style.display = 'block';
        }

        function closeDeleteConfirmation() {
            document.getElementById('deleteConfirmation').style.display = 'none';
        }

        function submitDeleteForm() {
            document.getElementById('deleteForm').submit();
        }
    </script>