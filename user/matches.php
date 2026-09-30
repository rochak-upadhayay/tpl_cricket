<?php
require_once "../config/database.php";
$result=$conn->query("SELECT m.*,a.name team1,b.name team2 FROM matches m JOIN teams a ON m.team1_id=a.id JOIN teams b ON m.team2_id=b.id ORDER BY m.match_date DESC");
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Matches</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<header class="topbar"><div class="brand">TPL Cricket Live</div><nav><a href="../index.php">Home</a><a href="matches.php">Matches</a></nav></header>
<main class="container"><h1>All Matches</h1><div class="cards">
<?php while($m=$result->fetch_assoc()): ?>
<div class="card"><span class="status <?= htmlspecialchars($m['status']) ?>"><?= strtoupper($m['status']) ?></span><h3><?= htmlspecialchars($m['team1']) ?> vs <?= htmlspecialchars($m['team2']) ?></h3><p><?= htmlspecialchars($m['venue']) ?></p><a class="btn" href="live_score.php?match_id=<?= $m['id'] ?>">View Score</a></div>
<?php endwhile; ?>
</div></main>
</body></html>
