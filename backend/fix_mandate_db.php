<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=gad_submission_system', 'root', '');

$fullTitle = "Lack of senior citizens access to gender-responsive programs and services/DBM-DSWD Joint Circular No. 2003-01 provides guidelines for the implementation of Section 29 of the General Appropriations Act (GAA), requiring government agencies to set aside at least 1% of their budget for programs and projects related to senior citizens and persons with disabilities (PWDs)";

// Find the ID of the truncated mandate
$stmt = $db->query("SELECT id FROM gad_mandates WHERE title LIKE 'Lack of senior citizens access to gender-responsive programs and services%'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $updateStmt = $db->prepare("UPDATE gad_mandates SET title = :title WHERE id = :id");
    $updateStmt->execute(['title' => $fullTitle, 'id' => $row['id']]);
    echo "Successfully fixed the truncated GAD mandate in the database.";
} else {
    echo "Mandate not found.";
}
