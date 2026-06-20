<?php 
$db = new mysqli("127.0.0.1", "root", "", "gad_submission_system", 3306);
$res = $db->query("DESCRIBE activity_design");
while ($row = $res->fetch_assoc()) { print_r($row); }
$res = $db->query("DESCRIBE accomplishment_report");
while ($row = $res->fetch_assoc()) { print_r($row); }
?>
