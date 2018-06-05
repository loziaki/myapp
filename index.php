<?php
//初始化
require 'init.php';
//注册logger
Service\Logger\LoggerFactory::set('opr', new \Service\Logger\OprDbLogger());
//使用Myapp体系处理http请求
Framework\MyApp::create()->handle();
