<?php
// +----------------------------------------------------------------------
// | Class  核心入口
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

if(substr(PHP_VERSION, 0, 3) < '5.3'){exit("PHP版本过低，必须5.3及以上");}  // PHP运行环境
date_default_timezone_set('PRC'); //ini_set('date.timezone', 'PRC');
const SYS_VERSION = '2.3.1';        // 系统版本
const SYS_WILL_FIX = '.php';        // 自定义类文件扩展名
const SYS_CLASS_EXT = '.class.php'; // 核心类文件扩展名
define('SYS_ROOT', __DIR__.'/');    // 核心文件目录（绝对路径）
defined('SYS_PATH') or define('SYS_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');    // 项目根目录(绝对路径)
$CONFIG_VAL = require SYS_ROOT . 'helper/config.php';          // 框架配置
foreach($CONFIG_VAL as $k=>$v){
    define($k, $v); // 循环配置常量
}
// 获取协议请求类型
//$HTTP_TYPE = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
//$HTTP_TYPE = $_SERVER['SERVER_PORT'] == '80' ? 'http://' : 'https://';
$HTTP_TYPE = '//';
$HTTP_HOST = $_SERVER['HTTP_HOST'];
// 当前项目访问主连接/当前项目根目录连接/项目域名
$WWW_URL = $HTTP_TYPE.$HTTP_HOST.dirname($_SERVER['PHP_SELF']);
$WWW_URL = strtr($WWW_URL,"\\","/");
$WWW_URL = rtrim($WWW_URL, '/'); // 去掉后边的斜杠
// 项目域名路径
define('HTTP_PATH', $WWW_URL.'/');
// 获取完整url
$WEB_URL = $HTTP_TYPE.$HTTP_HOST.$_SERVER['REQUEST_URI'];
$WEB_URL = strtr($WEB_URL,"\\","/");
$WEB_URL = rtrim($WEB_URL, '/');

// 参数串
$URL_PARAM = isset($_GET['zhlphpurl']) ? $_GET['zhlphpurl'] :  '';
// 开启伪静态时验证连接
if(SYS_URL_BOGUS){
    if(strpos($WEB_URL,SYS_APP_URL_FIX) === false){ // 没有后缀
        if($WEB_URL != $WWW_URL ){ // 判断不是主页
            $WEB_URL = $WEB_URL.SYS_APP_URL_FIX; // 当前连接拼接url后缀
            // 数据提交类型
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                /*echo '<form action="'.$WEB_URL.'" method="post">';
                foreach($_POST as $k=>$v){
                    echo '<input type="hidden" name="'.$k.'" value="'.$v.'">';
                }
                echo '</form><script type="text/javascript">document.urlfrom.submit();</script>';*/
            }else{
                header("Location: $WEB_URL");
            }
        }
    }
    // 参数串去除后缀
    if(strpos($URL_PARAM,SYS_APP_URL_FIX) !== false){ // 有后缀
        $URL_PARAM = substr($URL_PARAM, 0, -strlen(SYS_APP_URL_FIX));
    }
}

// 当前访问的url
define('SYS_WEB_URL', $WEB_URL);

/**
 * url解析分配路由
 */
$appMould = SYS_APP_DEFAULT;
$URL_PARAM_ARR = explode("/", $URL_PARAM);
$mouldArr = explode("_", $URL_PARAM_ARR[0]);
if(count($mouldArr) >= 2){
    $validM = SYS_PATH . SYS_APP_PATH . '/' . $mouldArr[0];
    if(is_dir($validM)){
        $appMould = $mouldArr[0];
        array_shift($mouldArr);
    }
}
// 定义功能模块
define('SYS_PRO_PATH', $appMould);
// 模版绝对路径常量
define('SYS_VIEWS', SYS_PATH.SYS_APP_PATH.'/'.SYS_PRO_PATH.'/views/');
// 域名时可能没有则默认index控制器
$mouldArr[0] = $mouldArr[0] ? $mouldArr[0] : 'index';
// 获取控制器(完整控制器名)
$controller = ucfirst($appMould).'_'.ucfirst($mouldArr[0]);
// 获取方法名
$action = isset($mouldArr[1]) && $mouldArr[1] ? $mouldArr[1] : 'index';
// 删除方法，剩下的为参数
array_shift($URL_PARAM_ARR);

// 获取URL参数键值混合数组
if(!empty($URL_PARAM_ARR)){
    // 为安全初始化参数
    $_GET = $_REQUEST = array();
    // 匹配参数键值对
    foreach($URL_PARAM_ARR as $k=>$v){
        if($k%2 == 0){
            $_GET[$v] = isset($URL_PARAM_ARR[$k+1]) ? $URL_PARAM_ARR[$k+1] : '';
            $_REQUEST[$v] = isset($URL_PARAM_ARR[$k+1]) ? $URL_PARAM_ARR[$k+1] : '';
        }
    }
}

// 删除默认参数
if(isset($_GET['zhlphpurl'])){
    unset($_GET['zhlphpurl']);
    unset($_REQUEST['zhlphpurl']);
}

require SYS_ROOT . 'helper/helper.php';  // 框架函数

define('SYS_START_MEMORY',  memory_get_usage());        // 系统初始内存
define('SYS_START_TIME',  microtime(true)); // 系统运行起始时间

require SYS_ROOT . 'plug/service.php';     // 自定义核心扩展服务文件

// 包含项目配置文件
if(is_file(SYS_PATH . 'config/config.php')){
    $GLOBALS['SYS'] = require SYS_PATH . 'config/config.php';;
}

// 包含核心框架类
if(!is_file(SYS_ROOT.'core/Core.php')){
    exit('Error: Warning！Base!');
}
require SYS_ROOT.'core/Core.php';
// 实例化核心类
$core = new Core;
$core->run($controller, $action);