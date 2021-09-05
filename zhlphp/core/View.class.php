<?php
// +----------------------------------------------------------------------
// | Class  视图基类
// +----------------------------------------------------------------------
// | Copyright (c) 2018
// +----------------------------------------------------------------------

class View {
 
    protected $var = array();
    protected $_controller;
    protected $_action;
 
    function __construct($controller, $action) {
        $this->_controller = $controller;
        $this->_action = $action;
    }
	
	/**
	 * @fun   设置变量方法
	 * @desc  
	 */
    function set($name, $value) {
        //$this->$name = $value;
        //$this->variables[$name] = $value;
        $this->var[$name] = $value;
    }
	
	/**
	 * @fun   显示视图
	 * @desc  
	 */
    function render($template) {
        extract($this->var); //从数组中将变量导入到当前的符号表。
		$controller = strtolower($this->_controller);
		$action = strtolower($this->_action);

        // 视图指定模版时
        if($template){
            $actionArr = explode('/',$template);
            $action = $actionArr[0];
            if(count($actionArr) > 1){ // 如果包含指定模型时
                $controller = $actionArr[0];
                $action = $actionArr[1];
            }
        }

        $defaultHeader = SYS_PATH.SYS_APP_PATH.'/'.SYS_PRO_PATH.'/views/header.php';
        $defaultFooter = SYS_PATH.SYS_APP_PATH.'/'.SYS_PRO_PATH.'/views/footer.php';
        $controllerHeader = SYS_PATH.SYS_APP_PATH.'/'.SYS_PRO_PATH.'/views/'.$controller.'/header.php';
        $controllerFooter = SYS_PATH.SYS_APP_PATH.'/'.SYS_PRO_PATH.'/views/'.$controller.'/footer.php';

        // 页头文件包含（控制器存在页头覆盖外层页头）
        if(file_exists($controllerHeader)){
            include ($controllerHeader);
        }else{
            if(file_exists($defaultHeader)){
                include ($defaultHeader);
            }
        }
        // 页面内容文件
		$contentPage = SYS_PATH.SYS_APP_PATH.'/'.SYS_PRO_PATH.'/views/'.$controller.'/'.$action.'.php';
		if(file_exists($contentPage)){
			include (SYS_PATH . SYS_APP_PATH.'/'.SYS_PRO_PATH.'/views/'.$controller.'/'.$action.'.php');
		}else{
			exit('error : 没有找到'.SYS_PRO_PATH.'/'.$controller.'/'.$action.'.php 视图文件');
		}
        // 页脚文件（控制器存在页脚覆盖外层页脚）
        if(file_exists($controllerFooter)){
            include ($controllerFooter);
        }else{
			if(file_exists($defaultFooter)){
				include ($defaultFooter);
			}
        }
    }
}