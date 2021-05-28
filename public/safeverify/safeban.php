<?php

/**
 * 设置sessions
 */
function safeVerifySetSessions($name, $val){
	@session_start();
	$_SESSION[$name] = $val;
	//session_write_close();
	session_commit();
}

/**
 * 获取sessions
 */
function safeVerifyGetSessions($name){
	@session_start();
	if(isset($_SESSION[$name])){
		return $_SESSION[$name];
	}
	session_commit(); // 等价于 session_write_close
	return null;
}

/**
 * 删除sessions
 */
function safeVerifyDelSessions($name){
	@session_start();
	if(empty($_SESSION[$name])){
		return false;
	}
	unset($_SESSION[$name]);
	session_write_close();
}

/**
 * 13位时间戳
 */
function safeVerifyTime13(){
	list($s1, $s2) = explode(' ', microtime());
	return (float)sprintf('%.0f',(floatval($s1) + floatval($s2)) * 1000);
}

/**
 * 安全检测
 */
function safeVerifyExecute($allowCount = 6,$allowTime = 600,$loseTime = 300000){ // 连续6次/0.6秒内/不验证5分钟失效
	$nowTime = safeVerifyTime13();
	$token = md5('safeVerify'.md5($nowTime));
	$safeVerifyTmpDate = safeVerifyGetSessions('safe_Verify_Info');
	if(!$safeVerifyTmpDate){
		$sessionVal = array('time'=>$nowTime,'count'=>1,'token'=>$token);
		safeVerifySetSessions('safe_Verify_Info',$sessionVal);
		return array('code'=>10001,'msg'=>'初始化数据','token'=>$token);
	}else{
		$previousCount = $safeVerifyTmpDate['count'];
        $previousTime = $safeVerifyTmpDate['time'];
		if($previousCount >= $allowCount && ($nowTime-$previousTime) < $loseTime){
			$token = $safeVerifyTmpDate['token'];
			return array('code'=>10000,'msg'=>'需要验证','count'=>$previousCount+1,'token'=>$token);
		}
		$previousTime = $safeVerifyTmpDate['time'];
		
		if($nowTime - $previousTime > $allowTime){
			safeVerifyDelSessions('safe_Verify_Info');
			$count = 1;
			$sessionVal = array('time'=>$nowTime,'count'=>$count,'token'=>$token);
			safeVerifySetSessions('safe_Verify_Info',$sessionVal);
			return array('code'=>10002,'msg'=>'数据已重置','count'=>$count,'token'=>$token);
		}else{
			$newCount = $previousCount + 1;
			$sessionVal = array('time'=>$nowTime,'count'=>$newCount,'token'=>$token);
			safeVerifySetSessions('safe_Verify_Info',$sessionVal);
			return array('code'=>10003,'msg'=>'数据累加中','count'=>$newCount,'token'=>$token);
		}
	}
}

/**
 * 安全验证
 */
function safeVerifyGOTo($token){
	$sessionInfo = safeVerifyGetSessions('safe_Verify_Info');
	if(isset($sessionInfo['token']) && $sessionInfo['token'] == $token){
		$count = 1;
		$nowTime = safeVerifyTime13();
		$sessionVal = array('time'=>$nowTime,'count'=>$count);
		safeVerifySetSessions('safe_Verify_Info',$sessionVal);
		return 'Y';
	}
	return 'N';
}


// 验证
if(isset($_REQUEST['flag']) && isset($_REQUEST['token']) && $_REQUEST['flag'] == 'verifyCode'){
	$token = $_REQUEST['token'];
	$result = safeVerifyGOTo($token);
	echo json_encode($result);exit;
}

