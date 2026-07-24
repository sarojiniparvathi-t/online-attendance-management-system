<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

$page_title = 'Manage Staff';

$staff_list = mysqli_query($conn, "SELECT * FROM staff ORDER BY id DESC");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <h3 style="margin:0;">All Staff</h3>
        <a href="staff_form.php" class="btn btn-primary btn-sm">+ Add Staff</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Gender</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($staff_list) === 0): ?>
                <tr><td colspan="6" class="empty-state">No staff members added yet.</td></tr>
            <?php else: $i = 1; while ($s = mysqli_fetch_assoc($staff_list)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['phone']); ?></td>
                    <td><?php echo htmlspecialchars($s['gender']); ?></td>
                    <td class="action-links">
                        <a href="staff_form.php?id=<?php echo $s['id']; ?>" class="edit-link">Edit</a>
                        <a href="delete_staff.php?id=<?php echo $s['id']; ?>" class="delete-link confirm-delete">Delete</a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
