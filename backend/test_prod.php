<?php
$content = @file_get_contents('https://gad-ams-2-1.onrender.com/api/get-gad-mandates');
var_dump($content);
var_dump($http_response_header);
