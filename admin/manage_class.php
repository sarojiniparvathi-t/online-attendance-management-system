<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $class_name = clean($conn, $_POST['class_name']);
        if ($class_name !== '') {
            $stmt = mysqli_prepare($conn, "INSERT INTO classes (class_name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, 's', $class_name);
            mysqli_stmt_execute($stmt);
            set_message('Class added successfully.');
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = (int) $_POST['id'];
        $class_name = clean($conn, $_POST['class_name']);
        $stmt = mysqli_prepare($conn, "UPDATE classes SET class_name = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $class_name, $id);
        mysqli_stmt_execute($stmt);
        set_message('Class updated successfully.');
    }
    redirect('manage_class.php');
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM classes WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    set_message('Class deleted.');
    redirect('manage_class.php');
}

$page_title = 'Manage Classes';
$classes = mysqli_query($conn, "SELECT * FROM classes ORDER BY id DESC");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="grid-2">
    <div class="card">
        <h3>All Classes</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>#</th><th>Class Name</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($classes) === 0): ?>
                    <tr><td colspan="3" class="empty-state">No classes added yet.</td></tr>
                <?php else: $i = 1; while ($c = mysqli_fetch_assoc($classes)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <form method="POST" style="display:flex; gap:8px;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                <input type="text" name="class_name" value="<?php echo htmlspecialchars($c['class_name']); ?>" style="padding:6px 10px; border:1px solid var(--border); border-radius:6px;">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </form>
                        </td>
                        <td class="action-links">
                            <a href="?delete=<?php echo $c['id']; ?>" class="delete-link confirm-delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3>Add New Class</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Class Name</label>
                <input type="text" name="class_name" required placeholder="e.g. BCA I Year">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Add Class</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
