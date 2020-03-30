<?php
namespace Framework;

class MyAutoloader
{
    public static $mappings = array();

    const DIY_MAPPING_PATH = ROOT_PATH.'include/mapping.php';
    const APP_MAPPING_PATH = ROOT_PATH.'framework/baseMapping.php';

    public static function register()
    {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }

        $baseMapping = require(self::APP_MAPPING_PATH);

        if (file_exists(self::DIY_MAPPING_PATH)) {
            $diyMappping = require(self::DIY_MAPPING_PATH);
            self::$mappings = array_merge($baseMapping, $diyMappping);
        } else {
            self::$mappings = $baseMappping;
        }

        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            return spl_autoload_register(array('Framework\MyAutoloader', 'load'), true, true);
        } else {
            return spl_autoload_register(array('Framework\MyAutoloader', 'load'));
        }
    }

    public static function load($class)
    {
        if (!class_exists($class)) {
            //check if this is some mappings
            if (isset(static::$mappings[$class])) {
                include static::$mappings[$class];
                return;
            }

            //can not find in the mappings variable ,then load the file by namespace
            $dir  = ROOT_PATH.'include'.DIRECTORY_SEPARATOR;
            $str  = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $file = $dir.$str.'.php';
            if (file_exists($file) && !class_exists($class, false)) {
                include $file;
            }
        }
    }
}
