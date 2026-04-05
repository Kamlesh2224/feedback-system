<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$stmt = $conn->prepare("SELECT prn, name, branch FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Student Portal</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>

    <div class="dashboard-container">
        <header class="dash-header">
            <div>
                
                <h1 class="dash-title">My Profile</h1>
            </div>
            <a href="dashboard.php" class="btn-back">← Dashboard</a>
        </header>

        <div class="profile-card">
            <div class="profile-avatar-section">
                <div class="avatar-circle">
                    <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                </div>
                <div class="avatar-info">
                    <h2><?php echo htmlspecialchars($student['name']); ?></h2>
                    <p><?php echo htmlspecialchars($student['branch']); ?> Department</p>
                    
                </div>
            </div>

            <div class="profile-details-grid">
                <div class="detail-item">
                    <label>University PRN</label>
                    <div class="detail-value"><?php echo htmlspecialchars($student['prn']); ?></div>
                </div>

                <div class="detail-item">
                    <label>Academic Branch</label>
                    <div class="detail-value"><?php echo htmlspecialchars($student['branch']); ?></div>
                </div>

                <div class="detail-item">
                    <label>Account Status</label>
                    <div class="detail-value status-active">● Verified Student</div>
                </div>
            </div>

            <hr class="auth-sep">

            <div class="profile-actions">
                <button class="btn-secondary" onclick="alert('Update feature coming soon!')">Edit Profile</button>
                <a href="../auth/logout.php" class="link-danger">Sign Out of Session</a>
            </div>
        </div>
    </div>

</body>
</html>