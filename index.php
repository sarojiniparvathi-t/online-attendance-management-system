<?php
require_once 'includes/functions.php';

// If already logged in, send straight to the right dashboard
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin': redirect('admin/dashboard.php');
        case 'staff': redirect('staff/dashboard.php');
        case 'student': redirect('student/dashboard.php');
    }
}

$selected_role = $_GET['role'] ?? 'student';
if (!in_array($selected_role, ['admin', 'staff', 'student'])) {
    $selected_role = 'student';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Student Attendance System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>🎓 Attendance System</h2>

        <div class="role-tabs">
            <a href="index.php?role=student" class="role-tab <?php echo $selected_role === 'student' ? 'active' : ''; ?>">Student</a>
            <a href="index.php?role=staff" class="role-tab <?php echo $selected_role === 'staff' ? 'active' : ''; ?>">Staff</a>
            <a href="index.php?role=admin" class="role-tab <?php echo $selected_role === 'admin' ? 'active' : ''; ?>">Admin</a>
        </div>

        <?php show_message(); ?>

        <form action="login_process.php" method="POST">
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($selected_role); ?>">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login as <?php echo ucfirst($selected_role); ?></button>
        </form>

        <?php if ($selected_role === 'student'): ?>
            <div class="auth-links">
                Don't have an account? <a href="register.php">Register Now</a>
            </div>
        <?php else: ?>
            <div class="auth-links">
                Login using the tabs above based on your role.
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
