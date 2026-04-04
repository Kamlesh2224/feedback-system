<?php
include('../config/db.php');

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=feedback.csv');

$output = fopen("php://output", "w");

// Header
fputcsv($output, ['ID','Category','Subject','Teacher','Description','Date']);

$query = "
SELECT 
    f.id,
    f.category_id,
    s.name,
    t.name,
    f.description,
    f.created_at
FROM feedback f
LEFT JOIN subject_teacher st ON f.subject_teacher_id = st.id
LEFT JOIN subjects s ON st.subject_id = s.id
LEFT JOIN teachers t ON st.teacher_id = t.id
";

$result = $conn->query($query);

while($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
?>