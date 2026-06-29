<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=gad_submission_system', 'root', '');
// Update TWG users
$stmt = $db->query("UPDATE users u JOIN user_profiles p ON u.id = p.user_id SET u.role = 'twg' WHERE p.user_role = 'TWG'");
echo "TWG updated: " . $stmt->rowCount() . "\n";

// Update Non-TWG users
$stmt = $db->query("UPDATE users u JOIN user_profiles p ON u.id = p.user_id SET u.role = 'non-twg' WHERE p.user_role = 'Non-TWG'");
echo "Non-TWG updated: " . $stmt->rowCount() . "\n";

// Fallback for any remaining 'college' users (if their profile isn't exactly TWG/Non-TWG, they become twg)
$stmt = $db->query("UPDATE users SET role = 'twg' WHERE role = 'college'");
echo "Fallback updated: " . $stmt->rowCount() . "\n";
