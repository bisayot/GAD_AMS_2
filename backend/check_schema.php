<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=gad_submission_system', 'root', '');
$stmt = $db->query('SHOW COLUMNS FROM users');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
