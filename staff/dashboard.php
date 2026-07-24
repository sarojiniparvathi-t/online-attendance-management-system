<?php
require_once '../includes/functions.php';
require_staff();
require_once '../config/db_connect.php';

$page_title = 'Staff Dashboard';
$staff_id = $_SESSION['user_id'];

$subjects = mysqli_query($conn, "
    SELECT sub.id, sub.subject_name, c.class_name,
        (SELECT COUNT(*) FROM students st WHERE st.class_id = sub.class_id) AS total_students
    FROM subjects sub
    JOIN classes c ON c.id = sub.class_id
    WHERE sub.staff_id = $staff_id
");

$stmt = mysqli_prepare($conn, "SELECT COUNT(DISTINCT attendance_date) c FROM attendance WHERE staff_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $staff_id);
mysqli_stmt_execute($stmt);
$days_marked = mysqli_stmt_get_result($stmt)->fetch_assoc()['c'];

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM attendance WHERE staff_id = ? AND status='Present'");
mysqli_stmt_bind_param($stmt, 'i', $staff_id);
mysqli_stmt_execute($stmt);
$total_present = mysqli_stmt_get_result($stmt)->fetch_assoc()['c'];

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM attendance WHERE staff_id = ? AND status='Absent'");
mysqli_stmt_bind_param($stmt, 'i', $staff_id);
mysqli_stmt_execute($stmt);
$total_absent = mysqli_stmt_get_result($stmt)->fetch_assoc()['c'];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Days Attendance Marked</div>
        <div class="value"><?php echo $days_marked; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Present Marks</div>
        <div class="value" style="color: var(--success);"><?php echo $total_present; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Absent Marks</div>
        <div class="value" style="color: var(--danger);"><?php echo $total_absent; ?></div>
    </div>
</div>

<div class="card">
    <h3>My Assigned Subjects</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Subject</th><th>Class</th><th>Total Students</th><th>Action</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($subjects) === 0): ?>
                <tr><td colspan="4" class="empty-state">No subjects assigned to you yet. Contact the admin.</td></tr>
            <?php else: while ($s = mysqli_fetch_assoc($subjects)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['class_name']); ?></td>
                    <td><?php echo $s['total_students']; ?></td>
                    <td><a href="take_attendance.php?subject_id=<?php echo $s['id']; ?>" class="btn btn-primary btn-sm">Take Attendance</a></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
