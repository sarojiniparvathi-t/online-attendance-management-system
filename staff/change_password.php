<?php
require_once '../includes/functions.php';
require_staff();
require_once '../config/db_connect.php';

$page_title = 'Change Password';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = mysqli_prepare($conn, "SELECT password FROM staff WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $row = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$row || !password_verify($current, $row['password'])) {
        set_message('Current password is incorrect.', 'error');
    } elseif ($new !== $confirm) {
        set_message('New passwords do not match.', 'error');
    } elseif (strlen($new) < 6) {
        set_message('New password must be at least 6 characters.', 'error');
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = mysqli_prepare($conn, "UPDATE staff SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($update, 'si', $hashed, $_SESSION['user_id']);
        mysqli_stmt_execute($update);
        set_message('Password changed successfully.');
    }
    redirect('change_password.php');
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card" style="max-width: 480px;">
    <h3>Change Password</h3>
    <form method="POST">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required minlength="6">
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Update Password</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
