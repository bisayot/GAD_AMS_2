<?php
$content = file_get_contents('http://localhost:8080/api/get-gad-mandates');
var_dump($content);
var_dump($http_response_header);
