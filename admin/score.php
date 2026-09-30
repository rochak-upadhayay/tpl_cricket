<?php
require_once "../config/database.php";
require_once "../config/auth.php";
admin_required();

$match_id=(int)($_GET["match_id"] ?? $_POST["match_id"] ?? 0);

$match=$conn->query("SELECT m.*,a.name team1,b.name team2 FROM matches m JOIN teams a ON m.team1_id=a.id JOIN teams b ON m.team2_id=b.id WHERE m.id=$match_id")->fetch_assoc();
if(!$match){ die("Match not found."); }

$innings=$conn->query("SELECT * FROM innings WHERE match_id=$match_id ORDER BY innings_number DESC LIMIT 1")->fetch_assoc();
if(!$innings){ die("Start the match first."); }

if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["action"])){
    $action=$_POST["action"];
    $runs=0; $wicket=0; $extra=0; $extra_type=null; $legal=1;

    if(in_array($action,["0","1","2","3","4","6"])) $runs=(int)$action;
    elseif($action==="W") $wicket=1;
    elseif($action==="wide"){ $extra=1; $extra_type="wide"; $legal=0; }
    elseif($action==="noball"){ $extra=1; $extra_type="noball"; $legal=0; }
    elseif($action==="bye"){ $extra=1; $extra_type="bye"; }

    $total=$runs+$extra;
    $new_runs=$innings["runs"]+$total;
    $new_wickets=$innings["wickets"]+$wicket;
    $new_balls=$innings["balls"]+$legal;

    $stmt=$conn->prepare("INSERT INTO balls(innings_id,ball_number,runs,extra_runs,extra_type,wicket) VALUES(?,?,?,?,?,?)");
    $types="iiiisi";
    $stmt->bind_param($types,$innings["id"],$new_balls,$runs,$extra,$extra_type,$wicket);
    $stmt->execute();

    $stmt=$conn->prepare("UPDATE innings SET runs=?, wickets=?, balls=? WHERE id=?");
    $stmt->bind_param("iiii",$new_runs,$new_wickets,$new_balls,$innings["id"]);
    $stmt->execute();

    header("Location: score.php?match_id=".$match_id); exit;
}

if(isset($_GET["finish"])){
    $stmt=$conn->prepare("UPDATE matches SET status='completed' WHERE id=?");
    $stmt->bind_param("i",$match_id); $stmt->execute();
    header("Location: matches.php"); exit;
}

$overs=floor($innings["balls"]/6).".".($innings["balls"]%6);
$balls=$conn->query("SELECT * FROM balls WHERE innings_id=".$innings["id"]." ORDER BY id DESC LIMIT 12");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Live Score Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<header class="topbar"><div class="brand">TPL Score Control</div><nav><a href="dashboard.php">Dashboard</a><a href="matches.php">Matches</a><a href="logout.php">Logout</a></nav></header>
<main class="container">
<div class="scoreboard">
<h1><?= htmlspecialchars($match["team1"]) ?> vs <?= htmlspecialchars($match["team2"]) ?></h1>
<div class="big-score"><?= $innings["runs"] ?>/<?= $innings["wickets"] ?></div>
<div class="overs"><?= $overs ?> Overs</div>
<p>Batting: <strong><?= htmlspecialchars($match["team1"]) ?></strong></p>
</div>

<h2>Update Score</h2>
<form method="post" class="score-buttons">
<input type="hidden" name="match_id" value="<?= $match_id ?>">
<?php foreach(["0","1","2","3","4","6","W"] as $a): ?><button name="action" value="<?= $a ?>" class="score-btn"><?= $a ?></button><?php endforeach; ?>
<button name="action" value="wide" class="score-btn extra">Wide</button>
<button name="action" value="noball" class="score-btn extra">No Ball</button>
<button name="action" value="bye" class="score-btn extra">Bye</button>
</form>
<a class="btn danger" href="?match_id=<?= $match_id ?>&finish=1" onclick="return confirm('Finish this match?')">Finish Match</a>

<h2>Recent Balls</h2>
<div class="balls">
<?php while($b=$balls->fetch_assoc()): ?>
<span><?= $b["wicket"] ? "W" : ($b["extra_type"] ? strtoupper(substr($b["extra_type"],0,1)) : $b["runs"]) ?></span>
<?php endwhile; ?>
</div>
</main>
</body>
</html>
