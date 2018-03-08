<?php
//初始化
require 'init.php';
//注册个logger
Service\Logger\LoggerFactory::set('msg', new \Service\Logger\MsgLogger());
//使用Myapp体系处理http请求
Framework\MyApp::create()->handle();
