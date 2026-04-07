<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// GET Parameters
$category_id = $_GET['category_id'] ?? null;
$st_id = $_GET['st_id'] ?? null;

// QUESTIONS Logic
$questions = [];
if ($category_id == 1) {
    $questions = [
        1 => "How clearly does the instructor explain complex concepts?",
        2 => "Does the instructor demonstrate strong subject knowledge?",
        3 => "Is grading fair and transparent?",
        4 => "Does the instructor encourage participation?",
        5 => "How available is the instructor outside class?"
    ];
} elseif ($category_id == 2) {
    $questions = [
        1 => "How would you rate the quality of facilities?",
        2 => "Are resources adequate?",
        3 => "Is maintenance proper?",
        4 => "Are facilities accessible?",
        5 => "Overall satisfaction?"
    ];
} elseif ($category_id == 3) {
    $questions = [
        1 => "How smooth is the process?",
        2 => "Are issues resolved quickly?",
        3 => "Is communication clear?",
        4 => "Are staff helpful?",
        5 => "Overall satisfaction?"
    ];
}

// SUBMIT Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $st_id = !empty($_POST['st_id']) ? $_POST['st_id'] : null;

    // Duplicate check for academic
    if ($category_id == 1) {
        $check = $conn->prepare("SELECT id FROM feedback WHERE student_id=? AND subject_teacher_id=?");
        $check->bind_param("ii", $student_id, $st_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error_msg = "You have already submitted feedback for this subject!";
        }
    }

    if (!isset($error_msg)) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO feedback (student_id, category_id, subject_teacher_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $student_id, $category_id, $st_id);
            $stmt->execute();
            $feedback_id = $stmt->insert_id;

            foreach ($_POST['rating'] as $q_no => $rating) {
                $stmt = $conn->prepare("INSERT INTO feedback_answers (feedback_id, question_number, rating) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $feedback_id, $q_no, $rating);
                $stmt->execute();
            }

            $conn->commit();
            $success_msg = "Feedback submitted successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback | Student Portal</title>
    <link rel="stylesheet" href="../assets/css/feedback.css">
</head>
<body>

<div class="dashboard-container">
    <header class="dash-header">
        <h1 class="dash-title">Feedback Form</h1>
        <a href="academic.php?category_id=<?php echo $category_id; ?>" class="btn-back">← Back</a>
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
    <form method="POST" class="feedback-form">
        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
        <input type="hidden" name="st_id" value="<?php echo $st_id; ?>">

        <?php foreach ($questions as $q_no => $q_text): ?>
            <div class="question-card">
                <div class="q-number">Question <?php echo $q_no; ?></div>
                <p class="q-text"><?php echo $q_text; ?></p>
                
                <div class="rating-group">
                    <?php 
                    $options = [1 => "Very Bad", 2 => "Bad", 3 => "Good", 4 => "Very Good", 5 => "Excellent"];
                    foreach ($options as $val => $label): ?>
                        <label class="rating-option">
                            <input type="radio" name="rating[<?php echo $q_no; ?>]" value="<?php echo $val; ?>" required>
                            <span class="rating-label"><?php echo $val; ?> <?php echo $label; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Transmit Feedback</button>
        </div>
    </form>
    <?php endif; ?>
</div>

</body>
</html>