<?php
header("Content-Type: application/json");
require_once "../config/database.php";

$match_id=(int)($_GET["match_id"] ?? 0);

$stmt=$conn->prepare("SELECT m.status, i.runs, i.wickets, i.balls
                      FROM matches m
                      LEFT JOIN innings i ON m.id=i.match_id
                      WHERE m.id=?
                      ORDER BY i.innings_number DESC LIMIT 1");
$stmt->bind_param("i",$match_id);
$stmt->execute();
$data=$stmt->get_result()->fetch_assoc();

if(!$data){
    echo json_encode(["success"=>false,"message"=>"Match not found"]);
    exit;
}

$balls=(int)($data["balls"] ?? 0);
echo json_encode([
    "success"=>true,
    "status"=>$data["status"],
    "runs"=>(int)($data["runs"] ?? 0),
    "wickets"=>(int)($data["wickets"] ?? 0),
    "overs"=>floor($balls/6).".".($balls%6)
]);
?>
