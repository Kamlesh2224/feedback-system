<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prn = $_POST['prn'];
    $name = $_POST['name'];
    $branch = $_POST['branch'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if PRN exists
    $check = $conn->prepare("SELECT id FROM students WHERE prn=?");
    $check->bind_param("s", $prn);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "PRN already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (prn, name, branch, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $prn, $name, $branch, $password);

        if ($stmt->execute()) {
            echo "Registration successful! <a href='login.php'>Login</a>";
        } else {
            echo "Error!";
        }
    }
}
?>

<h2>Register</h2>
<form method="POST">
    PRN: <input type="text" name="prn" required><br><br>
    Name: <input type="text" name="name" required><br><br>
    Branch: <input type="text" name="branch" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>