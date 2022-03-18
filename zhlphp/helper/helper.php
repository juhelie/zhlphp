<?php
// +----------------------------------------------------------------------
// | Class  Helper 系统基础方法
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

/**
 * Notes: 客户端操作限制
 * User: ZHL
 * Date: 2020/5/5
 */
if(!function_exists('cookiesCount')) {
    function cookiesCount($kname, $kcount){
        $limitFlag = getCookies($kname);
        if (!$limitFlag) {
            $counts = base64_encode(base64_encode(1));
            setCookies($kname, $counts,86400);
        } else {
            $counts = intval(base64_decode(base64_decode($limitFlag)));
            if ($counts >= $kcount) {
                return false;
            }
            $counts = base64_encode(base64_encode($counts + 1));
            setCookies($kname, $counts,86400);
        }
        return true;
    }
}

/**
 * Notes: 字符串-加密
 * Desc: 兼容字符串特殊字符加密
 */
if(!function_exists('sys_encrypt')) {
    function sys_encrypt($string, $key)
    {
        $string = base64_encode(openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA));
        return rtrim(strtr($string, '+/', '-_'), '=');
    }
}

/**
 * Notes: 字符串-解密
 * Desc: 兼容字符串特殊字符解密
 */
if(!function_exists('sys_decrypt')) {
    function sys_decrypt($string, $key)
    {
        $string = str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT);
        return openssl_decrypt(base64_decode($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
    }
}

/**
 * 设置sessions
 */
if(!function_exists('setSessions')){
	function setSessions($name, $val){
        @session_start();
		$_SESSION[$name] = $val;
		//session_write_close();
        session_commit();
	}
}

/**
 * 获取sessions
 */
if(!function_exists('getSessions')){
	function getSessions($name){
        @session_start();
		if(isset($_SESSION[$name])){
			return $_SESSION[$name];
		}
		session_commit(); // 等价于 session_write_close
		return null;
	}
}

/**
 * 删除sessions
 */
if(!function_exists('delSessions')){
	function delSessions($name){
        @session_start();
		if(empty($_SESSION[$name])){
		    return false;
		}
		unset($_SESSION[$name]);
		session_write_close();
	}
}

/**
 * 清空所有session
 */
if(!function_exists('clearSessions')){
    function clearSessions(){
        $_SESSION = array(); // 删除所有 Session 变量
        //判断 cookie 中是否保存 Session ID
        if(isset($_COOKIE[session_name()])){
            // cookie清理
            setcookie(session_name(),'',time()-3600, '/');
        }
        @session_destroy(); //删除当前用户对应的session文件以及释放session id，内存中的$_SESSION变量内容依然保留
        return true;
    }
}

/**
 * 设置cookie
 */
if(!function_exists('setCookies')){
	function setCookies($name, $val, $expire = 604800){ // 默认7天
		$expire += time();
		@setcookie($name, $val, $expire, '/');
		$_COOKIE[$name] = $val;
	}
}

/**
 * 获取cookie
 */
if(!function_exists('getCookies')){
	function getCookies($name){
		if(isset($_COOKIE[$name])){
			return $_COOKIE[$name];
		}
		return null;
	}
}

/**
 * 删除cookie
 */
if(!function_exists('delCookies')){
	function delCookies($name){
		setcookie($name, 'null', time() - 1000, '/');
	}
}

/**
 * 运行开销统计
 */
if(!function_exists('runCosts')){
	function runCosts(){
		return array(
			'times'=>round((microtime(true) - SYS_START_TIME), 3),
			'ram'=>round((memory_get_usage() - SYS_START_MEMORY) / 1024, 2)
		);
	}
}

/**
 * 重定向
 */
if(!function_exists('redirect')){
    function redirect($url){
        if($url){
            if(stripos($url,'http://') == false && stripos($url,'https://') == false){
                $url = HTTP_PATH.$url.SYS_APP_URL_FIX;
            }
        }else{
            $url = HTTP_PATH;
        }
        header("Location: ".$url);exit;
    }
}

/**
 * js提示跳转
 */
if(!function_exists('jump')){
	function jump($txt = '提示', $url=''){
		$txt = strval($txt);
		if($url){
            $httpType = '';
            if(strpos($url,'http://') !== false){
                $httpType = 'http://';
            }else if(strpos($url,'https://') !== false){
                $httpType = 'https://';
            }
            $url = $httpType ? $url : HTTP_PATH.$url;
            echo "<script>alert('".$txt."');window.location.href='".$url."';</script>";
        }else{
            echo "<script>alert('".$txt."');window.history.go(-1);</script>";
        }
	}
}

/**
 * 路由拼接
 */
if(!function_exists('sysUrlFix')){
    function sysUrlFix($url){
        return HTTP_PATH.$url.SYS_APP_URL_FIX;
    }
}

/**
 * 系统日志
 */
if(!function_exists('sysloger')){
    function sysloger($str, $errorFile='', $errorLine='0'){
        $errorStr = '[' . date('Y-m-d H:i:s') . '][SYS] '.$str.' in '.$errorFile.' on line '.$errorLine . PHP_EOL;
        if(SYS_DEBUG_LOG){
            error_log($errorStr, 3, SYS_LOG_PATH . 'SYS' .date('Y_m_d') . '.log', 'extra');
        }
        if(SYS_PAGE404){
            header("Location: ".HTTP_PATH."404.html");
        }else if(SYS_DEBUG){
            exit($errorStr);
        }
    }
}

/**
 * loger
 */
if(!function_exists('loger_r')) {
    function loger_r($s){
        echo '<pre>';
        print_r($s);
        echo '</pre>';
        exit;
    }
}

if(!function_exists('loger_d')) {
    function loger_d($s){
        echo '<pre>';
        var_dump($s);
        echo '</pre>';
        exit;
    }
}
if(!function_exists('loger')){
    function loger($str){
        if(!SYS_DEBUG_LOG){
            return '';
        }
        $errorStr = '[' . date('Y-m-d H:i:s') . '][LOG] '.$str . PHP_EOL;
        error_log($errorStr, 3, SYS_PATH.SYS_LOG_PATH . 'LOG' .date('Y_m_d') . '.log', 'extra');
    }
}

