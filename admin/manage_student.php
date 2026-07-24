<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

$page_title = 'Manage Students';

$students = mysqli_query($conn, "
    SELECT s.*, c.class_name, sec.section_name
    FROM students s
    JOIN classes c ON c.id = s.class_id
    JOIN sections sec ON sec.id = s.section_id
    ORDER BY s.id DESC
");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <h3 style="margin:0;">All Students</h3>
        <a href="student_form.php" class="btn btn-primary btn-sm">+ Add Student</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Roll No</th><th>Name</th><th>Class</th><th>Section</th><th>Email</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($students) === 0): ?>
                <tr><td colspan="6" class="empty-state">No students added yet.</td></tr>
            <?php else: while ($s = mysqli_fetch_assoc($students)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['roll_no']); ?></td>
                    <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['section_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td class="action-links">
                        <a href="student_form.php?id=<?php echo $s['id']; ?>" class="edit-link">Edit</a>
                        <a href="delete_student.php?id=<?php echo $s['id']; ?>" class="delete-link confirm-delete">Delete</a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
