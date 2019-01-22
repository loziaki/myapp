<?php
define('BASE_DIR', dirname(__FILE__).'/');

require BASE_DIR.'RouteCompiler.php';

$routes_file_path = BASE_DIR.'routes.php';
$output_path = BASE_DIR.'../../routes.php';

MyApp\Routing\RouteCompiler::compile($routes_file_path)->output($output_path);


