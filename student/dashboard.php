<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome to Dashboar</h2>

<a href="categories.php">Give Feedback</a>
<br><br>

<a href="../auth/logout.php">Logout</a>

</body>
</html>