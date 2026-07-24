<?php
/**
 * Common helper functions used across the whole application.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Basic input sanitizer
 */
function clean($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}

/**
 * Guard: only allow logged-in admins
 */
function require_admin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        redirect(get_base_url() . 'index.php');
    }
}

/**
 * Guard: only allow logged-in staff
 */
function require_staff() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
        redirect(get_base_url() . 'index.php');
    }
}

/**
 * Guard: only allow logged-in students
 */
function require_student() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        redirect(get_base_url() . 'index.php');
    }
}

/**
 * Works out the relative path back to the project root
 * so links work whether we're inside /admin, /staff, /student or root.
 */
function get_base_url() {
    return '../';
}

/**
 * Flash message helpers (simple session based)
 */
function set_message($msg, $type = 'success') {
    $_SESSION['flash_message'] = $msg;
    $_SESSION['flash_type'] = $type;
}

function show_message() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] === 'error' ? 'alert-error' : 'alert-success';
        echo '<div class="alert ' . $type . '">' . htmlspecialchars($_SESSION['flash_message']) . '</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}
