<?php
require_once '../includes/functions.php';
require_staff();
require_once '../config/db_connect.php';

$page_title = 'View Attendance';
$staff_id = $_SESSION['user_id'];

$my_subjects = mysqli_query($conn, "SELECT id, subject_name FROM subjects WHERE staff_id = $staff_id");
$my_subjects_arr = [];
while ($s = mysqli_fetch_assoc($my_subjects)) { $my_subjects_arr[] = $s; }

$subject_id = $_GET['subject_id'] ?? '';
$date = $_GET['date'] ?? '';

$where = ['a.staff_id = ?'];
$params = [$staff_id];
$types = 'i';

if ($subject_id !== '') { $where[] = 'a.subject_id = ?'; $params[] = (int) $subject_id; $types .= 'i'; }
if ($date !== '') { $where[] = 'a.attendance_date = ?'; $params[] = $date; $types .= 's'; }

$sql = "
    SELECT a.attendance_date, a.status, s.full_name, s.roll_no, sub.subject_name
    FROM attendance a
    JOIN students s ON s.id = a.student_id
    JOIN subjects sub ON sub.id = a.subject_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY a.attendance_date DESC, s.roll_no ASC
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$records = mysqli_stmt_get_result($stmt);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card">
    <h3>Filter</h3>
    <form method="GET" class="filter-row">
        <div class="form-group">
            <label>Subject</label>
            <select name="subject_id">
                <option value="">All My Subjects</option>
                <?php foreach ($my_subjects_arr as $s): ?>
                    <option value="<?php echo $s['id']; ?>" <?php echo $subject_id == $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['subject_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="view_attendance.php" class="btn" style="background:#e5e7eb;">Reset</a>
    </form>
</div>

<div class="card">
    <h3>Attendance Records</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Roll No</th><th>Student</th><th>Subject</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($records) === 0): ?>
                <tr><td colspan="5" class="empty-state">No records found.</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($records)): ?>
                <tr>
                    <td><?php echo date('d M Y', strtotime($r['attendance_date'])); ?></td>
                    <td><?php echo htmlspecialchars($r['roll_no']); ?></td>
                    <td><?php echo htmlspecialchars($r['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                    <td><span class="badge <?php echo $r['status'] === 'Present' ? 'badge-present' : 'badge-absent'; ?>"><?php echo $r['status']; ?></span></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
