<?php
require_once "../config/database.php";
require_once "../config/auth.php";
admin_required();

if (isset($_GET["start"])) {
    $id=(int)$_GET["start"];
    $m=$conn->query("SELECT * FROM matches WHERE id=$id")->fetch_assoc();
    if ($m) {
        $conn->begin_transaction();
        try {
            $stmt=$conn->prepare("UPDATE matches SET status='live', current_innings=1 WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();

            $stmt=$conn->prepare("INSERT INTO innings(match_id, innings_number, batting_team_id) VALUES(?,1,?)");
            $stmt->bind_param("ii",$id,$m["team1_id"]);
            $stmt->execute();
            $conn->commit();
        } catch(Exception $e) {
            $conn->rollback();
        }
    }
    header("Location: matches.php"); exit;
}

if (isset($_GET["score"])) {
    header("Location: score.php?match_id=".(int)$_GET["score"]); exit;
}

if ($_SERVER["REQUEST_METHOD"]==="POST") {
    $t1=(int)$_POST["team1_id"]; $t2=(int)$_POST["team2_id"];
    $venue=trim($_POST["venue"]); $date=$_POST["match_date"];
    if($t1 && $t2 && $t1!==$t2){
        $stmt=$conn->prepare("INSERT INTO matches(team1_id,team2_id,venue,match_date) VALUES(?,?,?,?)");
        $stmt->bind_param("iiss",$t1,$t2,$venue,$date);
        $stmt->execute();
    }
    header("Location: matches.php"); exit;
}

$teams=$conn->query("SELECT * FROM teams ORDER BY name");
$matches=$conn->query("SELECT m.*,a.name team1,b.name team2 FROM matches m JOIN teams a ON m.team1_id=a.id JOIN teams b ON m.team2_id=b.id ORDER BY m.match_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Matches</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<header class="topbar"><div class="brand">TPL Admin</div><nav><a href="dashboard.php">Dashboard</a><a href="teams.php">Teams</a><a href="players.php">Players</a><a href="logout.php">Logout</a></nav></header>
<main class="container">
<h1>Matches</h1>
<form class="form-card" method="post">
<select name="team1_id" required><option value="">Team 1</option><?php while($t=$teams->fetch_assoc()): ?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option><?php endwhile; ?></select>
<?php $teams2=$conn->query("SELECT * FROM teams ORDER BY name"); ?>
<select name="team2_id" required><option value="">Team 2</option><?php while($t=$teams2->fetch_assoc()): ?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option><?php endwhile; ?></select>
<input name="venue" placeholder="Venue">
<input type="datetime-local" name="match_date" required>
<button class="btn">Create Match</button>
</form>
<table><tr><th>Match</th><th>Date</th><th>Status</th><th>Action</th></tr>
<?php while($m=$matches->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($m['team1']) ?> vs <?= htmlspecialchars($m['team2']) ?></td>
<td><?= date("d M Y H:i",strtotime($m['match_date'])) ?></td>
<td><?= htmlspecialchars($m['status']) ?></td>
<td>
<?php if($m['status']==='scheduled'): ?><a class="btn small" href="?start=<?= $m['id'] ?>">Start</a><?php endif; ?>
<?php if($m['status']==='live'): ?><a class="btn small" href="?score=<?= $m['id'] ?>">Update Score</a><?php endif; ?>
<a class="btn secondary small" href="../user/live_score.php?match_id=<?= $m['id'] ?>">View</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</main>
</body>
</html>
