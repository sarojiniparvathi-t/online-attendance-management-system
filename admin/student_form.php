<?php
require_once '../includes/functions.php';
require_admin();
require_once '../config/db_connect.php';

$edit_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$student = [
    'roll_no' => '', 'full_name' => '', 'email' => '', 'phone' => '', 'gender' => 'Male',
    'dob' => '', 'address' => '', 'class_id' => '', 'section_id' => '', 'parent_name' => '', 'parent_phone' => ''
];

if ($edit_id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $edit_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $student = $row;
    } else {
        redirect('manage_student.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll_no = clean($conn, $_POST['roll_no']);
    $full_name = clean($conn, $_POST['full_name']);
    $email = clean($conn, $_POST['email']);
    $phone = clean($conn, $_POST['phone']);
    $gender = clean($conn, $_POST['gender']);
    $dob = $_POST['dob'] !== '' ? $_POST['dob'] : null;
    $address = clean($conn, $_POST['address']);
    $class_id = (int) $_POST['class_id'];
    $section_id = (int) $_POST['section_id'];
    $parent_name = clean($conn, $_POST['parent_name']);
    $parent_phone = clean($conn, $_POST['parent_phone']);
    $password = $_POST['password'];

    if ($roll_no === '' || $full_name === '' || $email === '' || !$class_id || !$section_id) {
        set_message('Please fill all required fields.', 'error');
        redirect('student_form.php' . ($edit_id ? "?id=$edit_id" : ''));
    }

    if ($edit_id) {
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE students SET roll_no=?, full_name=?, email=?, phone=?, gender=?, dob=?, address=?, class_id=?, section_id=?, parent_name=?, parent_phone=?, password=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssssssiisssi', $roll_no, $full_name, $email, $phone, $gender, $dob, $address, $class_id, $section_id, $parent_name, $parent_phone, $hashed, $edit_id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE students SET roll_no=?, full_name=?, email=?, phone=?, gender=?, dob=?, address=?, class_id=?, section_id=?, parent_name=?, parent_phone=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssssssiissi', $roll_no, $full_name, $email, $phone, $gender, $dob, $address, $class_id, $section_id, $parent_name, $parent_phone, $edit_id);
        }
        mysqli_stmt_execute($stmt);
        set_message('Student updated successfully.');
    } else {
        if ($password === '') {
            set_message('Password is required for new students.', 'error');
            redirect('student_form.php');
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO students (roll_no, full_name, email, phone, gender, dob, address, class_id, section_id, parent_name, parent_phone, password) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'sssssssiisss', $roll_no, $full_name, $email, $phone, $gender, $dob, $address, $class_id, $section_id, $parent_name, $parent_phone, $hashed);
        if (!mysqli_stmt_execute($stmt)) {
            set_message('A student with that email or roll number already exists.', 'error');
            redirect('student_form.php');
        }
        set_message('Student added successfully.');
    }
    redirect('manage_student.php');
}

$classes = mysqli_query($conn, "SELECT id, class_name FROM classes ORDER BY class_name");
$sections = mysqli_query($conn, "SELECT id, section_name, class_id FROM sections ORDER BY section_name");
$sections_arr = [];
while ($s = mysqli_fetch_assoc($sections)) { $sections_arr[] = $s; }

$page_title = $edit_id ? 'Edit Student' : 'Add Student';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="card" style="max-width: 640px;">
    <h3><?php echo $edit_id ? 'Edit Student' : 'Add New Student'; ?></h3>
    <form method="POST">
        <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required value="<?php echo htmlspecialchars($student['full_name']); ?>">
            </div>
            <div class="form-group">
                <label>Roll No</label>
                <input type="text" name="roll_no" required value="<?php echo htmlspecialchars($student['roll_no']); ?>">
            </div>
        </div>
        <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Class</label>
                <select name="class_id" id="class_id" required onchange="loadSections()">
                    <option value="">-- Select Class --</option>
                    <?php mysqli_data_seek($classes, 0); while ($c = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $student['class_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['class_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="section_id" id="section_id" required>
                    <option value="">-- Select --</option>
                </select>
            </div>
        </div>
        <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($student['email']); ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>
        </div>
        <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                        <option value="<?php echo $g; ?>" <?php echo $student['gender'] === $g ? 'selected' : ''; ?>><?php echo $g; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="2"><?php echo htmlspecialchars($student['address']); ?></textarea>
        </div>
        <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Parent Name</label>
                <input type="text" name="parent_name" value="<?php echo htmlspecialchars($student['parent_name']); ?>">
            </div>
            <div class="form-group">
                <label>Parent Phone</label>
                <input type="text" name="parent_phone" value="<?php echo htmlspecialchars($student['parent_phone']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Password <?php echo $edit_id ? '(leave blank to keep unchanged)' : ''; ?></label>
            <input type="password" name="password" placeholder="<?php echo $edit_id ? 'Leave blank to keep current password' : 'Set a password'; ?>">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $edit_id ? 'Update Student' : 'Add Student'; ?></button>
        <a href="manage_student.php" class="btn" style="background:#e5e7eb;">Cancel</a>
    </form>
</div>

<script>
const sectionsByClass = <?php echo json_encode($sections_arr); ?>;
const currentSection = <?php echo (int) $student['section_id']; ?>;

function loadSections() {
    const classId = document.getElementById('class_id').value;
    const sectionSelect = document.getElementById('section_id');
    sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
    sectionsByClass.filter(s => s.class_id == classId).forEach(function (sec) {
        const opt = document.createElement('option');
        opt.value = sec.id;
        opt.textContent = sec.section_name;
        if (sec.id == currentSection) opt.selected = true;
        sectionSelect.appendChild(opt);
    });
}
// Trigger on load if editing
if (document.getElementById('class_id').value) { loadSections(); }
</script>

<?php include '../includes/footer.php'; ?>
