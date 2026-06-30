<?php
require 'backend/vendor/autoload.php';
// Boot Codeigniter
$app = \Config\Services::codeigniter(new \Config\App());
$app->initialize();

$userId = 2; // fake
$adModel = new \App\Models\ActivityDesignModel();
$adQuery = $adModel->onlyDeleted()
    ->select('activity_design.act_design_id as id, activity_design.activity_title as title, activity_design.form_type as type, users.full_name as submitter_name, activity_design.deleted_at')
    ->join('users', 'users.id = activity_design.user_id', 'left');
    
if ($userId) {
    $adQuery->where('activity_design.user_id', $userId);
}

echo $adModel->builder()->getCompiledSelect();
