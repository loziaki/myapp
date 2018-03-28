<?php
set_time_limit(0);
date_default_timezone_set("PRC");
//确定根目录
define('ROOT_PATH', dirname(__FILE__));

//引用配置
require ROOT_PATH.'/config/config.php';

//引用MyAutoLoader
require ROOT_PATH.'/framework/MyAutoloader.php';
Framework\MyAutoloader::register();

//引用Composer的autoloader
if (is_dir(ROOT_PATH.'/vendor')) {
    require ROOT_PATH.'/vendor/autoload.php';
} else {
    exit('plz run "composer installed" in command line');
}
