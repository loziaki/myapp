<?php
return [
    'POST' => [
        'test1' => [
            'action' => 'TestController@abc',
        ],
        'test3' => [
            'action' => 'TestController1@abc',
            'middleware' => 'lalala,bububu',
        ],
        'test7' => [
            'action' => 'TestController1@abc',
            'middleware' => 'lalala,bububu,kekeke',
        ],
        'test8' => [
            'action' => 'TestController1@abc',
            'middleware' => 'lalala,bububu,kekeke,gigigi',
        ],
        'test9' => [
            'action' => 'TestController1@abc',
            'middleware' => 'lalala,bububu,kekeke,gigigi',
        ],
    ],
    'GET' => [
        'test2' => [
            'action' => 'TestController@cde',
        ],
        'test4' => [
            'action' => 'TestController1@cde',
            'middleware' => 'lalala,bububu',
        ],
        'test6' => [
            'action' => 'TestController1@cde',
            'middleware' => 'lalala,bububu,jiujiujiu',
        ],
        'test5' => [
            'action' => 'TestController@cde',
            'middleware' => 'jiujiujiu',
        ],
    ],
];