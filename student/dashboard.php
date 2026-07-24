<?php
require_once '../includes/functions.php';
require_student();
require_once '../config/db_connect.php';

$page_title = 'Student Dashboard';
$student_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "
    SELECT s.*, c.class_name, sec.section_name
    FROM students s
    JOIN classes c ON c.id = s.class_id
    JOIN sections sec ON sec.id = s.section_id
    WHERE s.id = ?
");
mysqli_stmt_bind_param($stmt, 'i', $student_id);
mysqli_stmt_execute($stmt);
$student = mysqli_stmt_get_result($stmt)->fetch_assoc();

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM attendance WHERE student_id = ? AND status = 'Present'");
mysqli_stmt_bind_param($stmt, 'i', $student_id);
mysqli_stmt_execute($stmt);
$present = mysqli_stmt_get_result($stmt)->fetch_assoc()['c'];

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM attendance WHERE student_id = ? AND status = 'Absent'");
mysqli_stmt_bind_param($stmt, 'i', $student_id);
mysqli_stmt_execute($stmt);
$absent = mysqli_stmt_get_result($stmt)->fetch_assoc()['c'];

$total = $present + $absent;
$percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card">
    <h3>My Details</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
    <p><strong>Roll Number:</strong> <?php echo htmlspecialchars($student['roll_no']); ?></p>
    <p><strong>Class:</strong> <?php echo htmlspecialchars($student['class_name']); ?> - <?php echo htmlspecialchars($student['section_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
</div>

<div class="card">
    <h3>📌 Attendance Summary</h3>
    <p><strong>Attendance Percentage:</strong> <?php echo $percentage; ?>%</p>
    <div class="progress-bar">
        <div class="fill" style="width: <?php echo $percentage; ?>%;"></div>
    </div>
    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 18px;">
        <div class="stat-card">
            <div class="label">Present</div>
            <div class="value" style="color: var(--success);"><?php echo $present; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Absent</div>
            <div class="value" style="color: var(--danger);"><?php echo $absent; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Total Days</div>
            <div class="value"><?php echo $total; ?></div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
