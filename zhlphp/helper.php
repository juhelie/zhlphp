<?php
// +----------------------------------------------------------------------
// | Class  Helper 系统基础方法
// +----------------------------------------------------------------------
// | Copyright (c) 2018
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
	function runCost(){
		return array(
		    'unit'=>'s',
			'times'=>round((microtime(true) - SYS_START_TIME) * 1000, 3),
			'ram'=>round((memory_get_usage() - SYS_START_MEMORY) / 1024, 2)
		);
	}
}

/**
 * 重定向
 */
if(!function_exists('redirect')){
	function redirect($url, $type=''){
		$url = $type ? $url : HTTP_PATH.$url;
		header("Location: ".$url);
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
 * 栏目路由
 */
if(!function_exists('setClassUrl')){
    function setClassUrl($param){
        if(!isset($param['id']) || !$param['id'] || !isset($param['url']) || !$param['url']){
            return '';
        }
        $id = intval($param['id']);
        $url = trim($param['url']);
        $gourl = isset($param['gourl']) ? trim($param['gourl']) : '';
        $htmlflag = isset($param['htmlflag']) ? intval($param['htmlflag']) : 1;
        if(!$gourl){
            $gourl = $url.SYS_APP_URL_FIX;
            if($htmlflag == 2){
                $gourl = 'item/'.$url.'/'.$id.SYS_APP_URL_FIX;
            }else if($htmlflag == 3) {
                $gourl = $url.'/item/'.$id.SYS_APP_URL_FIX;
            }
        }
        return $gourl;
    }
}

/**
 * 文章路由
 */
if(!function_exists('setEssayUrl')){
    function setEssayUrl($param){
        if(!isset($param['id']) || !$param['id']){
            return '';
        }
        $classM = isset($param['mould']) && $param['mould'] ? $param['mould'] : '';
        if(isset($param['tpl']) && $param['tpl']){
            $classM = $param['tpl'];
        }
        $classM = str_replace(array(" ","　","\t","\n","\r"),array("","","","",""),$classM);
        if(!$classM){
            return '';
        }
        return HTTP_PATH.$classM.'/'.$param["id"].SYS_APP_URL_FIX;
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