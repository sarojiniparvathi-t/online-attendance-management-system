<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

$edit_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$staff = ['full_name' => '', 'email' => '', 'phone' => '', 'gender' => 'Male', 'address' => ''];

if ($edit_id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM staff WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $edit_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $staff = $row;
    } else {
        redirect('manage_staff.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($conn, $_POST['full_name']);
    $email = clean($conn, $_POST['email']);
    $phone = clean($conn, $_POST['phone']);
    $gender = clean($conn, $_POST['gender']);
    $address = clean($conn, $_POST['address']);
    $password = $_POST['password'];

    if ($full_name === '' || $email === '') {
        set_message('Name and email are required.', 'error');
        redirect('staff_form.php' . ($edit_id ? "?id=$edit_id" : ''));
    }

    if ($edit_id) {
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE staff SET full_name=?, email=?, phone=?, gender=?, address=?, password=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssssssi', $full_name, $email, $phone, $gender, $address, $hashed, $edit_id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE staff SET full_name=?, email=?, phone=?, gender=?, address=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssssi', $full_name, $email, $phone, $gender, $address, $edit_id);
        }
        mysqli_stmt_execute($stmt);
        set_message('Staff updated successfully.');
    } else {
        if ($password === '') {
            set_message('Password is required for new staff.', 'error');
            redirect('staff_form.php');
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO staff (full_name, email, phone, gender, address, password) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssssss', $full_name, $email, $phone, $gender, $address, $hashed);
        if (!mysqli_stmt_execute($stmt)) {
            set_message('A staff member with that email already exists.', 'error');
            redirect('staff_form.php');
        }
        set_message('Staff added successfully.');
    }
    redirect('manage_staff.php');
}

$page_title = $edit_id ? 'Edit Staff' : 'Add Staff';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card" style="max-width: 560px;">
    <h3><?php echo $edit_id ? 'Edit Staff' : 'Add New Staff'; ?></h3>
    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required value="<?php echo htmlspecialchars($staff['full_name']); ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?php echo htmlspecialchars($staff['email']); ?>">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($staff['phone']); ?>">
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender">
                <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                    <option value="<?php echo $g; ?>" <?php echo $staff['gender'] === $g ? 'selected' : ''; ?>><?php echo $g; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="2"><?php echo htmlspecialchars($staff['address']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Password <?php echo $edit_id ? '(leave blank to keep unchanged)' : ''; ?></label>
            <input type="password" name="password" placeholder="<?php echo $edit_id ? 'Leave blank to keep current password' : 'Set a password'; ?>">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $edit_id ? 'Update Staff' : 'Add Staff'; ?></button>
        <a href="manage_staff.php" class="btn" style="background:#e5e7eb;">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
