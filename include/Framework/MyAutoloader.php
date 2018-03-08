<?php
namespace Base;

class MyAutoloader {
    public static $mappings = array();

    const MAPPING_PATH = ROOT_PATH.'/include/Framework/mapping.php';

    public static function register() {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }

        if (file_exists(self::MAPPING_PATH)) {
            self::$mappings = require(self::MAPPING_PATH);
        } else {
            self::$mappings = [];
        }

        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            return spl_autoload_register(array('Base\MyAutoloader', 'load'), true, true);
        } else {
            return spl_autoload_register(array('Base\MyAutoloader', 'load'));
        }
    }

    public static function load($class) {
        if(!class_exists($class)){
            //check if this is some mappings
            if (isset(static::$mappings[$class])) {
                include static::$mappings[$class];
                return;
            }

            //can not find in the mappings variable ,then load the file by namespace
            $dir  = ROOT_PATH.'/include/';
            $str  = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $file = $dir.$str.'.php';
            if (file_exists($file) && !class_exists($class, false)) {
                include $file;
            }
        }
    }
}