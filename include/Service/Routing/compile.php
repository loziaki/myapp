<?php
define('BASE_DIR', dirname(__FILE__).'/');

spl_autoload_register(function($classname) {
    Switch($classname) {
        case 'Routing\Route':
            require BASE_DIR.'/Route.php';break;
        case 'Routing\RouteCollection':
            require BASE_DIR.'/RouteCollection.php';break;
    }
}, true, true);

require BASE_DIR.'../../routes.php';

\Routing\RouteCollection::compile();


