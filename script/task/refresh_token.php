<?php
//初始化
require '../../init.php';
//直接调用某个Cli下的操作
(new Controller\CliController())->refreshAccessTokenAction();