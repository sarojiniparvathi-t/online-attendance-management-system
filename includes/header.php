<?php
/**
 * Shared header for all dashboard pages (admin/staff/student).
 * Expects $page_title to be set before including this file.
 */
if (!isset($page_title)) { $page_title = 'Dashboard'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> | Student Attendance System</title>
    <link rel="stylesheet" href="<?php echo get_base_url(); ?>assets/css/style.css">
</head>
<body>
<div class="app-layout">
