<?php
set_time_limit(0);
date_default_timezone_set("PRC");

//定义ROOT_PATH
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
}

//引用配置
require ROOT_PATH.'config/config.php';

//引用MyAutoLoader
require ROOT_PATH.'framework/MyAutoloader.php';
Framework\MyAutoloader::register();

//引用Composer的autoloader
if (is_dir(ROOT_PATH.'vendor')) {
    require ROOT_PATH.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
} else {
    throw new Error('composer is not ready.');
}