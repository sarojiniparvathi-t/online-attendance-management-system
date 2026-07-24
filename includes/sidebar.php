<?php
/**
 * Dynamic sidebar + topbar.
 * Renders different nav links depending on $_SESSION['role'].
 * Must be included AFTER header.php.
 */
$role = $_SESSION['role'] ?? '';
$current = basename($_SERVER['PHP_SELF']);
$base = get_base_url();

function nav_active($file, $current) {
    return $file === $current ? 'active' : '';
}
?>
<div class="sidebar">
    <div class="brand">
        🎓 Attendance System
        <span><?php echo ucfirst($role); ?> Panel</span>
    </div>
    <nav>
        <?php if ($role === 'admin'): ?>
            <a href="<?php echo $base; ?>admin/dashboard.php" class="<?php echo nav_active('dashboard.php', $current); ?>">📊 Dashboard</a>
            <a href="<?php echo $base; ?>admin/manage_staff.php" class="<?php echo nav_active('manage_staff.php', $current); ?>">👨‍🏫 Manage Staff</a>
            <a href="<?php echo $base; ?>admin/manage_student.php" class="<?php echo nav_active('manage_student.php', $current); ?>">🎓 Manage Students</a>
            <a href="<?php echo $base; ?>admin/manage_class.php" class="<?php echo nav_active('manage_class.php', $current); ?>">🏫 Manage Classes</a>
            <a href="<?php echo $base; ?>admin/manage_section.php" class="<?php echo nav_active('manage_section.php', $current); ?>">📁 Manage Sections</a>
            <a href="<?php echo $base; ?>admin/manage_subject.php" class="<?php echo nav_active('manage_subject.php', $current); ?>">📘 Manage Subjects</a>
            <a href="<?php echo $base; ?>admin/view_attendance.php" class="<?php echo nav_active('view_attendance.php', $current); ?>">📝 View Attendance</a>
            <a href="<?php echo $base; ?>admin/change_password.php" class="<?php echo nav_active('change_password.php', $current); ?>">🔒 Change Password</a>
        <?php elseif ($role === 'staff'): ?>
            <a href="<?php echo $base; ?>staff/dashboard.php" class="<?php echo nav_active('dashboard.php', $current); ?>">📊 Dashboard</a>
            <a href="<?php echo $base; ?>staff/take_attendance.php" class="<?php echo nav_active('take_attendance.php', $current); ?>">✅ Take Attendance</a>
            <a href="<?php echo $base; ?>staff/view_attendance.php" class="<?php echo nav_active('view_attendance.php', $current); ?>">📝 View Attendance</a>
            <a href="<?php echo $base; ?>staff/change_password.php" class="<?php echo nav_active('change_password.php', $current); ?>">🔒 Change Password</a>
        <?php elseif ($role === 'student'): ?>
            <a href="<?php echo $base; ?>student/dashboard.php" class="<?php echo nav_active('dashboard.php', $current); ?>">📊 Dashboard</a>
            <a href="<?php echo $base; ?>student/view_attendance.php" class="<?php echo nav_active('view_attendance.php', $current); ?>">📝 My Attendance</a>
            <a href="<?php echo $base; ?>student/change_password.php" class="<?php echo nav_active('change_password.php', $current); ?>">🔒 Change Password</a>
        <?php endif; ?>
        <a href="<?php echo $base; ?>logout.php">🚪 Logout</a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <button id="sidebarToggle" class="btn btn-sm btn-primary" style="display:none;">☰</button>
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></strong></span>
        </div>
    </div>
    <div class="page-body">
        <?php show_message(); ?>
