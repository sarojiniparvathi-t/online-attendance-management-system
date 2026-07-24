<?php
require_once 'includes/functions.php';
require_once 'config/db_connect.php';

// Fetch classes and sections for the dropdowns
$classes = mysqli_query($conn, "SELECT id, class_name FROM classes ORDER BY class_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean($conn, $_POST['full_name']);
    $roll_no = clean($conn, $_POST['roll_no']);
    $class_id = (int) $_POST['class_id'];
    $section_id = (int) $_POST['section_id'];
    $email = clean($conn, $_POST['email']);
    $phone = clean($conn, $_POST['phone']);
    $password = $_POST['password'];

    if ($full_name === '' || $roll_no === '' || $email === '' || $password === '' || !$class_id || !$section_id) {
        set_message('Please fill in all required fields.', 'error');
        redirect('register.php');
    }

    // Check duplicate email / roll number
    $check = mysqli_prepare($conn, "SELECT id FROM students WHERE email = ? OR roll_no = ? LIMIT 1");
    mysqli_stmt_bind_param($check, 'ss', $email, $roll_no);
    mysqli_stmt_execute($check);
    if (mysqli_stmt_get_result($check)->fetch_assoc()) {
        set_message('A student with that email or roll number already exists.', 'error');
        redirect('register.php');
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO students (roll_no, full_name, email, phone, class_id, section_id, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssssiis', $roll_no, $full_name, $email, $phone, $class_id, $section_id, $hashed);

    if (mysqli_stmt_execute($stmt)) {
        set_message('Registration successful! You can now log in.');
        redirect('index.php?role=student');
    } else {
        set_message('Something went wrong. Please try again.', 'error');
        redirect('register.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Register | Student Attendance System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Student Register</h2>
        <?php show_message(); ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label>Roll No</label>
                <input type="text" name="roll_no" required placeholder="e.g. BCA004">
            </div>
            <div class="form-group">
                <label>Class</label>
                <select name="class_id" id="class_id" required onchange="loadSections()">
                    <option value="">-- Select Class --</option>
                    <?php while ($c = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['class_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="section_id" id="section_id" required>
                    <option value="">-- Select Class First --</option>
                </select>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" placeholder="Enter phone number">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <div class="auth-links">
            Already have an account? <a href="index.php?role=student">Login</a>
        </div>
    </div>
</div>

<script>
// Load sections dynamically based on selected class
const sectionsByClass = <?php
    $map = [];
    $sec_res = mysqli_query($conn, "SELECT id, section_name, class_id FROM sections ORDER BY section_name");
    while ($s = mysqli_fetch_assoc($sec_res)) {
        $map[$s['class_id']][] = ['id' => $s['id'], 'name' => $s['section_name']];
    }
    echo json_encode($map);
?>;

function loadSections() {
    const classId = document.getElementById('class_id').value;
    const sectionSelect = document.getElementById('section_id');
    sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
    if (sectionsByClass[classId]) {
        sectionsByClass[classId].forEach(function (sec) {
            const opt = document.createElement('option');
            opt.value = sec.id;
            opt.textContent = sec.name;
            sectionSelect.appendChild(opt);
        });
    }
}
</script>
</body>
</html>
