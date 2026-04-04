<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$category_id = $_GET['category_id'];
?>

<h2>Infrastructure Feedback</h2>

<ul>
    <li><a href="feedback.php?category_id=<?php echo $category_id; ?>&type=library">Library</a></li>
    <li><a href="feedback.php?category_id=<?php echo $category_id; ?>&type=wifi">WiFi</a></li>
    <li><a href="feedback.php?category_id=<?php echo $category_id; ?>&type=lab">Lab</a></li>
</ul>