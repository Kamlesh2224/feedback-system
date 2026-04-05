<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <div class="dashboard-container">
        <header class="dash-header">
            <div>
                <h1 class="dash-title">Welcome back, Scholar</h1>
            </div>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </header>

        <div class="dash-grid">
            
            <a href="categories.php" class="dash-card">
                <div class="card-icon">📣</div>
                <div class="card-content">
                    <h3>Give Feedback</h3>
                    <p>Share your thoughts on faculty and campus facilities.</p>
                </div>
                <div class="card-arrow">→</div>
            </a>

            <a href="profile.php" class="dash-card ">
                <div class="card-icon">👤</div>
                <div class="card-content">
                    <h3>My Profile</h3>
                    <p>View and edit your personal academic information.</p>
                </div>
                <div class="card-arrow">→</div>
            </a>

        </div>
    </div>

</body>
</html>