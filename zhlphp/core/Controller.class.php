<?php 
 
// +----------------------------------------------------------------------
// | Class  控制器基类
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

class Controller {
 
    protected $_controller;
    protected $_action;
    protected $_view;
	protected $fun;
    // 构造函数，初始化属性，并实例化对应模型
    function __construct($controller, $action) {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = new View($controller, $action);
		$this->fun = Fun::getInstance();
    }
	
	/**
	 * @fun   设置变量方法
	 * @desc  
	 */
    function set($name, $value) {
        $this->_view->set($name, $value);
    }
	
	/**
	 * @fun   加载模版
	 * @desc  
	 */
	public function display($template=''){
		$this->_view->render($template);
	}

    /**
     * 自动加载模版
     */
    /*function __destruct() {
        $this->_view->render();
    }*/
 
}