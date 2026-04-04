<!DOCTYPE html>
<html>
<head>
    <title>Feedback System</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
        }

        h1 {
            margin-bottom: 30px;
        }

        .btn {
            display: block;
            width: 200px;
            margin: 10px auto;
            padding: 12px;
            background: #334155;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #475569;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Feedback System</h1>

    <a href="auth/register.php" class="btn">Register</a>
    <a href="auth/login.php" class="btn">Login</a>
    <a href="admin/login.php" class="btn">Admin Login</a>
</div>

</body>
</html>