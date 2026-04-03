<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$result = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Category</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Select Feedback Category</h2>

<div class="container">
<?php while($row = $result->fetch_assoc()) { ?>
    <div class="card" onclick="selectCategory(<?php echo $row['id']; ?>)">
        <?php echo $row['name']; ?>
    </div>
<?php } ?>
</div>

<script src="../assets/js/script.js"></script>

</body>
</html>