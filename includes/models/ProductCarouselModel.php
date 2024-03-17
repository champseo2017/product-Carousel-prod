<?php
if (!defined('ABSPATH')) {
    exit;
}

class ProductCarouselModel
{
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function addCarousel($title, $language = 'th')
    {
        // ตรวจสอบและทำความสะอาดข้อความใน title โดยใช้ CarouselValidation
        $title = CarouselValidation::validateText($title);

        // ตรวจสอบว่า title ไม่ว่างเปล่า
        if (!CarouselValidation::required($title)) {
            return ['error' => 'Title required field'];
        }

        // ตรวจสอบว่ารหัสภาษาที่ระบุถูกต้อง
        if (!CarouselValidation::validLanguage($language)) {
            return ['error' => 'Required languages: th, en, zh'];
        }

        // ตรวจสอบว่ามี Carousel ที่มี title เดียวกันในภาษานั้นอยู่แล้วหรือไม่
        if ($this->carouselExists($title, $language)) {
            return ['error' => 'Carousel with this name in the specified language already exists.'];
        }

        // สร้างอาร์เรย์ข้อมูลสำหรับการสร้างโพสต์ใหม่
        $postarr = array(
            'post_title' => $title, // ชื่อโพสต์
            'post_content' => '', // เนื้อหาโพสต์ (ว่างเปล่า)
            'post_status' => 'draft', // สถานะโพสต์เป็นร่าง (draft)
            'post_type' => 'product_carousel', // ประเภทโพสต์เป็น 'product_carousel'
        );

        // เพิ่มโพสต์ใหม่ในฐานข้อมูล WordPress และคืนค่า ID ของโพสต์
        $post_id = wp_insert_post($postarr);

        // ตรวจสอบว่าการเพิ่มโพสต์มีข้อผิดพลาดหรือไม่
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'language', $language);
            $post = get_post($post_id);
            $title = $post->post_title;
            return [
                'success' => true,
                'id' => $post_id,
                'title' => $title,
            ];
        } else {
            return ['error' => 'Unable to add Carousel'];
        }
    }

    public function addProductToCarousel($carouselId, $product_data) {
    
        $existingProductsMeta = get_post_meta($carouselId, 'product_details', true);
        $existingProductsMeta = stripslashes($existingProductsMeta); // ลบการ escape อักขระ
        
        // ตรวจสอบว่า existingProductsMeta ว่างหรือเป็น null
        if (empty($existingProductsMeta)) {
            $existingProducts = [];
        } else {
            $existingProducts = json_decode($existingProductsMeta, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // จัดการข้อผิดพลาดในกรณีที่ json_decode ล้มเหลว
                return ['error' => 'Failed to decode product details: ' . json_last_error_msg()];
            }
        }

        $image_url = $product_data['image'];
        // สร้าง ID ที่ไม่ซ้ำกันสำหรับผลิตภัณฑ์ โดยรวมกับ Unix timestamp
        $uniqueId = $carouselId . '-' . rand() . '-' . time();

        // สร้างผลิตภัณฑ์
        $productDetails = array(
            'id' => $uniqueId, // เพิ่ม ID ที่ไม่ซ้ำกัน
            'title' => $product_data['title'],
            'description' => $product_data['description'],
            'image' => $image_url,
            'link' => $product_data['link'],
            'status' => isset($product_data['status']) ? $product_data['status'] : 'draft'
        );

        // เพิ่มผลิตภัณฑ์ใหม่
        $existingProducts[] = $productDetails;

        // อัปเดต Carousel
        $result = update_post_meta($carouselId, 'product_details', json_encode($existingProducts, JSON_UNESCAPED_UNICODE));

        if ($result) {
            return [
                'success' => true,
                'id' => $carouselId,
                'title' => $product_data['title'],
            ];
        } else {
            return ['error' => 'Unable to add product to Carousel'];
        }
    }

    public function updateProductToCarousel($carouselId, $productId, $product_data) {

        $existingProductsMeta = get_post_meta($carouselId, 'product_details', true);
        $existingProductsMeta = stripslashes($existingProductsMeta); // ลบการ escape อักขระ
        $existingProducts = json_decode($existingProductsMeta, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // จัดการข้อผิดพลาดในกรณีที่ json_decode ล้มเหลว
            return ['error' => 'Failed to decode product details: ' . json_last_error_msg()];
        }

        // อัปเดตรูปภาพหากมีการอัปโหลดใหม่
        $image_url = $product_data['image'];
       
        // ค้นหาและอัปเดตผลิตภัณฑ์
        $updated = false;
        foreach ($existingProducts as &$product) {
            if ($product['id'] === $productId) {
                $product['title'] = $product_data['title'];
                $product['description'] = $product_data['description'];
                $product['image'] = $image_url;
                $product['link'] = $product_data['link'];
                $product['status'] = $product_data['status'];
                $updated = true;
                break;
            }
        }
        unset($product); // ยกเลิกการอ้างอิงเพื่อความปลอดภัย

        if ($updated) {
            // อัปเดต Carousel ด้วยข้อมูลผลิตภัณฑ์ที่อัปเดต
            $result = update_post_meta($carouselId, 'product_details', json_encode($existingProducts, JSON_UNESCAPED_UNICODE));
            
            if ($result) {
                return [
                    'success' => true,
                    'id' => $carouselId,
                    'title' => $product_data['title'],
                ];
            } else {
                return ['error' => 'Unable to update product in Carousel'];
            }
        } else {
            return ['error' => 'Product not found in Carousel'];
        }
    }

    private function handleImageUpload() {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
    
            // ตรวจสอบและเปลี่ยนชื่อไฟล์หากเป็นภาษาไทย
            $filename = $_FILES['image']['name'];
            if (preg_match('/[ก-๙]/', $filename)) {
                // สร้างชื่อไฟล์ใหม่เป็นภาษาอังกฤษหรือตัวเลข
                $newFilename = 'image_' . uniqid() . '_' . mt_rand(1000, 9999) . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                $_FILES['image']['name'] = $newFilename;
            }
    
            $attachment_id = media_handle_upload('image', 0);
            
            if (is_wp_error($attachment_id)) {
                error_log('Error uploading image: ' . $attachment_id->get_error_message());
                return ''; // กำหนดค่าเริ่มต้นหากไม่สามารถอัปโหลดได้
            } else {
                return wp_get_attachment_url($attachment_id);
            }
        }
    
        return ''; // กำหนดค่าเริ่มต้นหากไม่มีการอัปโหลด
    }
    
    public function updateCarousel($carouselId, $title, $language, $status) {
        $title = CarouselValidation::validateText($title);
        $language = CarouselValidation::validateText($language);
        $status = CarouselValidation::validateText($status);
    
        if (get_post_type($carouselId) !== 'product_carousel') {
            return ['error' => 'Invalid carousel ID'];
        }
    
        $current_time = current_time('mysql');
        $gmt_time = current_time('mysql', 1);
    
        // อัปเดตชื่อ Carousel และวันที่แก้ไข
        $this->wpdb->update(
            $this->wpdb->posts, 
            ['post_title' => $title, 'post_modified' => $current_time, 'post_modified_gmt' => $gmt_time], 
            ['ID' => $carouselId]
        );
    
        // อัปเดตภาษาในเมตาดาต้า
        update_post_meta($carouselId, 'language', $language);
    
        // อัปเดตสถานะ Carousel และวันที่แก้ไข
        $this->wpdb->update(
            $this->wpdb->posts, 
            ['post_status' => $status, 'post_modified' => $current_time, 'post_modified_gmt' => $gmt_time], 
            ['ID' => $carouselId]
        );
    
        // ตรวจสอบข้อผิดพลาด
        if ($this->wpdb->last_error) {
            return ['error' => 'Update failed'];
        }
    
        return ['success' => 'Update Success'];
    }
    
    public function mockData($numberOfItems = 50) {
        $mockedItems = [];
    
        for ($i = 0; $i < $numberOfItems; $i++) {
            $title = 'Test Carousel ' . mt_rand(1000, 9999); // สร้างชื่อที่มีค่าสุ่ม
            $language = 'th'; // ตั้งค่าภาษาเป็น 'th'
    
            $result = $this->addCarousel($title, $language);
    
            if (!isset($result['error'])) {
                $mockedItems[] = $result;
            }
        }
    
        return $mockedItems;
    }
    
    private function carouselExists($title, $language)
    {
        $args = array(
            'post_type' => 'product_carousel',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'language',
                    'value' => $language,
                ),
            ),
            'posts_per_page' => 1,
            'title' => $title, // ตรวจสอบ title
        );

        $query = new WP_Query($args);

        return $query->have_posts();
    }

    public function listCarousels($language, $page = 1, $perPage = 10) {

        $offset = ($page - 1) * $perPage;
        // สร้างคำสั่ง SQL สำหรับดึงข้อมูล Carousel
        $query = "
            SELECT {$this->wpdb->posts}.*, wp_postmeta.meta_value AS 'language' 
            FROM {$this->wpdb->posts} 
            INNER JOIN wp_postmeta ON {$this->wpdb->posts}.ID = wp_postmeta.post_id AND wp_postmeta.meta_key = 'language'
            WHERE {$this->wpdb->posts}.post_type = 'product_carousel' AND wp_postmeta.meta_value = %s
            ORDER BY {$this->wpdb->posts}.post_date DESC
            LIMIT %d, %d
        ";

        // เตรียมคำสั่ง SQL โดยใส่ค่าแทนที่ placeholders (%s, %d, %d) ด้วยค่าที่ปลอดภัย
        // %s สำหรับภาษา, %d สำหรับตำแหน่งเริ่มต้น (offset) และ %d สำหรับจำนวนโพสต์ต่อหน้า (perPage)
        $prepared_query = $this->wpdb->prepare($query, $language, $offset, $perPage);
        // ดึงข้อมูลจากฐานข้อมูลตามคำสั่งที่เตรียมไว้
        $posts = $this->wpdb->get_results($prepared_query);

        // จัดการข้อมูลโพสต์
        $carousels = [];
        foreach ($posts as $post) {
            $carousels[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'status' => $post->post_status,
                'date_created' => $post->post_date,
                'date_modified' => $post->post_modified,
                'language' => $post->language,
            ];
        }

        // นับจำนวนโพสต์ทั้งหมด
        // ปรับแก้ SQL query เพื่อนับจำนวนโพสต์ตามภาษา
        $total_query = "
            SELECT COUNT(*) 
            FROM {$this->wpdb->posts} 
            INNER JOIN wp_postmeta ON {$this->wpdb->posts}.ID = wp_postmeta.post_id 
            WHERE {$this->wpdb->posts}.post_type = 'product_carousel' 
            AND wp_postmeta.meta_key = 'language' 
            AND wp_postmeta.meta_value = %s
        ";
        $total = $this->wpdb->get_var($this->wpdb->prepare($total_query, $language));

        return [
            'data' => $carousels,
            'total' => $total,
            'page' => $page,
            'lastPage' => ($perPage > 0) ? ceil($total / $perPage) : 0,
        ];
    }

    public function listAllProductInCarousels($carouselId, $productIds = '') {
        // Plugin_Logger::log_to_debug("listAllProductInCarousels carouselId" . json_encode($carouselId));
    
        $carousel_query = "
            SELECT p.*, lang.meta_value AS 'language'
            FROM {$this->wpdb->posts} p
            LEFT JOIN {$this->wpdb->postmeta} lang ON p.ID = lang.post_id AND lang.meta_key = 'language'
            WHERE p.post_type = 'product_carousel' AND p.ID = %d AND p.post_status = 'public'
        ";
        $carousel_prepared_query = $this->wpdb->prepare($carousel_query, $carouselId);
        $carousel_data = $this->wpdb->get_results($carousel_prepared_query);
    
        $product_details_query = "
            SELECT meta_value
            FROM {$this->wpdb->postmeta}
            WHERE post_id = %d AND meta_key = 'product_details'
        ";
        $product_details_prepared_query = $this->wpdb->prepare($product_details_query, $carouselId);
        $product_details_result = $this->wpdb->get_results($product_details_prepared_query);
    
        $product_ids_array = strlen(trim($productIds)) > 0 ? explode(',', $productIds) : [];
    
        $product_details = [];
        foreach ($product_details_result as $item) {
            $details = json_decode($item->meta_value, true);
            if (is_array($details)) {
                $filtered_details = empty($product_ids_array) ? $details : array_filter($details, function ($product) use ($product_ids_array) {
                    return in_array($product['id'], $product_ids_array) && isset($product['status']) && $product['status'] == 'public';
                });
                $product_details = array_merge($product_details, $filtered_details);
               
            }
        }
    
        $carousels = [];
        foreach ($carousel_data as $carousel) {
            $carousels[] = [
                'id' => $carousel->ID,
                'title' => $carousel->post_title,
                'status' => $carousel->post_status,
                'date_created' => $carousel->post_date,
                'date_modified' => $carousel->post_modified,
                'language' => $carousel->language,
                'product_details' => $product_details
            ];
        }
       
        return $carousels;
    }
    
    public function listProductInCarousels($carouselId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
    
        // Query 1: ดึงรายละเอียดของ Carousel และภาษา
        $carousel_query = "
            SELECT p.*, lang.meta_value AS 'language'
            FROM {$this->wpdb->posts} p
            LEFT JOIN {$this->wpdb->postmeta} lang ON p.ID = lang.post_id AND lang.meta_key = 'language'
            WHERE p.post_type = 'product_carousel' AND p.ID = %d
        ";
        $carousel_prepared_query = $this->wpdb->prepare($carousel_query, $carouselId);
        $carousel_data = $this->wpdb->get_results($carousel_prepared_query);
    
        // Query 2: ดึง product_details
        $product_details_query = "
            SELECT meta_value
            FROM {$this->wpdb->postmeta}
            WHERE post_id = %d AND meta_key = 'product_details'
        ";
        $product_details_prepared_query = $this->wpdb->prepare($product_details_query, $carouselId);
        $product_details_result = $this->wpdb->get_results($product_details_prepared_query);
    
        // จัดรูปแบบ product_details
        $product_details = [];
        foreach ($product_details_result as $item) {
            $stripped_value = stripslashes($item->meta_value);
            $details = json_decode($stripped_value, true);
            if (is_array($details)) {
                $product_details = array_merge($product_details, $details);
            }
        }
        
        // นับจำนวน product_details
        $total = count($product_details);

        if ($total > 0) {
            $product_details_paginated = array_slice($product_details, $offset, $perPage);
        } else {
            $product_details_paginated = [];
        }
    
        // จัดการข้อมูล Carousel
        $carousels = [];
        foreach ($carousel_data as $carousel) {
            $carousels[] = [
                'id' => $carousel->ID,
                'title' => $carousel->post_title,
                'status' => $carousel->post_status,
                'date_created' => $carousel->post_date,
                'date_modified' => $carousel->post_modified,
                'language' => $carousel->language,
                'product_details' => $product_details_paginated
            ];
        }
        // var_dump($carousels);
        return [
            'data' => $carousels,
            'total' => $total,
            'page' => $page,
            'lastPage' => ($perPage > 0) ? ceil($total / $perPage) : 0,
        ];
    }

    public function deleteNonPublicCarousel($carouselId) {
    
        // ดึงข้อมูลของ carousel ก่อนลบ
        $carousel = $this->wpdb->get_row($this->wpdb->prepare("SELECT ID, post_title FROM {$this->wpdb->posts} WHERE ID = %d AND post_type = %s", $carouselId, 'product_carousel'), ARRAY_A);
    
        if (is_null($carousel)) {
            return ['error' => 'Carousel not found'];
        }
    
        // SQL สำหรับลบ product_carousel ที่ไม่ใช่ public
        $sql = "DELETE FROM {$this->wpdb->posts} WHERE ID = %d AND post_type = %s AND post_status != %s";
        
        // ใช้ $wpdb->prepare เพื่อป้องกัน SQL Injection
        $prepared_query = $this->wpdb->prepare($sql, $carouselId, 'product_carousel', 'publish');
        
        // ทำการลบ
        $result = $this->wpdb->query($prepared_query);
        
        // ตรวจสอบและคืนค่าผลลัพธ์
        if ($result === false) {
            return ['error' => 'Error in deleting the non-public carousel'];
        } else {
             // คืนค่าข้อมูลของ carousel ที่ถูกลบ
            return [
                'success' => "Carousel deleted success {$carousel['ID']} {$carousel['post_title']}",
                'id' => $carousel['ID'],
                'title' => $carousel['post_title']
            ];
        }
    }

    public function displayProductInCarousel($carouselId, $productId) {
        // ดึงข้อมูลผลิตภัณฑ์ที่มีอยู่ใน Carousel
        $existingProductsMeta = get_post_meta($carouselId, 'product_details', true);
        $existingProductsMeta = stripslashes($existingProductsMeta); // ลบการ escape อักขระ
        $existingProducts = json_decode($existingProductsMeta, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // จัดการข้อผิดพลาดในกรณีที่ json_decode ล้มเหลว
            return ['error' => 'Failed to decode product details: ' . json_last_error_msg()];
        }

        if (!is_array($existingProducts)) {
            // ตรวจสอบว่าข้อมูลหลังจาก decode เป็น array
            return ['error' => 'Product details format is invalid'];
        }

        // ค้นหาผลิตภัณฑ์ตาม productId
        foreach ($existingProducts as $product) {
            if (isset($product['id']) && $product['id'] === $productId) {
                // คืนค่าข้อมูลผลิตภัณฑ์ที่พบ
                return [
                    'success' => true,
                    'product' => $product
                ];
            }
        }

        // ถ้าไม่พบผลิตภัณฑ์
        return ['error' => 'Product not found in Carousel'];
    }

    // ฟังก์ชันสำหรับดึงข้อมูล carousel โดยใช้ ID
    public function getCarouselData($carouselId) {
        $post = get_post($carouselId);

        // ตรวจสอบว่าโพสต์มีอยู่จริงและเป็นประเภท 'product_carousel'
        if ($post && $post->post_type === 'product_carousel') {
            $postarr = array(
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'post_status' => $post->post_status,
                'post_type' => $post->post_type,
                'language' => get_post_meta($carouselId, 'language', true)
            );
            // ส่งกลับข้อมูล carousel ในรูปแบบ 'success'
            return ['success' => true, 'data' => $postarr];
        }

        // ส่งกลับข้อผิดพลาดหากไม่พบ carousel
        return ['error' => 'Carousel not found.'];
    }

    public function deleteProductDetails($carouselId, $productId) {
        // ดึง product_details จาก carousel
        $existingProductsMeta = get_post_meta($carouselId, 'product_details', true);
        $existingProductsMeta = stripslashes($existingProductsMeta); // ลบการ escape อักขระ
        $existingProducts = $existingProductsMeta ? json_decode($existingProductsMeta, true) : [];
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            // จัดการข้อผิดพลาดในกรณีที่ json_decode ล้มเหลว
            return ['error' => 'Failed to decode product details: ' . json_last_error_msg()];
        }
    
        // ค้นหารายการผลิตภัณฑ์ที่ต้องการลบ
        $found = false;
        foreach ($existingProducts as $key => $product) {
            if ($product['id'] === $productId) {
                unset($existingProducts[$key]);
                $found = true;
                break; // หยุดลูปเมื่อพบและลบผลิตภัณฑ์
            }
        }
    
        if (!$found) {
            return ['error' => 'Product not found'];
        }
    
        // หลังจากลบ ใช้ array_values สำหรับ re-index อาร์เรย์ เพื่อป้องกันโครงสร้างข้อมูล JSON เปลี่ยนไป
        $updatedProducts = array_values($existingProducts);
    
        // อัปเดต product_details ใน carousel ใช้ JSON_UNESCAPED_UNICODE สำหรับรองรับ Unicode
        $result = update_post_meta($carouselId, 'product_details', json_encode($updatedProducts, JSON_UNESCAPED_UNICODE));
    
        if ($result === false) {
            return ['error' => 'Error in updating the product details after deletion'];
        } else {
            return [
                'success' => "Product details deleted successfully",
                'id' => $productId
            ];
        }
    }
    

    private function convertUrlToPath($imageUrl) {
        $uploadDirs = wp_upload_dir();
        $uploadBaseDir = trailingslashit($uploadDirs['basedir']); // เพิ่ม trailing slash
        $uploadBaseUrl = trailingslashit($uploadDirs['baseurl']); // เพิ่ม trailing slash
    
        $relativePath = str_replace($uploadBaseUrl, '', $imageUrl);
        $fullPath = $uploadBaseDir . ltrim($relativePath, '/'); // ลบ leading slash หากมี
    
        return $fullPath;
    }    
    
}
