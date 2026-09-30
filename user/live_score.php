<?php
require_once "../config/database.php";
$match_id=(int)($_GET["match_id"] ?? 0);
$match=$conn->query("SELECT m.*,a.name team1,b.name team2 FROM matches m JOIN teams a ON m.team1_id=a.id JOIN teams b ON m.team2_id=b.id WHERE m.id=$match_id")->fetch_assoc();
if(!$match) die("Match not found.");

$innings=$conn->query("SELECT * FROM innings WHERE match_id=$match_id ORDER BY innings_number DESC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($match["team1"]) ?> vs <?= htmlspecialchars($match["team2"]) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="topbar"><div class="brand">TPL Cricket Live</div><nav><a href="../index.php">Home</a><a href="matches.php">Matches</a></nav></header>
<main class="container">
<div class="scoreboard" id="liveScore">
<h1><?= htmlspecialchars($match["team1"]) ?> vs <?= htmlspecialchars($match["team2"]) ?></h1>
<?php if($innings): ?>
<div class="big-score" id="score"><?= $innings["runs"] ?>/<?= $innings["wickets"] ?></div>
<div class="overs" id="overs"><?= floor($innings["balls"]/6).".".($innings["balls"]%6) ?> Overs</div>
<p id="status"><?= strtoupper($match["status"]) ?></p>
<?php else: ?>
<p>Match has not started yet.</p>
<?php endif; ?>
</div>

<h2>Match Information</h2>
<div class="info-grid">
<div><strong>Teams</strong><br><?= htmlspecialchars($match["team1"]) ?> vs <?= htmlspecialchars($match["team2"]) ?></div>
<div><strong>Venue</strong><br><?= htmlspecialchars($match["venue"]) ?></div>
<div><strong>Date</strong><br><?= date("d M Y, h:i A",strtotime($match["match_date"])) ?></div>
</div>
</main>
<script>
const matchId = <?= $match_id ?>;
async function refreshScore() {
    try {
        const response = await fetch("../api/live_score.php?match_id=" + matchId, {cache:"no-store"});
        const data = await response.json();
        if (data.success) {
            document.getElementById("score").textContent = data.runs + "/" + data.wickets;
            document.getElementById("overs").textContent = data.overs + " Overs";
            document.getElementById("status").textContent = data.status.toUpperCase();
        }
    } catch(e) {}
}
setInterval(refreshScore, 3000);
</script>
</body>
</html>
