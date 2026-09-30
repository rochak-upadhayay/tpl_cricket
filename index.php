<?php
require_once "config/database.php";

$sql = "SELECT m.*, t1.name AS team1, t2.name AS team2
        FROM matches m
        JOIN teams t1 ON m.team1_id=t1.id
        JOIN teams t2 ON m.team2_id=t2.id
        ORDER BY m.match_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TPL Cricket Live</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">TPL Cricket Live</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="user/matches.php">Matches</a>
        <a href="admin/login.php">Admin</a>
    </nav>
</header>

<main class="container">
    <section class="hero">
        <h1>TPL Cricket Live Score</h1>
        <p>Follow matches, scores, wickets and overs in real time.</p>
    </section>

    <h2>Matches</h2>
    <div class="cards">
    <?php while ($m = $result->fetch_assoc()): ?>
        <div class="card">
            <span class="status <?= htmlspecialchars($m['status']) ?>">
                <?= strtoupper(htmlspecialchars($m['status'])) ?>
            </span>
            <h3><?= htmlspecialchars($m['team1']) ?> vs <?= htmlspecialchars($m['team2']) ?></h3>
            <p><?= htmlspecialchars($m['venue']) ?></p>
            <p><?= date("d M Y, h:i A", strtotime($m['match_date'])) ?></p>
            <a class="btn" href="user/live_score.php?match_id=<?= (int)$m['id'] ?>">View Score</a>
        </div>
    <?php endwhile; ?>
    </div>
</main>
<script src="assets/js/script.js"></script>
</body>
</html>
