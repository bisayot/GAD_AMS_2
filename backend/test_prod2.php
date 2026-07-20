<?php
$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$content = file_get_contents('https://gad-ams-2-1.onrender.com/api/get-gad-mandates', false, $context);
echo $content;
