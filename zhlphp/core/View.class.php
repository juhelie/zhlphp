<?php
// +----------------------------------------------------------------------
// | Class  视图基类
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

class View {

    protected $_controller;
    protected $_action;
    protected $vars;

    function __construct($controller, $action) {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->vars = array();
    }
	
	/**
	 * @fun   设置变量方法
	 * @desc  
	 */
    function set($name, $value) {
        //$this->$name = $value;
        //$this->variables[$name] = $value;
        $this->vars[$name] = $value;
    }
	
	/**
	 * @fun   显示视图
	 * @desc  
	 */
    /**
     * @fun   显示视图
     * @desc
     */
    function render($template, $flag) {
        //从数组中将变量导入到当前的符号表。
        extract($this->vars);

        // 默认模版
        $controller = strtolower($this->_controller);
        $action = strtolower($this->_action);

        // 视图指定模版时覆盖默认模版，最多两层（允许当前控制器下模版和跨控制器模版不允许映射到其他模块下的模版）
        if($template){
            $actionArr = explode('/',$template);
            $action = $actionArr[0];
            if(count($actionArr) > 1){ // 如果包含指定模型时
                $controller = $actionArr[0];
                $action = $actionArr[1];
            }
        }

        /*if($flag){
            $defaultHeader = APP_PATH.SYS_APP.'/'.PRO_PATH.'views/header.php';
            $defaultFooter = APP_PATH.SYS_APP.'/'.PRO_PATH.'views/footer.php';
            $controllerHeader = APP_PATH.SYS_APP.'/'.PRO_PATH.'views/'.$controller.'/header.php';
            $controllerFooter = APP_PATH.SYS_APP.'/'.PRO_PATH.'views/'.$controller.'/footer.php';
            // 页头文件包含（控制器存在页头覆盖外层页头）
            if(file_exists($controllerHeader)){
                include ($controllerHeader);
            }else{
                if(file_exists($defaultHeader)){
                    include ($defaultHeader);
                }
            }
        }*/

        // 页面内容文件
        $contentPagePath = SYS_VIEWS.$controller.'/'.$action.SYS_TMPL_FIX;
        if(file_exists($contentPagePath)){
            $content = $contentPagePath;
        }else{
            exit('error : 没有找到'.$contentPagePath);
        }

        if($flag){
            $commonHtml = SYS_VIEWS.SYS_TMPL_PARENT.SYS_TMPL_FIX;
            $commonHtmlIn = SYS_VIEWS . $controller . '/'.SYS_TMPL_PARENT.SYS_TMPL_FIX;
            if(file_exists($commonHtmlIn)) {
                include $commonHtmlIn;
            }else if(file_exists($commonHtml)){
                include $commonHtml;
            }
        }else{
            include $content;
        }

        // 页脚文件（控制器存在页脚覆盖外层页脚）
        /*if($flag){
            if(file_exists($controllerFooter)){
                include ($controllerFooter);
            }else{
                if(file_exists($defaultFooter)){
                    include ($defaultFooter);
                }
            }
        }*/
    }
}