<?php

// +----------------------------------------------------------------------
// | Class  核心类
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

class Core {

    public $BaseExceptionClass;

	/**
	 * @fun  运行程序
	 */
    function run($controller, $action){
        spl_autoload_register(array($this, 'loadClass')); // 自动加载类
        $this->setErrorDomain();
        $this->setParamVerifiy();
        $this->setParamGlobals();
        $this->runHook($controller, $action);
    }
 
	/**
	 * @fun   检测开发环境
	 */
    private function setErrorDomain(){
        $this->BaseExceptionClass = new BaseException();
        /*if(SYS_DEBUG == true){
            error_reporting(E_ALL); //打开全部错误监视
            ini_set('display_errors','On');  //把错误输出到页面
            ini_set('log_errors', 'On');     //设置错误信息输出到文件
            ini_set("error_log", SYS_PATH.'runtime/error.log'); //指定错误日志文件名，只要路径正确即可
        }else{
            error_reporting(0);
            ini_set('display_errors','Off');
        }*/
    }

	/**
	 * @fun   检测敏感字符
	 * @desc  
	 */
    private function setParamVerifiy(){
        if(get_magic_quotes_gpc()){
            $_GET = $this->stripSlashesDeep($_GET );
            $_POST = $this->stripSlashesDeep($_POST );
            $_COOKIE = $this->stripSlashesDeep($_COOKIE);
            $_SESSION = $this->stripSlashesDeep($_SESSION);
        }
    }
 
	/**
	 * @fun   删除敏感字符
	 * @desc  
	 */
    private function stripSlashesDeep($value){
        $value = is_array($value) ? array_map(array(__CLASS__, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }
	
	/**
	 * @fun   检测自定义全局变量
	 * @desc  register , globals并移除
	 */
    private function setParamGlobals(){
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
           foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }
 
	/**
	 * @fun   自动加载类
	 * @desc  核心，扩展，第三方扩展，控制器，服务类，模型类 
	 */
    static function loadClass($class){
        $load['core'] = SYS_ROOT .'core/'. $class . SYS_CLASS_EXT;      // 核心库
        $load['lib'] = SYS_ROOT .'library/'. $class . SYS_CLASS_EXT;    // 程序库
        $load['extend'] = SYS_PATH .'extend/'. $class . SYS_WILL_FIX;   // 第三方扩展库
        $load['controllers'] = SYS_PATH . SYS_APP_PATH.'/'.SYS_PRO_PATH.'/controllers/'.$class.SYS_WILL_FIX;
        $load['serves'] = SYS_PATH . SYS_APP_PATH.'/'.SYS_PRO_PATH.'/serves/'.$class.SYS_WILL_FIX;
        $load['models'] = SYS_PATH . SYS_APP_PATH.'/'.SYS_PRO_PATH.'/models/'.$class.SYS_WILL_FIX;
        $existFlag = false;
        foreach($load as $v){
            if(file_exists($v)){
                $existFlag = true;
                require $v;
                break;
            }
        }
        if(!$existFlag){
            sysloger('没有找到相应的类文件'.$v, dirname(__FILE__).'/Core.php', __LINE__);
        }
    }

    /**
     * @fun   主请求方法
     * @desc  主要目的是拆分URL请求
     */
    private function runHook($controller, $action){
        $controllers = $controller.'Controller';
        // 检查控制器和动作是否存在，
        if(method_exists($controllers, $action)){
            $int = new $controllers($controller, $action);
            $int->$action(); // 直接调取方法
        }else{
            sysloger($controller.'/'.$action.'non-existent!', dirname(__FILE__).'/Core.php', __LINE__);
        }
    }

}