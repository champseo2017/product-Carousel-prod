<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class SettingsModel {
    public function sanitize_allowed_domains($inputs) {
        $sanitized_inputs = array();
        if (is_array($inputs)) {
            foreach ($inputs as $input) {
                if (filter_var($input, FILTER_VALIDATE_URL)) {
                    $sanitized_inputs[] = $input;
                }
            }
        }
        return $sanitized_inputs;
    }
}
