<?php
$baseMappping = require(ROOT_PATH.'/include/Framework/baseMapping.php');
return array_merge($baseMappping,[
    'Middleware\TokenCheck' => ROOT_PATH.'/include/Middleware/TokenCheck.php',
    'Framework\Jwt' => ROOT_PATH.'/include/Middleware/TokenCheck.php',
]);