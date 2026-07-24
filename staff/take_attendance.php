<?php
require_once '../includes/functions.php';
require_staff();
require_once '../config/db_connect.php';

$page_title = 'Take Attendance';
$staff_id = $_SESSION['user_id'];

// Get subjects assigned to this staff member
$my_subjects = mysqli_query($conn, "SELECT id, subject_name, class_id FROM subjects WHERE staff_id = $staff_id");
$my_subjects_arr = [];
while ($s = mysqli_fetch_assoc($my_subjects)) { $my_subjects_arr[] = $s; }

$subject_id = (int) ($_GET['subject_id'] ?? ($my_subjects_arr[0]['id'] ?? 0));
$date = $_GET['date'] ?? date('Y-m-d');

// Verify this subject belongs to this staff member
$subject_info = null;
foreach ($my_subjects_arr as $s) {
    if ($s['id'] == $subject_id) { $subject_info = $s; break; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_subject_id = (int) $_POST['subject_id'];
    $post_date = $_POST['attendance_date'];
    $statuses = $_POST['status'] ?? [];

    // Re-verify ownership
    $owns = false;
    foreach ($my_subjects_arr as $s) { if ($s['id'] == $post_subject_id) { $owns = true; $class_id = $s['class_id']; break; } }

    if ($owns) {
        foreach ($statuses as $student_id => $status) {
            $student_id = (int) $student_id;
            $status = $status === 'Present' ? 'Present' : 'Absent';

            // Need section_id for this student
            $sres = mysqli_prepare($conn, "SELECT section_id FROM students WHERE id = ?");
            mysqli_stmt_bind_param($sres, 'i', $student_id);
            mysqli_stmt_execute($sres);
            $section_row = mysqli_stmt_get_result($sres)->fetch_assoc();
            $section_id = $section_row['section_id'];

            $stmt = mysqli_prepare($conn, "
                INSERT INTO attendance (student_id, subject_id, staff_id, class_id, section_id, attendance_date, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status)
            ");
            mysqli_stmt_bind_param($stmt, 'iiiisss', $student_id, $post_subject_id, $staff_id, $class_id, $section_id, $post_date, $status);
            mysqli_stmt_execute($stmt);
        }
        set_message('Attendance saved successfully for ' . date('d M Y', strtotime($post_date)) . '.');
    } else {
        set_message('You are not authorized to mark attendance for this subject.', 'error');
    }
    redirect("take_attendance.php?subject_id=$post_subject_id&date=$post_date");
}

// Fetch students in the subject's class, along with any existing attendance for the selected date
$students = [];
$total_present = 0;
$total_absent = 0;
if ($subject_info) {
    $class_id = $subject_info['class_id'];
    $stmt = mysqli_prepare($conn, "
        SELECT s.id, s.full_name, s.roll_no,
               a.status AS existing_status
        FROM students s
        LEFT JOIN attendance a ON a.student_id = s.id AND a.subject_id = ? AND a.attendance_date = ?
        WHERE s.class_id = ?
        ORDER BY s.roll_no ASC
    ");
    mysqli_stmt_bind_param($stmt, 'isi', $subject_id, $date, $class_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $students[] = $row;
        if ($row['existing_status'] === 'Present') $total_present++;
        if ($row['existing_status'] === 'Absent') $total_absent++;
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="grid-2">
    <div class="card">
        <h3>Mark Attendance</h3>
        <form method="GET" class="filter-row">
            <div class="form-group">
                <label>Subject</label>
                <select name="subject_id" onchange="this.form.submit()">
                    <?php if (empty($my_subjects_arr)): ?>
                        <option value="">No subjects assigned</option>
                    <?php endif; ?>
                    <?php foreach ($my_subjects_arr as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo $subject_id == $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['subject_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" onchange="this.form.submit()" max="<?php echo date('Y-m-d'); ?>">
            </div>
        </form>

        <?php if (!$subject_info): ?>
            <p class="empty-state">You have no subjects assigned yet. Please contact the admin.</p>
        <?php elseif (empty($students)): ?>
            <p class="empty-state">No students found in this class.</p>
        <?php else: ?>
        <form method="POST">
            <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
            <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($date); ?>">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>S.No</th><th>Name</th><th>Roll Number</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php $i = 1; foreach ($students as $st): $status = $st['existing_status'] ?: 'Present'; ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($st['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($st['roll_no']); ?></td>
                            <td>
                                <div class="status-toggle">
                                    <input type="radio" name="status[<?php echo $st['id']; ?>]" value="Present" id="p<?php echo $st['id']; ?>" <?php echo $status === 'Present' ? 'checked' : ''; ?>>
                                    <label for="p<?php echo $st['id']; ?>" class="present-label">P</label>
                                    <input type="radio" name="status[<?php echo $st['id']; ?>]" value="Absent" id="a<?php echo $st['id']; ?>" <?php echo $status === 'Absent' ? 'checked' : ''; ?>>
                                    <label for="a<?php echo $st['id']; ?>" class="absent-label">A</label>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-primary mt-16">Submit Attendance</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Attendance Summary</h3>
        <p class="text-muted">For <?php echo htmlspecialchars($subject_info['subject_name'] ?? '—'); ?> on <?php echo date('d M Y', strtotime($date)); ?></p>
        <p class="mt-16"><strong>Total Students:</strong> <?php echo count($students); ?></p>
        <p><strong>Present:</strong> <?php echo $total_present; ?></p>
        <p><strong>Absent:</strong> <?php echo $total_absent; ?></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
