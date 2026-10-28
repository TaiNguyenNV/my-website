<?php
// index.php (Thư mục gốc)
session_start();

if (isset($_SESSION['user'])) {
    header("Location: views/dashboard.php");
    exit();
} else {
    header("Location: views/login.php");
    exit();
}
?>