<?php
//初始化
require 'init.php';
//注册logger
Service\Logger\LoggerFactory::set('opr', new \Service\Logger\OprDbLogger());

//用路由控件找到一个view
$_view  = \Service\EasyRouter::path(addslashes(strip_tags(trim($_GET['_path']))));

//使用Myapp体系处理http请求
//像django的view体系
Framework\MyApp::create()->handle($_view);
