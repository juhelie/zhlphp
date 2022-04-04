<?php

// +----------------------------------------------------------------------
// | Class  错误捕获类
// +----------------------------------------------------------------------
// | Copyright (c) 2022
// +----------------------------------------------------------------------

defined('SYS_PATH') or define('SYS_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');    // 项目根目录(绝对路径)
defined('SYS_DEBUG') or define('SYS_DEBUG', true);                      // 调试开关
defined('SYS_DEBUG_LOG') or define('SYS_DEBUG_LOG', true);              // 系统日志开关
defined('SYS_PAGE404') or define('SYS_PAGE404', false);                 // 404开关
defined('SYS_ERR_PATH') or define('SYS_ERR_PATH', 'runtime/error/');   // 日志打印文件路径

class BaseException extends Exception{

    function __construct(){
        error_reporting(E_ALL); //打开全部错误监视
        ini_set('display_errors','Off');  //把错误输出到页面
        ini_set('log_errors', 'On');     //设置错误信息输出到文件
        ini_set("error_log", SYS_PATH.'runtime/error/error.log'); //指定错误日志文件名，只要路径正确即可

        //error_reporting(0); //禁止错误输出
        // 设置一个用户定义的错误处理函数
        set_error_handler(array($this, '_error_handler'));
        //定义PHP程序执行完成后执行的函数
        register_shutdown_function(array($this, '_fatal_error_handler'));
        //自定义异常处理，注册异常处理方法来捕获异常
        set_exception_handler(array($this, '_exception_handler'));
    }

    /**
     * 自定义报错函数
     */
    public function _error_handler($errno, $errstr, $errfile, $errline){
        $errorType = array(
            '0' => 'E_ERROR(致命错误)',
            '1' => 'E_ERROR(致命错误)',
            '2' => 'E_WARNING(警告性错误)',
            '4' => 'E_PARSE(编译错误)',
            '8' => 'E_NOTICE(提示性错误)',
            '16' => 'E_CORE_ERROR(初始化致命错误)',
            '32' => 'E_CORE_WARNING(初始化过程警告)',
            '64' => 'E_COMPILE_ERROR(编译时致命错误)',
            '128' => 'E_COMPILE_WARNING(编译时警告)',
            '256' => 'E_USER_ERROR(自定义错误)',
            '512' => 'E_USER_WARNING(自定义警告)',
            '1024' => 'E_USER_NOTICE(自定义提示)',
            '2048' => 'E_STRICT(编译标准化警告)',
            '4096' => 'E_RECOVERABLE_ERROR(开捕致命错误)',
            '8191' => 'E_ALL(所有错误和警告)',
            '16384' => 'E_USER_DEPRECATED',
            '30719' => 'E_ALL(所有错误和警告)',
        );
        $errType = isset($errorType[$errno]) ? $errorType[$errno] : $errno;
        $errorStr = '[' . date('Y-m-d H:i:s') . '][SYS] ' . $errType . '：' . $errstr . ' in ' . $errfile . ' on line ' . $errline . PHP_EOL;
        //写入错误日志
        //格式 ：  时间 uri | 错误消息 文件位置 第几行
        if(SYS_DEBUG_LOG){
            error_log($errorStr, 3, SYS_PATH.SYS_ERR_PATH . 'SYS'. date('Y_m_d') . '.log', 'extra');
        }
        if(SYS_PAGE404){
            header("Location: ".HTTP_PATH."404.html");
        }else if(SYS_DEBUG){
            echo "$errorStr<br>";
        }
    }

    //捕获fatalError
    public function _fatal_error_handler(){
        $e = error_get_last();
        switch ($e['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $this->_error_handler($e['type'], $e['message'], $e['file'], $e['line']);
                break;
        }
    }

    //自定义异常处理，注册异常处理方法来捕获异常
    public function _exception_handler(Throwable $e){
        $errorStr = '';
        /*if($e instanceof Error){
            $errorStr = 'catch Error(捕捉错误)：';
        }else{
            $errorStr = 'catch Exception(捕捉异常)：';
        }*/
        $this->_error_handler($e->getCode(), $errorStr . $e->getMessage(), $e->getFile(), $e->getLine());
    }
}