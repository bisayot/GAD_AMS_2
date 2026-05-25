<?php
// database.php
// Modify these values for your local XAMPP MySQL setup.
$DB_HOST = 'localhost';
$DB_NAME = 'gad_submission_system';
$DB_USER = 'root';
$DB_PASS = '';
$DB_PORT = 3308; // Default XAMPP MySQL port is 3306, change if needed.

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($mysqli->connect_error) {
    die('Database connection error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
