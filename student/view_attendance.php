<?php
require_once '../includes/functions.php';
require_student();
require_once '../config/db_connect.php';

$page_title = 'My Attendance';
$student_id = $_SESSION['user_id'];

$subject_id = $_GET['subject_id'] ?? '';

$subjects = mysqli_query($conn, "
    SELECT DISTINCT sub.id, sub.subject_name
    FROM attendance a JOIN subjects sub ON sub.id = a.subject_id
    WHERE a.student_id = $student_id
");
$subjects_arr = [];
while ($s = mysqli_fetch_assoc($subjects)) { $subjects_arr[] = $s; }

$where = ['a.student_id = ?'];
$params = [$student_id];
$types = 'i';
if ($subject_id !== '') { $where[] = 'a.subject_id = ?'; $params[] = (int) $subject_id; $types .= 'i'; }

$sql = "
    SELECT a.attendance_date, a.status, sub.subject_name
    FROM attendance a
    JOIN subjects sub ON sub.id = a.subject_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY a.attendance_date DESC
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$records = mysqli_stmt_get_result($stmt);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card">
    <h3>Filter by Subject</h3>
    <form method="GET" class="filter-row">
        <div class="form-group">
            <label>Subject</label>
            <select name="subject_id">
                <option value="">All Subjects</option>
                <?php foreach ($subjects_arr as $s): ?>
                    <option value="<?php echo $s['id']; ?>" <?php echo $subject_id == $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['subject_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="view_attendance.php" class="btn" style="background:#e5e7eb;">Reset</a>
    </form>
</div>

<div class="card">
    <h3>My Attendance History</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Subject</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($records) === 0): ?>
                <tr><td colspan="3" class="empty-state">No attendance records found yet.</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($records)): ?>
                <tr>
                    <td><?php echo date('d M Y', strtotime($r['attendance_date'])); ?></td>
                    <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                    <td><span class="badge <?php echo $r['status'] === 'Present' ? 'badge-present' : 'badge-absent'; ?>"><?php echo $r['status']; ?></span></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
