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
    <link rel="stylesheet" href="../assets/css/categories.css">
</head>
<body>

<div class="dashboard-container">
    <header class="dash-header">
        <div>
            <h1 class="dash-title">Select Feedback Type</h1>
        </div>
        <a href="dashboard.php" class="btn-back">← Back</a>
    </header>

    <div class="category-grid">
        <?php while($row = $result->fetch_assoc()) { ?>
            <div class="cat-card" onclick="selectCategory(<?php echo $row['id']; ?>)">
                <div class="cat-icon">📁</div>
                <div class="cat-info">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>Click to provide feedback for this department.</p>
                </div>
                <div class="cat-badge">Select</div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="../assets/js/script.js"></script>

</body>
</html>