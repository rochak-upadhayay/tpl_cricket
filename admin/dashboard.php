<?php
require_once "../config/database.php";
require_once "../config/auth.php";
admin_required();

$live = $conn->query("SELECT COUNT(*) c FROM matches WHERE status='live'")->fetch_assoc()['c'];
$teams = $conn->query("SELECT COUNT(*) c FROM teams")->fetch_assoc()['c'];
$players = $conn->query("SELECT COUNT(*) c FROM players")->fetch_assoc()['c'];
$matches = $conn->query("SELECT COUNT(*) c FROM matches")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">TPL Admin</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="matches.php">Matches</a>
        <a href="teams.php">Teams</a>
        <a href="players.php">Players</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
<main class="container">
<h1>Dashboard</h1>
<div class="stats">
    <div class="stat"><strong><?= $live ?></strong><span>Live Matches</span></div>
    <div class="stat"><strong><?= $teams ?></strong><span>Teams</span></div>
    <div class="stat"><strong><?= $players ?></strong><span>Players</span></div>
    <div class="stat"><strong><?= $matches ?></strong><span>Total Matches</span></div>
</div>
</main>
</body>
</html>
