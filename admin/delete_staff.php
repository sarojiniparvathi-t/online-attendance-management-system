<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM staff WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    set_message('Staff member deleted.');
}
redirect('manage_staff.php');
