<?php
$db = mysqli_connect('localhost', 'root', '', 'gad_submission_system');
mysqli_query($db, 'ALTER TABLE activity_design ADD COLUMN deleted_by INT NULL DEFAULT NULL AFTER deleted_at');
mysqli_query($db, 'ALTER TABLE accomplishment_report ADD COLUMN deleted_by INT NULL DEFAULT NULL AFTER deleted_at');
echo "Columns added.\n";
