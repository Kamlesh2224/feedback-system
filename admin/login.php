<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    // Tip: Use password_verify() in production for better security than md5
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials. Please check your access.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access | Secure Login</title>
    <link rel="stylesheet" href="../assets/css/adminLogin.css">
</head>
<body>

    <div class="auth-card">
        

        
        <h1 class="auth-title">Admin Login</h1>
        

        <?php if(isset($error)): ?>
            <div class="auth-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label for="username">Administrator ID</label>
                <input type="text" id="username" name="username" placeholder="e.g. admin_01" required>
            </div>

            <div class="field">
                <label for="password">Access Key</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary">Authenticate</button>
        </form>

        <hr class="auth-sep">

        
    </div>

</body>
</html>