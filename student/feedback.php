<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// GET first
$category_id = $_GET['category_id'] ?? null;
$st_id = $_GET['st_id'] ?? null;
$type = $_GET['type'] ?? null;

// POST override
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $st_id = !empty($_POST['st_id']) ? $_POST['st_id'] : null;
}

// Remove subject for non-academic
if ($category_id != 1) {
    $st_id = null;
}

// QUESTIONS
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

// SUBMIT
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Duplicate check ONLY for academic
    if ($category_id == 1) {
        $check = $conn->prepare("
            SELECT id FROM feedback 
            WHERE student_id=? AND subject_teacher_id=?
        ");
        $check->bind_param("ii", $student_id, $st_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "You already submitted feedback!";
            exit();
        }
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO feedback (student_id, category_id, subject_teacher_id)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param("iii", $student_id, $category_id, $st_id);
        $stmt->execute();

        $feedback_id = $stmt->insert_id;

        foreach ($_POST['rating'] as $q_no => $rating) {
            $stmt = $conn->prepare("
                INSERT INTO feedback_answers (feedback_id, question_number, rating)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iii", $feedback_id, $q_no, $rating);
            $stmt->execute();
        }

        $conn->commit();
        echo "Feedback submitted successfully!";

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>

<h2>Feedback Form</h2>

<form method="POST">
    <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
    <input type="hidden" name="st_id" value="<?php echo $st_id; ?>">

<?php foreach ($questions as $q_no => $q_text) { ?>
    <p><?php echo $q_text; ?></p>

    <label><input type="radio" name="rating[<?php echo $q_no; ?>]" value="1" required> Very Bad</label>
    <label><input type="radio" name="rating[<?php echo $q_no; ?>]" value="2"> Bad</label>
    <label><input type="radio" name="rating[<?php echo $q_no; ?>]" value="3"> Good</label>
    <label><input type="radio" name="rating[<?php echo $q_no; ?>]" value="4"> Very Good</label>
    <label><input type="radio" name="rating[<?php echo $q_no; ?>]" value="5"> Excellent</label>

    <br><br>
<?php } ?>

<button type="submit">Submit Feedback</button>
</form>