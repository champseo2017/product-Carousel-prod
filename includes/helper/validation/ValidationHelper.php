<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly to maintain security
}

class ValidationHelper {

    // Method to validate and sanitize 'id' from $_GET, returning an integer value
    public static function getSanitizedId($param = 'id') {
        return isset($_GET[$param]) ? intval($_GET[$param]) : 0;
    }

    public static function sanitizeUriSafely($uri) {
        $rawValue = filter_input(INPUT_SERVER, $uri);
        if (is_null($rawValue)) {
            return ''; // Return an empty string or handle as needed if the URI is not set
        }
        // Apply a more appropriate sanitization method depending on your context
        // Example for a general text without HTML tags
        return filter_var($rawValue, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    // Validates a URI to ensure it is a proper URL or starts with a slash (/)
    public static function validateUri($uri) {
        return filter_var($uri, FILTER_VALIDATE_URL) || preg_match('/^\//', $uri);
    }

    // Escapes a URL using WordPress's esc_url_raw function for safe database or redirect use
    public static function escUrl($url) {
        return esc_url_raw($url);
    }

    // Verifies a nonce for security, sanitizing and unslashing the nonce value before checking
    public static function verifyNonce($nonce, $action) {
        return wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), $action);
    }

    // Sanitizes text field input to ensure it's string without dangerous tags or scripts
    public static function sanitizeTextField($field) {
        return sanitize_text_field($field);
    }

    // Sanitizes textarea input for safe use, removing harmful tags/scripts while preserving line breaks
    public static function sanitizeTextareaField($field) {
        return sanitize_textarea_field($field);
    }

    // Sanitizes a key, string or database field name, ensuring it contains only safe characters
    public static function sanitizeKey($key) {
        return sanitize_key($key);
    }

    // Escapes HTML content for safe output, preventing XSS attacks by converting to HTML entities
    public static function escHtml($html) {
        return esc_html($html);
    }

    public static function isPostRequestWithRequiredFields($fields) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $missingFields = [];
            $emptyFields = []; // Add this variable to store fields that are empty
    
            foreach ($fields as $field) {
                // Check if the field is set and not empty
                if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                    if (!isset($_POST[$field])) {
                        $missingFields[] = $field; // The field is not set
                    } else {
                        $emptyFields[] = $field; // The field is empty
                    }
                }
            }
    
            if (!empty($missingFields) || !empty($emptyFields)) {
                return [
                    'error' => 'Error with required fields',
                    'missing_fields' => $missingFields,
                    'empty_fields' => $emptyFields // Include message and empty fields in the result
                ];
            }
            return true; // All fields are set and not empty
        }
    }

    public static function getErrorMessage($resultValidation) {
        if (is_array($resultValidation) && isset($resultValidation['error'])) {
            $error = 'Error: ' . $resultValidation['error'];
            if (!empty($resultValidation['missing_fields'])) {
                $error .= ' Missing fields: ' . implode(', ', $resultValidation['missing_fields']);
            }
            return $error;
        }
        return ''; // Return an empty string if there's no error.
    }

    // Function to sanitize dynamic product data
    public static function sanitizeProductData($product_data) {
        $sanitized_data = [];
        foreach ($product_data as $key => $value) {
            switch ($key) {
                case 'title':
                    $sanitized_data[$key] = self::sanitizeTextField($value);
                    break;
                case 'description':
                    $sanitized_data[$key] = self::sanitizeTextareaField($value);
                    break;
                case 'link':
                    // Validate the URL before escaping
                    if (self::validateProductUrl($value)) {
                        $sanitized_data[$key] = self::escUrl($value);
                    } else {
                        $sanitized_data[$key] = ''; // Example default value or error handling
                    }
                    break;
                case 'image':
                    // Assuming $image_url is already sanitized
                    $sanitized_data[$key] = $value;
                    break;
                case 'status':
                    $sanitized_data[$key] = self::sanitizeKey($value);
                    break;
                default:
                    // Optionally handle any keys not explicitly mentioned
                    $sanitized_data[$key] = self::sanitizeTextField($value);
            }
        }
        return $sanitized_data;
    }
    // Validates a URL from $product_data using FILTER_VALIDATE_URL
    public static function validateProductUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function generateErrorMessage($resultValidation) {
        $errorMessage = '';

        if (!empty($resultValidation['missing_fields'])) {
            $errorMessage .= 'Missing fields: ' . implode(', ', $resultValidation['missing_fields']) . "<br>";
        }

        if (!empty($resultValidation['empty_fields'])) {
            $errorMessage .= 'Fields with empty values: ' . implode(', ', $resultValidation['empty_fields']) . "<br>";
        }

        return $errorMessage;
    }
}