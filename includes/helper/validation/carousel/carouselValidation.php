<?php
class CarouselValidation {
    // ตรวจสอบความถูกต้องของข้อความ
    public static function validateText($text) {
        return trim(wp_strip_all_tags($text));
    }

    // ตรวจสอบว่าข้อมูลไม่ว่างเปล่า
    public static function required($value) {
        return !empty($value);
    }

    // ตรวจสอบความยาวของข้อความ
    public static function minLength($value, $length) {
        return strlen($value) >= $length;
    }

    // ตรวจสอบว่าข้อมูลเป็นภาษาที่ถูกต้อง (ตัวอย่างเช่น: 'en', 'fr')
    public static function validLanguage($language) {
        $validLanguages = ['th', 'en', 'zh']; // แก้ไขตามภาษาที่คุณรองรับ
        return in_array($language, $validLanguages);
    }

    // ตรวจสอบข้อความหรืออีเมล์ที่ถูกต้อง
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
