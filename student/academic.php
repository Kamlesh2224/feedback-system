<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$category_id = $_GET['category_id'];
$student_id = $_SESSION['student_id'];

// Get student branch
$stmt = $conn->prepare("SELECT branch FROM students WHERE id=?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$branch = $student['branch'];

// Fetch subject + teacher
$query = "
SELECT 
    st.id as st_id,
    s.name as subject_name,
    t.name as teacher_name
FROM subject_teacher st
JOIN subjects s ON st.subject_id = s.id
JOIN teachers t ON st.teacher_id = t.id
WHERE s.branch = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $branch);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Subject | Student Portal</title>
    <link rel="stylesheet" href="../assets/css/academic.css">
</head>
<body>

<div class="dashboard-container">
    <header class="dash-header">
        <div>
            
            <h1 class="dash-title">Academic Feedback</h1>
        </div>
        <a href="categories.php" class="btn-back">← Back</a>
    </header>

    <div class="subject-list">
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()) { ?>
                <div class="subject-row">
                    <div class="subject-info">
                        <span class="subject-tag">Subject</span>
                        <h3><?php echo htmlspecialchars($row['subject_name']); ?></h3>
                        <div class="teacher-meta">
                            <span class="icon">👤</span> 
                            <span><?php echo htmlspecialchars($row['teacher_name']); ?></span>
                        </div>
                    </div>
                    
                    <div class="subject-action">
                        <a href="feedback.php?st_id=<?php echo $row['st_id']; ?>&category_id=<?php echo $category_id; ?>" class="btn-action">
                            Give Feedback
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php else: ?>
            <div class="empty-state">
                <p>No subjects found for your branch.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>