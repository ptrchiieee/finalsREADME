<?php
// db.php
$host = 'localhost';
$db_name = 'student_management';
$username = 'root';
$password = '';
$connected = mysqli_connect($host, $username, $password, $db_name);

if (!$connected) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
