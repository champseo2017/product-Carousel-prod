<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly to maintain security
}

class ValidationHelper {

    // Sanitizes a URI from the server input using a filter
    public static function sanitizeUri($uri) {
        return filter_input(INPUT_SERVER, $uri, FILTER_SANITIZE_STRING);
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
}