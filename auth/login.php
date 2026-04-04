<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prn = $_POST['prn'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM students WHERE prn=?");
    $stmt->bind_param("s", $prn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id'];
            header("Location: ../student/dashboard.php");
            exit();
        } else { $error = "The password you entered is incorrect."; }
    } else { $error = "No account found with that PRN."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Student Portal</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

    <div class="auth-card">
        <h1 class="auth-title">Student Login</h1>
        <?php if(isset($error)): ?>
            <div class="msg msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label>PRN Number</label>
                <input type="text" name="prn" placeholder="e.g. 20241001" required>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>

        <hr class="auth-sep">
        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one now</a>
        </div>
    </div>

</body>
</html>