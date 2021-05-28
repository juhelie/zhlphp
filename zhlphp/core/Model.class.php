<?php
// +----------------------------------------------------------------------
// | Class  模型基类
// +----------------------------------------------------------------------
// | Copyright (c) 2018
// +----------------------------------------------------------------------
require_once SYS_ROOT.'library/MySqls.php';
class Model extends MySqls {
    protected $_model;
    protected $_table;
    protected $fun;
    function __construct() {
 
        // 连接数据库
        $this->connect(SYS_DB_HOST,SYS_DB_USER,SYS_DB_PASSWORD,SYS_DB_NAME,SYS_DB_PORT);
 
        // 获取调用方法的类名
        $this->_model = get_class($this);
		
		// 从字符串右侧移除字符：Model
        $this->_model = rtrim($this->_model, 'Model');
 
        // 数据库表名与类名一致（把所有字符转换为小写）
        $modelArr = explode('_',$this->_model);
        $table = isset($modelArr[1]) ? $modelArr[1] : $modelArr[0];
        $this->_table = strtolower(SYS_DB_PREFIX.$table);
        $this->fun = new Fun();
		
    }
 
    function __destruct() {
		
    }
}