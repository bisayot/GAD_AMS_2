<?php
$db = mysqli_connect('localhost', 'root', '', 'gad_submission_system');
mysqli_query($db, 'UPDATE activity_design SET is_viewed_by_admin = 0');
mysqli_query($db, 'UPDATE accomplishment_report SET is_viewed_by_admin = 0');
echo "Reset successful.\n";
