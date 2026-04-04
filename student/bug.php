<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$category_id = $_GET['category_id'] ?? 4;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $description = $_POST['description'];

    $stmt = $conn->prepare("
        INSERT INTO feedback (student_id, category_id, description)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $student_id, $category_id, $description);
    $stmt->execute();

    echo "<h3>Bug reported successfully!</h3>";
}
?>

<h2>Report a Bug</h2>

<form method="POST">
    <textarea name="description" required></textarea><br><br>
    <button type="submit">Submit</button>
</form>