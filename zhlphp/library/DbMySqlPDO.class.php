<?php
// +----------------------------------------------------------------------
// | Class  MysqlPDO操作基类
// +----------------------------------------------------------------------
// | Copyright (c) 2022
// +----------------------------------------------------------------------

defined('SYS_PATH') or define('SYS_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');    // 项目根目录(绝对路径)
defined('SYS_DEBUG') or define('SYS_DEBUG', false);                      // 调试开关
defined('SYS_DEBUG_LOG') or define('SYS_DEBUG_LOG', true);              // 系统日志开关
defined('SYS_ERR_PATH') or define('SYS_ERR_PATH', 'runtime/error/');   // 日志打印文件路径

class DbMySqlPDO{

    public $db;
    //定义静态变量保存当前类的实例
    private static $instance;

    //防止在外部实例化
    private function __construct($config){
        $HOST_NAME = 'mysql:host='.$config['db_host'];
        $HOST_NAME .= ';port='.$config['db_port'];
        $HOST_NAME .= ';dbname='.$config['db_name'];
        $HOST_NAME .= ';charset='.$config['db_char'];;
        $HOST_USER = $config['db_user'];
        $HOST_PWD = $config['db_pwd'];
        try{
            $conn = new \PDO($HOST_NAME, $HOST_USER, $HOST_PWD);
            //$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
            $conn->setAttribute(\PDO::ATTR_CASE,\PDO::CASE_NATURAL);
            $this->db = $conn;
        }catch(PDOException $e){
            /*$errorStr = '[' . date('Y-m-d H:i:s') . '][SYS] ' . dirname(__FILE__) . (__CLASS__) . ($e->getMessage()) . PHP_EOL;
            if(SYS_DEBUG_LOG){
                error_log($errorStr, 3, SYS_PATH . SYS_ERR_PATH . 'SYS'. date('Y_m_d') . '.log', 'extra');
            }
            if(SYS_DEBUG){
                echo "$errorStr<br>";
            }
            exit;*/
        }
    }

    //防止在外部克隆
    private function __clone(){

    }

    //通过静态公有的方法获取这个类的实例
    public static function getInstance($config){
        //当前对象不属于当前例就实例化
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
}
