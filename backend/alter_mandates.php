<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=gad_submission_system', 'root', '');
$db->exec('ALTER TABLE gad_mandates MODIFY title TEXT;');
echo "Altered gad_mandates.title to TEXT.\n";
