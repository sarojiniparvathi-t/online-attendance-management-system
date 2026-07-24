# Online Student Attendance System

A simple full-stack **Online Student Attendance Management System** built with **HTML, CSS, JavaScript, PHP and MySQL**, with three modules: **Admin**, **Staff**, and **Student**.

This has been fully built and tested (PHP 8.3 + MariaDB) — every login, CRUD action, and the attendance marking workflow was verified end-to-end before delivery.

---

## ✅ Features

### Admin
- Dashboard with live stats (total students, staff, classes, subjects, today's attendance)
- Manage Staff (add / edit / delete)
- Manage Students (add / edit / delete)
- Manage Classes (add / edit / delete)
- Manage Sections (add / delete)
- Manage Subjects (add / delete, assign a staff member to each subject)
- View Attendance (filter by class, section, subject, date)
- Change Password

### Staff
- Dashboard showing assigned subjects and quick stats
- Take Attendance — pick a subject + date, mark every student Present/Absent, submit (re-opening the same subject/date lets you edit that day's attendance)
- View Attendance history (filter by subject/date)
- Change Password

### Student
- Self-registration (Full Name, Roll No, Class, Section, Email, Password)
- Dashboard with attendance percentage + present/absent/total counts
- View full attendance history (filter by subject)
- Change Password

---

## 🗂️ Project Structure

```
attendance_system/
├── index.php                 Login page (role tabs: Student / Staff / Admin)
├── login_process.php         Handles authentication for all 3 roles
├── register.php              Student self-registration
├── logout.php
├── config/
│   └── db_connect.php        Database connection settings (edit this first!)
├── includes/
│   ├── functions.php         Session helpers, guards, flash messages
│   ├── header.php            Shared <head> + layout opening
│   ├── sidebar.php           Role-based sidebar + topbar
│   └── footer.php            Shared layout closing + JS include
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── admin/
│   ├── dashboard.php
│   ├── manage_staff.php / staff_form.php / delete_staff.php
│   ├── manage_student.php / student_form.php / delete_student.php
│   ├── manage_class.php
│   ├── manage_section.php
│   ├── manage_subject.php
│   ├── view_attendance.php
│   └── change_password.php
├── staff/
│   ├── dashboard.php
│   ├── take_attendance.php
│   ├── view_attendance.php
│   └── change_password.php
├── student/
│   ├── dashboard.php
│   ├── view_attendance.php
│   └── change_password.php
└── database/
    └── attendance_system.sql   Full schema + sample data
```

---

## ⚙️ Setup Instructions (XAMPP / WAMP)

1. **Copy the project folder** into your server's web root:
   - XAMPP (Windows): `C:\xampp\htdocs\attendance_system`
   - WAMP: `C:\wamp64\www\attendance_system`

2. **Start Apache and MySQL** from the XAMPP/WAMP control panel.

3. **Create the database:**
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Click **Import**, choose `database/attendance_system.sql`, and click **Go**.
   - This creates the `attendance_system` database with all tables and sample data.

4. **Check the DB config** in `config/db_connect.php` — the defaults (`localhost`, user `root`, no password) match a standard XAMPP install. Update if your setup differs.

5. **Open the app** in your browser:
   ```
   http://localhost/attendance_system/index.php
   ```

---

## 🔑 Sample Login Credentials

| Role    | Email                     | Password    |
|---------|---------------------------|-------------|
| Admin   | admin@attendance.com      | admin123    |
| Staff   | staff@attendance.com      | staff123    |
| Student | student@attendance.com    | student123  |

You can also register a brand-new student account from the login page ("Register Now").

---

## 🧪 Suggested Demo Flow

1. Log in as **Admin** → add a Class/Section/Subject → assign the subject to the sample Staff member.
2. Log in as **Staff** → go to **Take Attendance** → pick the subject + today's date → mark students Present/Absent → Submit.
3. Log in as that **Student** → check the Dashboard to see the updated attendance percentage, and **My Attendance** for the full history.
4. Back in **Admin → View Attendance**, filter by class/subject/date to see everything staff have marked.

---

## 🔒 Security Notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- All database queries use **prepared statements** to prevent SQL injection.
- Each dashboard area is protected by a session role-guard (`require_admin()`, `require_staff()`, `require_student()`), so users can't access pages outside their role by guessing a URL.
- A unique DB constraint on `(student_id, subject_id, attendance_date)` stops duplicate attendance rows — resubmitting the same day's form simply updates the existing record instead of creating a new one.

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3 (custom, no framework), vanilla JavaScript
- **Backend:** PHP 8 (mysqli, prepared statements)
- **Database:** MySQL / MariaDB
