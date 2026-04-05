<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prn = $_POST['prn'];
    $name = $_POST['name'];
    $branch = $_POST['branch'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM students WHERE prn=?");
    $check->bind_param("s", $prn);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "This PRN is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (prn, name, branch, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $prn, $name, $branch, $password);
        if ($stmt->execute()) {
            $success = "Account created successfully!";
        } else { $error = "Something went wrong. Please try again."; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Student Portal</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

    <div class="auth-card">
       
        <h1 class="auth-title">Create Account</h1>
        

        <?php if(isset($error)): ?>
            <div class="msg msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
            <div class="msg msg-success">
                <?php echo $success; ?> <a href="login.php" style="font-weight:700">Login here</a>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label>PRN Number</label>
                <input type="text" name="prn" placeholder="University PRN" required>
            </div>
            
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="field">
                <label>Branch / Department</label>
                <select name="branch" required>
                    <option value="" disabled selected>Select your branch</option>
                    <option value="CSE">CSE</option>
                    <option value="IT">Information Technology</option>
                    <option value="MECH">Mechanical</option>
                    <option value="ENTC">E&TC</option>
                </select>
            </div>

            <div class="field">
                <label>Create Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
                <p class="field-hint">Must be at least 8 characters long.</p>
            </div>

            <button type="submit" class="btn-primary">Register Account</button>
        </form>

        <hr class="auth-sep">
        <div class="auth-footer">
            Already registered? <a href="login.php">Sign In</a>
        </div>
    </div>

</body>
</html>