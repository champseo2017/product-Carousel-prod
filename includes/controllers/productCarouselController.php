<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ProductCarouselController {
    private $model;
    
    public function __construct() {
        $this->model = new ProductCarouselModel();
        $this->cors = new Plugin_CORS(); // Initialize the CORS object
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes() {
        register_rest_route('productCarousel/v1', '/carousel/(?P<carouselId>\d+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'getCarouselProducts'],
            'permission_callback' => function($request) {
                return true;
            },
            'args' => [
                'carouselId' => [
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
                'productIds' => [
                    'required' => false,
                    'validate_callback' => function($param) {
                        // This pattern supports IDs like 307-1135363167-1707739987 or numeric IDs, separated by commas
                        return preg_match('/^([\d-]+,)*[\d-]+$/', $param) || empty($param);
                    },
                    'default' => ''
                ],
            ],
        ]);
    }

    public function getCarouselProducts($request) {
        $carouselId = $request['carouselId'];
        $productIds = $request['productIds'];
        $products = $this->model->listAllProductInCarousels($carouselId, $productIds);
        if (empty($products)) {
            return new WP_Error('no_products', 'No products found for the given carousel ID', ['status' => 200]);
        }
        return new WP_REST_Response($products, 200);
    }

    // ฟังก์ชันสำหรับเพิ่ม Carousel ใหม่
    public function addNewCarousel($title, $language = 'th') {
        
        // $result = $this->model->mockData();
        // return ['success' => "Done"];
        $result = $this->model->addCarousel($title, $language);
        
        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }

        $id = $result['id'];
        $new_title = $result['title'];

        return ['success' => "{$id} {$new_title}"];
        
    }


     // เมธอดสำหรับเพิ่มสินค้าใน Carousel
     public function addNewProductToCarousel($carouselId, $product_data) {

        // เรียกใช้เมธอดใน model เพื่อเพิ่มสินค้า
        $result = $this->model->addProductToCarousel($carouselId, $product_data);
        if (isset($result['error'])) {
            return $result;
        } else {
            return $result;
        }
    }

    public function addNewCarouselPage() {
        include plugin_dir_path( __FILE__ ) . '../views/addCarouselView.php';
    }

     // เมธอดสำหรับอัปเดตสินค้าใน Carousel
     public function updateExistingProductInCarousel($carouselId, $productId, $product_data) {
        // เรียกใช้เมธอดใน model เพื่ออัปเดตสินค้า
        $result = $this->model->updateProductToCarousel($carouselId, $productId, $product_data);
        if (isset($result['error'])) {
            return $result;
        } else {
            return $result;
        }
    }

    public function listCarouselsPage() {

        $language = isset($_GET['language']) ? $_GET['language'] : 'th';
        $page = max(1, isset($_GET['pg']) ? (int)$_GET['pg'] : 1);
        $perPage = max(10, isset($_GET['totalPage']) ? (int)$_GET['totalPage'] : 10);
    
        $result = $this->model->listCarousels($language, $page, $perPage);
        
        // ดึงข้อมูลที่ต้องการ 
        $data = $result['data']; 
        $total = $result['total'];
        $currentPage = $result['page'];
        $lastPage = $result['lastPage'];
        
        return [
            'success' => true,
            'data' => $data,
            'total' => $total,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
        ];
    
    }

    public function listProductInCarouselsPage() {

        $carouselId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $page = max(1, isset($_GET['pg']) ? (int)$_GET['pg'] : 1);
        $perPage = max(10, isset($_GET['totalPage']) ? (int)$_GET['totalPage'] : 10);
    
        $result = $this->model->listProductInCarousels($carouselId, $page, $perPage);
        
        // ดึงข้อมูลที่ต้องการ 
        $data = $result['data']; 
        $total = $result['total'];
        $currentPage = $result['page'];
        $lastPage = $result['lastPage'];
        
        return [
            'success' => true,
            'data' => $data,
            'total' => $total,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
        ];
    
    }

    public function listPage() {
        include plugin_dir_path( __FILE__ ) . '../views/listCarouselView.php';
    }

    public function viewProductInCarousels() {
        include plugin_dir_path( __FILE__ ) . '../views/listProductView.php';
    }

    public function deleteCarousel() {
        // ตรวจสอบว่ามีค่า carouselId ที่ส่งมาจาก POST หรือไม่
        if (isset($_POST['id'])) {
            $carouselId = $_POST['id'];
            $result = $this->model->deleteNonPublicCarousel($carouselId);
            return $result;
        } else {
            // ไม่พบ carouselId ในค่า POST
            return ['error' => 'Carousel ID is required.'];
        }
    }

    public function deleteProductInCarousel($carouselId, $productId) {
        if (isset($carouselId) && isset($productId)) {
            $result = $this->model->deleteProductDetails($carouselId, $productId);
            return $result;
        } else {
            return ['error' => 'Product ID is required.'];
        }
    }

    public function updateCarousel($carouselId, $title, $language, $status) {
        $result = $this->model->updateCarousel($carouselId, $title, $language, $status);

        if (isset($result['error'])) {
            return $result;
        } else {
            return $result;
        }
    }

    public function handleGetCarouselData($carouselId) {
        $result = $this->model->getCarouselData($carouselId);
        if (isset($result['error'])) {
            echo $result;
        } else if (isset($result['success'])) {
           return $result;
        }
    }

    public function updateCarouselPage() {
        include plugin_dir_path( __FILE__ ) . '../views/updateCarouselView.php';
    }

    public function addProductCarouselPage() {
        include plugin_dir_path( __FILE__ ) . '../views/addProductCarouselView.php';
    }

    public function editProductCarouselPage() {
        include plugin_dir_path( __FILE__ ) . '../views/editProductCarouselView.php';
    }

    public function displayProductInCarousel($carouselId, $productId) {
        return $this->model->displayProductInCarousel($carouselId, $productId);
    }

}