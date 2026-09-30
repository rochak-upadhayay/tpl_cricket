<?php
require_once "../config/database.php";
require_once "../config/auth.php";
admin_required();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $team_id = (int)$_POST["team_id"];
    $name = trim($_POST["name"]);
    $role = trim($_POST["role"]);
    if ($team_id && $name) {
        $stmt = $conn->prepare("INSERT INTO players(team_id,name,role) VALUES(?,?,?)");
        $stmt->bind_param("iss", $team_id, $name, $role);
        $stmt->execute();
    }
    header("Location: players.php");
    exit;
}

if (isset($_GET["delete"])) {
    $id=(int)$_GET["delete"];
    $stmt=$conn->prepare("DELETE FROM players WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location: players.php");
    exit;
}

$teams=$conn->query("SELECT * FROM teams ORDER BY name");
$players=$conn->query("SELECT p.*,t.name team_name FROM players p JOIN teams t ON p.team_id=t.id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Players</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<header class="topbar"><div class="brand">TPL Admin</div><nav><a href="dashboard.php">Dashboard</a><a href="matches.php">Matches</a><a href="teams.php">Teams</a><a href="logout.php">Logout</a></nav></header>
<main class="container">
<h1>Players</h1>
<form class="form-card" method="post">
<select name="team_id" required><option value="">Select team</option><?php while($t=$teams->fetch_assoc()): ?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option><?php endwhile; ?></select>
<input name="name" placeholder="Player name" required>
<select name="role"><option>Player</option><option>Batsman</option><option>Bowler</option><option>All-rounder</option><option>Wicketkeeper</option></select>
<button class="btn">Add Player</button>
</form>
<table><tr><th>Name</th><th>Team</th><th>Role</th><th>Action</th></tr>
<?php while($p=$players->fetch_assoc()): ?>
<tr><td><?= htmlspecialchars($p['name']) ?></td><td><?= htmlspecialchars($p['team_name']) ?></td><td><?= htmlspecialchars($p['role']) ?></td><td><a class="danger-link" href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete player?')">Delete</a></td></tr>
<?php endwhile; ?>
</table>
</main>
</body>
</html>
