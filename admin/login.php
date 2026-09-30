<?php
session_start();
require_once "../config/database.php";

if (!empty($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && hash("sha256", $password) === $admin["password"]) {
        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];
        header("Location: dashboard.php");
        exit;
    }

    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - TPL</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-box">
    <h1>Admin Login</h1>
    <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn" type="submit">Login</button>
    </form>
    <p>Demo: admin / admin123</p>
</div>
</body>
</html>
