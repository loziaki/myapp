<?php
//使用framework\Routes 为数组添加内容


$routes = array(
    'GET' =>array(
        'test' => 'testController@test'
    ),
    
    'POST' => array(
        'login' => [
            'middleware' => 'GetWxInfo',
            'action' => 'UserController@login'
        ],
    ),
);
$tempRoutes = [
    'GET' => [
    ],
    'POST' => [
    ]
];
$routes = array_merge_recursive($tempRoutes,$routes);
return $routes;