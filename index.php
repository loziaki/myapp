<?php
//初始化
require 'init.php';
//注册logger
Service\Logger\LoggerFactory::set('file', new \Service\Logger\FileLogger());

//用路由控件找到一个view
$_view  = \Service\EasyRouter::path(addslashes(strip_tags(trim($_GET['_path']))));

//是否开启debug模式
//取决于会不会返回错误信息
Framework\MyApp::debugMode(true);

//使用Myapp体系处理http请求
//像django的view体系
$app = Framework\MyApp::create()->handle($_view);
