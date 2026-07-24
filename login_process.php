<?php
require_once 'includes/functions.php';
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$role = $_POST['role'] ?? '';
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!in_array($role, ['admin', 'staff', 'student']) || $email === '' || $password === '') {
    set_message('Please fill all fields correctly.', 'error');
    redirect('index.php?role=' . urlencode($role));
}

$table = $role === 'admin' ? 'admin' : ($role === 'staff' ? 'staff' : 'students');

$stmt = mysqli_prepare($conn, "SELECT id, full_name, email, password FROM $table WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['role'] = $role;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];

    switch ($role) {
        case 'admin': redirect('admin/dashboard.php');
        case 'staff': redirect('staff/dashboard.php');
        case 'student': redirect('student/dashboard.php');
    }
} else {
    set_message('Invalid email or password.', 'error');
    redirect('index.php?role=' . urlencode($role));
}
