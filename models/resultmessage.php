<?php

trait ResultMessage {
    
    public static function addError($message) {
        $_SESSION['error_message'] = $message;
    }
    
    public static function hasError() {
        return isset($_SESSION['error_message']) ? true : false;
    }
    
    
    public static function showError($class = 'admin_warning') {
        if (isset($_SESSION['error_message'])) {
            echo "<div class=\"$class\">{$_SESSION['error_message']}</div>";
            unset($_SESSION['error_message']);
        }
    }
    
    
    public static function addSuccess($message) {
        $_SESSION['success_message'] = $message;
    }
    
    public static function hasSuccess() {
        return isset($_SESSION['success_message']) ? true : false;
    }
    
    public static function showSuccess($class = 'admin_message') {
        if (isset($_SESSION['success_message'])) {
            echo "<div class=\"$class\">{$_SESSION['success_message']}</div>";
            unset($_SESSION['success_message']);
        }
    }
}