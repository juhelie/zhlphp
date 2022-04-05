<?php
// +----------------------------------------------------------------------
// | Class  模型基类
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

class Model{
    protected $fun;
    protected $db;
    protected $dbConfig;

    /**
     * Model constructor.
     */
    function __construct() {
        // 传参处理
        $this->fun = Fun::getInstance();
        /** 数据库配置信息 **/
        // 包含数据库配置文件
        $databaseFile = SYS_PATH.'config/database.php';
        if(is_file($databaseFile)){
            $db_Con = require $databaseFile;
            if(!empty($db_Con)){
                $this->dbConfig = $db_Con;
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
        $errStr = '';
        foreach($mustParam as $v){
            if(!isset($dbParam[$v])){
                $errStr .= $v.',';
            }
        }
        return $errStr;
    }

    /**
     * Notes:连接指定数据库
     * User: ZhuHaili
     */
    public function conn($dbKey){
        // 判断指定的数据库
        if(!isset($this->dbConfig[$dbKey])){
            sysloger('Database parameter error:'.$dbKey,'core/Model.class.php',__LINE__);
        }
        // 验证指定的数据库配置参数合法性
        $dbError = $this->dbSignParam($this->dbConfig[$dbKey]);
        if($dbError){
            sysloger('Database parameter error:'.$dbError,'config/database.php / core/Model.class.php',__LINE__);
        }
        $dbKeyParam = $this->dbConfig[$dbKey];
        $dbType = $dbKeyParam['db_type']; // 数据库类型
        $dbLink = $dbKeyParam['db_link']; // 链接类型
        $this->db_fix = $dbKeyParam['db_fix']; // 表前缀
        if($dbType == 'mysql'){
            if($dbLink == 'mysqli'){
                $this->db = DbMySqli::getInstance($dbKeyParam);
            }else if($dbLink == 'pdo'){
                $db = DbMysqlPDO::getInstance($dbKeyParam);
                $this->db = $db->db;
                $this->db->db_fix = $db->db_fix;
            }
        }else if($dbType == 'oracle'){
            $db = DbOraclePDO::getInstance($dbKeyParam);
            $this->db = $db->db;
            $this->db->db_fix = $db->db_fix;
        }
        return $this->db;
    }

    /**
     * 析构方法
     */
    /*function __destruct() {
		
    }*/
}