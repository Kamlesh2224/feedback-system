<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$category_id = $_GET['category_id'] ?? 4; // Assuming 4 is 'Bug/Technical'

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];

    $stmt = $conn->prepare("
        INSERT INTO feedback (student_id, category_id, description)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $student_id, $category_id, $description);
    
    if ($stmt->execute()) {
        $success_msg = "Bug reported successfully! Our team will look into it.";
    } else {
        $error_msg = "Error submitting report. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Bug | Student Portal</title>
    <link rel="stylesheet" href="../assets/css/bug.css">
</head>
<body>

<div class="dashboard-container">
    <header class="dash-header">
        <div>
            
            <h1 class="dash-title">Report a Bug</h1>
        </div>
        <a href="dashboard.php" class="btn-back">← Back</a>
    </header>

    <?php if(isset($success_msg)): ?>
        <div class="msg msg-success">
            <?php echo $success_msg; ?> 
            <a href="dashboard.php" style="color:var(--accent); font-weight:700; margin-left:10px;">Return Home</a>
        </div>
    <?php elseif(isset($error_msg)): ?>
        <div class="msg msg-error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <?php if(!isset($success_msg)): ?>
    <div class="report-card">
        <p class="q-text">Describe the issue in detail.</p>
        
        <form method="POST" class="bug-form">
            <div class="field">
                <textarea name="description" placeholder="e.g. The feedback button doesn't respond on mobile..." required></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">Submit Bug Report</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>