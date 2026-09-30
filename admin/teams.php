<?php
require_once "../config/database.php";
require_once "../config/auth.php";
admin_required();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $short = strtoupper(trim($_POST["short_name"]));
    if ($name && $short) {
        $stmt = $conn->prepare("INSERT INTO teams(name, short_name) VALUES(?,?)");
        $stmt->bind_param("ss", $name, $short);
        $stmt->execute();
    }
    header("Location: teams.php");
    exit;
}

if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM teams WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: teams.php");
    exit;
}

$teams = $conn->query("SELECT * FROM teams ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teams</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="topbar"><div class="brand">TPL Admin</div><nav><a href="dashboard.php">Dashboard</a><a href="matches.php">Matches</a><a href="players.php">Players</a><a href="logout.php">Logout</a></nav></header>
<main class="container">
<h1>Teams</h1>
<form class="form-card" method="post">
    <input name="name" placeholder="Team name" required>
    <input name="short_name" placeholder="Short name e.g. KTM" required maxlength="20">
    <button class="btn">Add Team</button>
</form>
<table>
<tr><th>ID</th><th>Name</th><th>Short</th><th>Action</th></tr>
<?php while($t=$teams->fetch_assoc()): ?>
<tr>
<td><?= $t['id'] ?></td>
<td><?= htmlspecialchars($t['name']) ?></td>
<td><?= htmlspecialchars($t['short_name']) ?></td>
<td><a class="danger-link" href="?delete=<?= $t['id'] ?>" onclick="return confirm('Delete team?')">Delete</a></td>
</tr>
<?php endwhile; ?>
</table>
</main>
</body>
</html>
