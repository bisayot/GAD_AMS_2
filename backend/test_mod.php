<?php
require "vendor/autoload.php";
$db = \Config\Database::connect();
$active = $db->table("activity_design as ad")
    ->select("ad.act_design_id, ad.activity_title, ad.form_type, form_types.name as formLabel, ad.modification_request_status")
    ->join("form_types", "form_types.id = ad.form_type", "left")
    ->where("ad.modification_request_status", "pending")
    ->get()->getResultArray();
echo json_encode($active);

