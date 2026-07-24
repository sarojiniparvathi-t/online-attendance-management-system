<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

$page_title = 'Admin Dashboard';

$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM students"))['c'];
$total_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM staff"))['c'];
$total_classes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM classes"))['c'];
$total_subjects = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM subjects"))['c'];

$today = date('Y-m-d');
$today_present = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM attendance WHERE attendance_date = '$today' AND status = 'Present'"))['c'];
$today_absent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM attendance WHERE attendance_date = '$today' AND status = 'Absent'"))['c'];

// Recent attendance activity
$recent = mysqli_query($conn, "
    SELECT a.attendance_date, s.full_name AS student_name, sub.subject_name, st.full_name AS staff_name, a.status
    FROM attendance a
    JOIN students s ON s.id = a.student_id
    JOIN subjects sub ON sub.id = a.subject_id
    JOIN staff st ON st.id = a.staff_id
    ORDER BY a.created_at DESC LIMIT 8
");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Total Students</div>
        <div class="value"><?php echo $total_students; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Staff</div>
        <div class="value"><?php echo $total_staff; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Classes</div>
        <div class="value"><?php echo $total_classes; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Subjects</div>
        <div class="value"><?php echo $total_subjects; ?></div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h3>Recent Attendance Activity</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Date</th><th>Student</th><th>Subject</th><th>Marked By</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($recent) === 0): ?>
                    <tr><td colspan="5" class="empty-state">No attendance records yet.</td></tr>
                <?php else: while ($r = mysqli_fetch_assoc($recent)): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($r['attendance_date'])); ?></td>
                        <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['staff_name']); ?></td>
                        <td>
                            <span class="badge <?php echo $r['status'] === 'Present' ? 'badge-present' : 'badge-absent'; ?>">
                                <?php echo $r['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3>Today's Snapshot</h3>
        <p class="text-muted">Attendance marked today across all subjects.</p>
        <div class="stats-grid" style="grid-template-columns: 1fr 1fr; margin-top: 14px;">
            <div class="stat-card" style="border-top-color: var(--success);">
                <div class="label">Present</div>
                <div class="value" style="color: var(--success);"><?php echo $today_present; ?></div>
            </div>
            <div class="stat-card" style="border-top-color: var(--danger);">
                <div class="label">Absent</div>
                <div class="value" style="color: var(--danger);"><?php echo $today_absent; ?></div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
