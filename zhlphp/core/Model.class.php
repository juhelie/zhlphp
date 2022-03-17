<?php
// +----------------------------------------------------------------------
// | Class  模型基类
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

require_once SYS_ROOT.'library/MySqls.php';
require_once SYS_ROOT.'library/MysqlPDO.php';
require_once SYS_ROOT.'library/OraclePDO.php';

class Model  {
    protected $fun;
    protected $db;
    function __construct() {
        // 传参处理
        $this->fun = Fun::getInstance();
        /** 连接数据库 **/
        // 包含数据库配置文件
        $databaseFile = SYS_PATH.'config/database.php';
        if(file_exists($databaseFile)){
            $db_Con = require $databaseFile;
            // 数据库文件参数不为空
            if(!empty($db_Con)){
                // 多数据循环配置
                $dbs = array();
                foreach($db_Con as $dbKey=>$dbConfig){
                    // 检测数据库设置参数
                    $dbError = $this->dbSignParam($dbConfig);
                    if(!$dbError){
                        // 分配数据库
                        if($dbConfig['db_type'] == 'mysql'){
                            // mysql分配连接类型
                            if($dbConfig['db_link'] == 'mysqli'){
                                $db = MySqls::getInstance($dbConfig);
                                $dbs[$dbKey] = $db;
                                //$this->$dbAttr = $db;
                            }else if($dbConfig['db_link'] == 'pdo') {
                                $db = MysqlPDO::getInstance($dbConfig);
                                //$this->$dbAttr = $db->db;
                                $dbs[$dbKey] = $db->db;
                            }
                        }else if($dbConfig['db_type'] == 'oracle'){
                            // oracle
                            $db = OraclePDO::getInstance($dbConfig);
                            //$this->$dbAttr = $db->db;
                            $dbs[$dbKey] = $db->db;
                        }
                    }
                }
                $this->db = $dbs;
            }
        }
    }

    /**
     * Notes: 判断数据库参数是否正确
     * User: ZhuHaili
     * Date: 2022/3/17
     */
    private function dbSignParam($dbParam){
        $mustParam = array('db_type','db_link','db_host','db_port',
            'db_name','db_user','db_pwd','db_fix','db_char');
        foreach($mustParam as $v){
            if(!isset($dbParam[$v])){
                sysloger('Database parameter error:'.$v,'config/database.php','');
                return $v;
            }
        }
    }

    /*function __destruct() {
		
    }*/
}