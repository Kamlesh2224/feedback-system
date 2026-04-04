<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get category from URL
if (!isset($_GET['category_id'])) {
    echo "Invalid access";
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

// Fetch subjects + teachers
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
<html>
<head>
    <title>Select Subject</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Select Subject & Teacher</h2>

<div class="container">
<?php while($row = $result->fetch_assoc()) { ?>
    
    <a href="feedback.php?st_id=<?php echo $row['st_id']; ?>&category_id=<?php echo $category_id; ?>" 
       style="text-decoration: none; color: inherit;">
       
        <div class="card">
            <h3><?php echo $row['subject_name']; ?></h3>
            <p><?php echo $row['teacher_name']; ?></p>
        </div>

    </a>

<?php } ?>
</div>

</body>
</html>