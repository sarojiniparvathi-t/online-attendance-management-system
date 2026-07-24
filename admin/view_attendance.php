<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

$page_title = 'View Attendance';

$class_id = $_GET['class_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$date = $_GET['date'] ?? '';

$classes = mysqli_query($conn, "SELECT id, class_name FROM classes ORDER BY class_name");
$classes_arr = [];
while ($c = mysqli_fetch_assoc($classes)) { $classes_arr[] = $c; }

$sections = mysqli_query($conn, "SELECT id, section_name, class_id FROM sections ORDER BY section_name");
$sections_arr = [];
while ($s = mysqli_fetch_assoc($sections)) { $sections_arr[] = $s; }

$subjects = mysqli_query($conn, "SELECT id, subject_name, class_id FROM subjects ORDER BY subject_name");
$subjects_arr = [];
while ($s = mysqli_fetch_assoc($subjects)) { $subjects_arr[] = $s; }

$where = [];
$params = [];
$types = '';

if ($class_id !== '') { $where[] = 'a.class_id = ?'; $params[] = (int) $class_id; $types .= 'i'; }
if ($section_id !== '') { $where[] = 'a.section_id = ?'; $params[] = (int) $section_id; $types .= 'i'; }
if ($subject_id !== '') { $where[] = 'a.subject_id = ?'; $params[] = (int) $subject_id; $types .= 'i'; }
if ($date !== '') { $where[] = 'a.attendance_date = ?'; $params[] = $date; $types .= 's'; }

$sql = "
    SELECT a.attendance_date, a.status, s.full_name AS student_name, s.roll_no,
           sub.subject_name, c.class_name, sec.section_name, st.full_name AS staff_name
    FROM attendance a
    JOIN students s ON s.id = a.student_id
    JOIN subjects sub ON sub.id = a.subject_id
    JOIN classes c ON c.id = a.class_id
    JOIN sections sec ON sec.id = a.section_id
    JOIN staff st ON st.id = a.staff_id
";
if ($where) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= ' ORDER BY a.attendance_date DESC, s.roll_no ASC';

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$records = mysqli_stmt_get_result($stmt);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card">
    <h3>Filter Attendance Records</h3>
    <form method="GET" class="filter-row">
        <div class="form-group">
            <label>Class</label>
            <select name="class_id">
                <option value="">All Classes</option>
                <?php foreach ($classes_arr as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $class_id == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['class_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Section</label>
            <select name="section_id">
                <option value="">All Sections</option>
                <?php foreach ($sections_arr as $s): ?>
                    <option value="<?php echo $s['id']; ?>" <?php echo $section_id == $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['section_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Subject</label>
            <select name="subject_id">
                <option value="">All Subjects</option>
                <?php foreach ($subjects_arr as $s): ?>
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
            <thead>
                <tr><th>Date</th><th>Roll No</th><th>Student</th><th>Class</th><th>Section</th><th>Subject</th><th>Marked By</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($records) === 0): ?>
                <tr><td colspan="8" class="empty-state">No attendance records found for the selected filters.</td></tr>
            <?php else: while ($r = mysqli_fetch_assoc($records)): ?>
                <tr>
                    <td><?php echo date('d M Y', strtotime($r['attendance_date'])); ?></td>
                    <td><?php echo htmlspecialchars($r['roll_no']); ?></td>
                    <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['section_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['staff_name']); ?></td>
                    <td><span class="badge <?php echo $r['status'] === 'Present' ? 'badge-present' : 'badge-absent'; ?>"><?php echo $r['status']; ?></span></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
