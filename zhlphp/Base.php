<?php
// +----------------------------------------------------------------------
// | Class  核心入口
// +----------------------------------------------------------------------
// | Copyright (c) 2018
// +----------------------------------------------------------------------

if(substr(PHP_VERSION, 0, 3) < '5.3'){exit("PHP版本过低，必须5.3及以上");}   // PHP运行环境
const SYS_VERSION = '2.1';          // 系统版本
const SYS_WILL_FIX = '.php';        // 自定义类文件扩展名
const SYS_CLASS_EXT = '.class.php'; // 核心类文件扩展名
define('SYS_ROOT', __DIR__.'/');    // 核心文件目录（绝对路径）
defined('SYS_PATH') or define('SYS_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');    // 项目根目录(绝对路径)
defined('SYS_APP_PATH') or define('SYS_APP_PATH', 'app');           // 应用路径
defined('SYS_APP_DEFAULT') or define('SYS_APP_DEFAULT', 'index');   // 指向默认模块
defined('SYS_DEBUG') or define('SYS_DEBUG', false);                 // 调试开关
defined('SYS_PAGE404') or define('SYS_PAGE404', false);             // 404页面开关
defined('SYS_URL_BOGUS') or define('SYS_URL_BOGUS', false);         // 伪静态开关
defined('SYS_APP_URLFIX') or define('SYS_APP_URLFIX', '.html');     // 伪静态后缀初始值
if(SYS_URL_BOGUS){
    // 伪静态后缀重定义（SYS_APP_URL_FIX最后有效url后缀）
    defined('SYS_APP_URL_FIX') or define('SYS_APP_URL_FIX', SYS_APP_URLFIX);
}else{
    // 伪静态后缀重定义（SYS_APP_URL_FIX最后有效url后缀）
    defined('SYS_APP_URL_FIX') or define('SYS_APP_URL_FIX', '');
}

// 获取协议请求类型
$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
// 当前项目访问主连接/当前项目根目录连接/项目域名
$www_url = $http_type.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$www_url = str_replace("\\", '/', $www_url); // 反斜杠替换成斜杠
$www_url = rtrim($www_url, '/'); // 去掉后边的斜杠
// 项目域名路径
define('HTTP_PATH', $www_url.'/');
// 获取完整url
$web_url = $http_type.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$web_url = str_replace("\\", '/', $web_url);
$web_url = rtrim($web_url, '/');
// 参数串
$url_param = isset($_GET['zhlphpurl']) ? $_GET['zhlphpurl'] :  '';
// 开启伪静态时验证连接
if(SYS_URL_BOGUS){
    if(strstr($web_url,SYS_APP_URL_FIX) == null){ // 当前连接后缀不合法时
        if($web_url != $www_url ){ // 判断不是主页
            $web_url = $web_url.SYS_APP_URL_FIX; // 当前连接拼接url后缀
            // 数据提交类型
            if(!isset($_POST) || empty($_POST)){
                header("Location: $web_url");
            }else{
                echo '<form name="urlfrom" action="'.$web_url.'" method="post">';
                foreach($_POST as $k=>$v){
                    echo '<input type="hidden" name="'.$k.'" value="'.$v.'">';
                }
                echo '</form><script type="text/javascript">document.urlfrom.submit();</script>';
            }
        }
    }
    // 参数串去除后缀
    if(strpos($url_param,SYS_APP_URL_FIX) !== false){
        $url_param = substr($url_param, 0, -strlen(SYS_APP_URL_FIX));
    }
}

// 当前访问的url
defined('SYS_WEB_URL') or define('SYS_WEB_URL', $web_url);

/**
 * url解析分配路由
 */
$urlParam = explode("/",$url_param);
$mouldArr = explode("_",$urlParam[0]);
$appMould = SYS_APP_DEFAULT;
if(count($mouldArr) >= 2){
    $validM = SYS_PATH.SYS_APP_PATH.'/'.$mouldArr[0];
    if(is_dir($validM)){
        $appMould = $mouldArr[0];
        array_shift($mouldArr);
    }
}
// 定义功能模块
defined('SYS_PRO_PATH') or define('SYS_PRO_PATH', $appMould);
// 获取控制器
$controller = ucfirst($appMould).'_'.ucfirst($mouldArr[0]);
// 删除数组中的控制器名
array_shift($mouldArr);
// 获取方法名
$action = isset($mouldArr[0]) && $mouldArr[0] ? $mouldArr[0] : 'index';
// 删除方法，剩下的为参数
array_shift($urlParam);

// 获取URL参数键值混合数组
if(!empty($urlParam)){
    // 匹配参数键值对
    foreach($urlParam as $k=>$v){
        if($k%2 == 0){
            $_GET[$v] = isset($urlParam[$k+1]) ? $urlParam[$k+1] : '';
            $_REQUEST[$v] = isset($urlParam[$k+1]) ? $urlParam[$k+1] : '';
        }
    }
}
// 删除默认参数
if(isset($_GET['phpzhlurl'])){
    unset($_GET['phpzhlurl']);
    unset($_REQUEST['phpzhlurl']);
}

require SYS_ROOT.'helper.php';  // 框架函数
require SYS_ROOT.'log.php';     // 日志文件
require SYS_ROOT.'annex.php';  // 核心入口-组件扩展

define('SYS_START_MEMORY',  memory_get_usage());        // 系统初始内存
define('SYS_START_TIME',  microtime(true)); // 系统运行起始时间

// 包含项目配置文件
if(file_exists(SYS_PATH . 'config/config.php')){
    $SYS_VAL = require SYS_PATH . 'config/config.php';
    $GLOBALS['SYS'] = $SYS_VAL;
}

// 包含数据库配置文件
if(file_exists(SYS_PATH.'config/database.php')){
    $db_Con = require SYS_PATH.'config/database.php';
    $SYS_DB_HOST = isset($db_Con['hostname']) && $db_Con['hostname'] ? $db_Con['hostname'] : '';
    $SYS_DB_PORT = isset($db_Con['hostport']) && $db_Con['hostport'] ? $db_Con['hostport'] : '';
    $SYS_DB_NAME = isset($db_Con['database']) && $db_Con['database'] ? $db_Con['database'] : '';
    $SYS_DB_USER = isset($db_Con['username']) && $db_Con['username'] ? $db_Con['username'] : '';
    $SYS_DB_PASSWORD = isset($db_Con['password']) && $db_Con['password'] ? $db_Con['password'] : '';
    $SYS_DB_PREFIX = isset($db_Con['prefix']) && $db_Con['prefix'] ? $db_Con['prefix'] : '';
    define('SYS_DB_HOST', $SYS_DB_HOST);
    define('SYS_DB_PORT', $SYS_DB_PORT);
    define('SYS_DB_NAME', $SYS_DB_NAME);
    define('SYS_DB_USER', $SYS_DB_USER);
    define('SYS_DB_PASSWORD', $SYS_DB_PASSWORD);
    define('SYS_DB_PREFIX', $SYS_DB_PREFIX);
}

// 包含核心框架类
if(file_exists(SYS_ROOT.'core/Core.php')){
    require SYS_ROOT.'core/Core.php';
    // 实例化核心类
    $core = new Core;
    $core->run($controller, $action);
}else{
    exit('Error: Warning！Base!');
}