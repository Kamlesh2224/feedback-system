<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$category_id = $_GET['category_id'];
?>

<h2>Administrative Feedback</h2>

<ul>
    <li><a href="feedback.php?category_id=<?php echo $category_id; ?>&type=fees">Fees</a></li>
    <li><a href="feedback.php?category_id=<?php echo $category_id; ?>&type=admission">Admission</a></li>
    <li><a href="feedback.php?category_id=<?php echo $category_id; ?>&type=scholarship">Scholarship</a></li>
</ul>