<?php
if (!defined('ABSPATH')) {
    exit;
}

$controller = new ProductCarouselController();
$successDelete = '';
$errorDelete = '';

// ตรวจสอบและจัดการคำขอ POST สำหรับการลบ Carousel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'deleteCarousel') {
    if (isset($_POST['id'])) {
        $deleteResult = $controller->deleteCarousel($_POST['id']);
        if (isset($deleteResult['error'])) {
            $errorDelete = $deleteResult['error'];
        } else {
            $successDelete = $deleteResult['success'];
        }
    }
}

$language = isset($_GET['language']) ? $_GET['language'] : 'th';
$page = max(1, isset($_GET['pg']) ? (int)$_GET['pg'] : 1);
$perPage = max(10, isset($_GET['totalPage']) ? (int)$_GET['totalPage'] : 10);

$result = $controller->listCarouselsPage();
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
                <label for="title">
                    เลือกภาษา
                </label>
            </div>
        
                <select name="language" onchange="window.location.href='?page=list-carousel&pg=1&language=' + this.value + '&totalPage=10'" class="global-selector">
                    <option value="th" <?php echo $language == 'th' ? 'selected' : ''; ?>>Thai</option>
                    <option value="en" <?php echo $language == 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="zh" <?php echo $language == 'zh' ? 'selected' : ''; ?>>Chinese</option>
                </select>
                <!-- ส่วนแสดงข้อมูลตาราง -->
                <table class="lestCarousel-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>STATUS</th>
                            <th>Date Created</th>
                            <th>Date Modified</th>
                            <th>LANGUAGE</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr>
                                <td colspan="8" class="data-not-found">Data not found</td>
                            </tr>
                        <?php else: ?>
                            <?php $rowNumber = ($currentPage - 1) * $perPage + 1; ?> <!-- คำนวณหมายเลขแถวเริ่มต้น -->
                            <?php foreach ($data as $item): ?>
                                <tr>
                                    <td><?php echo $rowNumber++; ?></td> <!-- แสดงหมายเลขแถวและเพิ่มขึ้นทีละหนึ่ง -->
                                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo ucfirst(strtolower(htmlspecialchars($item['status']))); ?></td>
                                    <td><?php echo htmlspecialchars($item['date_created']); ?></td>
                                    <td><?php echo htmlspecialchars($item['date_modified']); ?></td>
                                    <td><?php echo strtoupper(htmlspecialchars($item['language'])); ?></td>
                                    <td>
                                        <div class="action-container">
                                        <div class="action-button" >
                                         <!-- ปุ่ม Add Product ที่แต่ละ Carousel -->
                                         <a href="admin.php?page=add-product-to-carousel&id=<?php echo htmlspecialchars($item['id']); ?>" class="lestCarousel-btn-add">
                                        Add Product
                                        </a>
                                         <!-- ปุ่ม List Product Carousel -->
                                         <a href="admin.php?page=list-product-in-carousel&id=<?php echo htmlspecialchars($item['id']); ?>" class="lestCarousel-btn-productList">
                                            List Product in Carousel
                                        </a>
                                         </div>
                                         <div class="action-button">
                                             <!-- ปุ่ม Edit ที่แต่ละ Carousel -->
                                        <a href="admin.php?page=update-carousel&id=<?php echo htmlspecialchars($item['id']); ?>" class="lestCarousel-btn-edit">Edit</a>
                                        <!-- ปุ่ม Delete ที่แต่ละ Carousel -->
                                       <button class="lestCarousel-btn-delete" 
                                        onclick="showDeleteConfirmation(<?php echo htmlspecialchars($item['id']); ?>, '<?php echo htmlspecialchars($item['title']); ?>')"
                                        <?php echo ($item['status'] === 'public') ? 'disabled' : ''; ?>>
                                            Delete
                                        </button>
                                        </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if (!empty($data)): ?>
                    <div class="pagination-container">
                        <div class="pagination-total">
                            <!-- เพิ่มตัวเลือกสำหรับ totalPage -->
                            <div class="total-page-selector">
                                <select name="totalPage" onchange="window.location.href='?page=list-carousel&pg=1&language=<?php echo $language; ?>&totalPage=' + this.value;" class="page-size-selector">
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
                                <a href="?page=list-carousel&pg=<?php echo $currentPage - 1; ?>&language=<?php echo $language; ?>&totalPage=<?php echo $perPage; ?>" class="page-link">Previous</a>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $currentPage - 5);
                            $endPage = min($totalPages, $currentPage + 4);

                            if ($startPage > 1) {
                                echo '<a href="?page=list-carousel&pg=1&language='.$language.'&totalPage='.$perPage.'" class="page-link">1</a>';
                                echo '<span class="page-link">...</span>';
                            }

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                echo '<a href="?page=list-carousel&pg='.$i.'&language='.$language.'&totalPage='.$perPage.'" class="page-link '.($i == $currentPage ? 'active' : '').'">'.$i.'</a>';
                            }

                            if ($endPage < $totalPages) {
                                echo '<span class="page-link">...</span>';
                                echo '<a href="?page=list-carousel&pg='.$totalPages.'&language='.$language.'&totalPage='.$perPage.'" class="page-link">'.$totalPages.'</a>';
                            }
                            ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=list-carousel&pg=<?php echo $currentPage + 1; ?>&language=<?php echo $language; ?>&totalPage=<?php echo $perPage; ?>" class="page-link">Next</a>
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
            <p>คุณแน่ใจหรือไม่ที่จะลบ carousel นี้?</p>
            <div id="carouselDetails"></div> <!-- เพิ่มส่วนแสดงรายละเอียด carousel -->
            <form method="post" id="deleteForm">
                <input type="hidden" name="action" value="deleteCarousel">
                <input type="hidden" name="id" id="carouselIdToDelete">
                <button type="button" onclick="submitDeleteForm()" class="delete">Yes, Delete</button>
                <button type="button" onclick="closeDeleteConfirmation()" class="cancel">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function showDeleteConfirmation(carouselId, title) {
            document.getElementById('carouselIdToDelete').value = carouselId;
            document.getElementById('carouselDetails').innerHTML = `<p>ID: ${carouselId}</p><p>Title: ${title}</p>`;
            document.getElementById('deleteConfirmation').style.display = 'block';
        }

        function closeDeleteConfirmation() {
            document.getElementById('deleteConfirmation').style.display = 'none';
        }

        function submitDeleteForm() {
            document.getElementById('deleteForm').submit();
        }
    </script>