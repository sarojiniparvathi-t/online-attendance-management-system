<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $section_name = clean($conn, $_POST['section_name']);
        $class_id = (int) $_POST['class_id'];
        if ($section_name !== '' && $class_id) {
            $stmt = mysqli_prepare($conn, "INSERT INTO sections (section_name, class_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, 'si', $section_name, $class_id);
            mysqli_stmt_execute($stmt);
            set_message('Section added successfully.');
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = (int) $_POST['id'];
        $section_name = clean($conn, $_POST['section_name']);
        $class_id = (int) $_POST['class_id'];
        $stmt = mysqli_prepare($conn, "UPDATE sections SET section_name = ?, class_id = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'sii', $section_name, $class_id, $id);
        mysqli_stmt_execute($stmt);
        set_message('Section updated successfully.');
    }
    redirect('manage_section.php');
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM sections WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    set_message('Section deleted.');
    redirect('manage_section.php');
}

$page_title = 'Manage Sections';
$sections = mysqli_query($conn, "SELECT sec.*, c.class_name FROM sections sec JOIN classes c ON c.id = sec.class_id ORDER BY sec.id DESC");
$classes = mysqli_query($conn, "SELECT id, class_name FROM classes ORDER BY class_name");
$classes_arr = [];
while ($c = mysqli_fetch_assoc($classes)) { $classes_arr[] = $c; }

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="grid-2">
    <div class="card">
        <h3>All Sections</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>#</th><th>Section</th><th>Class</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($sections) === 0): ?>
                    <tr><td colspan="4" class="empty-state">No sections added yet.</td></tr>
                <?php else: $i = 1; while ($s = mysqli_fetch_assoc($sections)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($s['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['class_name']); ?></td>
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
        <h3>Add New Section</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Section Name</label>
                <input type="text" name="section_name" required placeholder="e.g. Section A">
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
            <button type="submit" class="btn btn-primary btn-block">Add Section</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
