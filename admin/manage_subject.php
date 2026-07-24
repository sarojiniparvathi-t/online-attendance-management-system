<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $subject_name = clean($conn, $_POST['subject_name']);
    $subject_code = clean($conn, $_POST['subject_code']);
    $class_id = (int) $_POST['class_id'];
    $staff_id = $_POST['staff_id'] !== '' ? (int) $_POST['staff_id'] : null;

    if ($_POST['action'] === 'add') {
        if ($subject_name !== '' && $class_id) {
            $stmt = mysqli_prepare($conn, "INSERT INTO subjects (subject_name, subject_code, class_id, staff_id) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssii', $subject_name, $subject_code, $class_id, $staff_id);
            mysqli_stmt_execute($stmt);
            set_message('Subject added successfully.');
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = (int) $_POST['id'];
        $stmt = mysqli_prepare($conn, "UPDATE subjects SET subject_name=?, subject_code=?, class_id=?, staff_id=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssiii', $subject_name, $subject_code, $class_id, $staff_id, $id);
        mysqli_stmt_execute($stmt);
        set_message('Subject updated successfully.');
    }
    redirect('manage_subject.php');
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM subjects WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    set_message('Subject deleted.');
    redirect('manage_subject.php');
}

$page_title = 'Manage Subjects';
$subjects = mysqli_query($conn, "
    SELECT sub.*, c.class_name, st.full_name AS staff_name
    FROM subjects sub
    JOIN classes c ON c.id = sub.class_id
    LEFT JOIN staff st ON st.id = sub.staff_id
    ORDER BY sub.id DESC
");
$classes = mysqli_query($conn, "SELECT id, class_name FROM classes ORDER BY class_name");
$classes_arr = [];
while ($c = mysqli_fetch_assoc($classes)) { $classes_arr[] = $c; }

$staff_res = mysqli_query($conn, "SELECT id, full_name FROM staff ORDER BY full_name");
$staff_arr = [];
while ($s = mysqli_fetch_assoc($staff_res)) { $staff_arr[] = $s; }

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="grid-2">
    <div class="card">
        <h3>All Subjects</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Code</th><th>Subject</th><th>Class</th><th>Assigned Staff</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($subjects) === 0): ?>
                    <tr><td colspan="5" class="empty-state">No subjects added yet.</td></tr>
                <?php else: while ($s = mysqli_fetch_assoc($subjects)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['subject_code']); ?></td>
                        <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['staff_name'] ?? '—'); ?></td>
                        <td class="action-links">
                            <a href="?delete=<?php echo $s['id']; ?>" class="delete-link confirm-delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3>Add New Subject</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Subject Name</label>
                <input type="text" name="subject_name" required placeholder="e.g. Database Management">
            </div>
            <div class="form-group">
                <label>Subject Code</label>
                <input type="text" name="subject_code" placeholder="e.g. BCA201">
            </div>
            <div class="form-group">
                <label>Class</label>
                <select name="class_id" required>
                    <option value="">-- Select Class --</option>
                    <?php foreach ($classes_arr as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['class_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Assign Staff</label>
                <select name="staff_id">
                    <option value="">-- Not Assigned --</option>
                    <?php foreach ($staff_arr as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Add Subject</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
