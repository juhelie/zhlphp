<?php
/**
 * 功能描述：	用户登录注册模块
 * @datetime	2018-11-05
 * @version   	v1.0
 * @author   	ZHL
 * @copyrigh  	2019 ZHL 版权所有
 */

class W_LoginServe extends Model {

    /**
     * Notes: 用户登录操作
     * User: ZhuHaili
     * Date: 2019/9/12
     * @return string
     */
	function goUserLogin($userName, $userPwd, $verifyCode){
		if(!$userName){
            return array('code'=>40001,'msg'=>'帐号不能为空');
        }
		if(!$userPwd){
            return array('code'=>40002,'msg'=>'密码不能为空');
        }
		if(!$verifyCode){
            return array('code'=>40003,'msg'=>'验证码错误');
        }
		$verifyCodeVal = getSessions('login_name');
		if($verifyCodeVal != $verifyCode){
            return array('code'=>40004,'msg'=>'非法登录');
        }
        $pwd = md5(md5(md5($userPwd)));
        $pwd = strtoupper(substr($pwd,0,strlen($pwd)-2));
        $login = new W_LoginModel();
        // 昵称、手机、邮箱即可作为登录账号
        $find = $login->getUserInfoOther($userName, $pwd);
        if(empty($find)){
            return array('code'=>30000,'msg'=>'账号或密码错误');
        }
        if($find['status'] == 1){
            return array('code'=>30001,'msg'=>'账号未激活');
        }
        if($find['status'] == 3){
            return array('code'=>30002,'msg'=>'账号异常已冻结');
        }
        if($find['status'] == 4){
            return array('code'=>30003,'msg'=>'账号已注销');
        }
        $find['website_member'] = zhlEncrypt::encode(HTTP_PATH);
        setSessions('memberInfo', $find);
        return array('code'=>10000,'msg'=>'登录成功');
	}

    /**
     * Notes: 快捷登录
     * User: ZHL
     * Date: 2020/5/2
     */
	function memberReg($userName, $userCode, $typeKey, $type){
        $result = $this->verify($userName, $userCode, $typeKey);
        if($result['code'] != 10000){
            return $result;
        }
        // 昵称、手机、邮箱即可作为登录账号
        $login = new W_LoginModel();
        if($type == 'moblie'){
            $where['moblie'] = $userName;
        }else if($type == 'email'){
            $where['email'] = $userName;
        }
        $find = $login->getUserInfo($where);
        // 直接登录
        if(!empty($find)){
            if($find['status'] == 1){
                return array('code'=>30001,'msg'=>'账号未激活');
            }
            if($find['status'] == 3){
                return array('code'=>30002,'msg'=>'账号异常已冻结');
            }
            if($find['status'] == 4){
                return array('code'=>30003,'msg'=>'账号已注销');
            }
            $find['website_member'] = zhlEncrypt::encode(HTTP_PATH);
            setSessions('memberInfo', $find);
            return array('code'=>10000,'msg'=>'登录成功');
        }
        // 添加新帐号
        $data['mcode'] = time13();
        $data['nickname'] = $userName;
        $pwdde = baseData::$memberPWDde;
        $pwd = md5(md5(md5($pwdde)));
        $data['pwd'] = strtoupper(substr($pwd,0,strlen($pwd)-2));
        if($type == 'moblie'){
            $data['mobile'] = $userName;
        }else if($type == 'email'){
            $data['email'] = $userName;
        }
        $data['headimg'] = './public/web/img/common/headimg.png';
        $data['types'] = 3;
        $data['viewseq'] = 0;
        $data['status'] = 2;
        $data['createdate'] = date('Y-m-d H:i:s');
        $memberM = new A_MemberModel();
        $result = $memberM->addMember($data);
        if(!$result){
            return array('code'=>30000,'msg'=>'登录失败');
        }
        $data['website_member'] = zhlEncrypt::encode(HTTP_PATH);
        unset($data['pwd']);
        setSessions('memberInfo', $data);
        return array('code'=>10000,'msg'=>'登录成功');
    }

    /**
     * Notes: 验证码验证
     * User: ZHL
     * Date: 2020/5/2
     */
    private function verify($account, $code, $typekey){
        $result = array('code'=>40000,'msg'=>'验证码类型错误');
        $mustNameArr = array('member_reg_code');
        if(!in_array($typekey, $mustNameArr)){
            return $result;
        }
        if(!$account){
            return array('code'=>40001,'msg'=>'账号不能为空');
        }
        $verifyInfo = getSessions($typekey);
        if(!$verifyInfo){
            return array('code'=>30000,'msg'=>'验证码已失效');
        }
        if(time()-$verifyInfo['time'] > 300){
            return array('code'=>30001,'msg'=>'验证码已过期');
        }
        if($verifyInfo['account'] != $account){
            return array('code'=>30002,'msg'=>'账号错误');
        }
        if($verifyInfo['code'] != $code){
            return array('code'=>30003,'msg'=>'验证码错误');
        }
        return array('code'=>10000,'msg'=>'验证通过');
    }

	/**
	 * @fun		退出登录
	 * @date	2018-11-05
	 */
    function userOut(){
        $_SESSION = array(); // 删除所有 Session 变量
        //判断 cookie 中是否保存 Session ID
        if(isset($_COOKIE[session_name()])){
            // cookie清理
            setcookie(session_name(),'',time()-3600, '/');
        }
        @session_destroy(); //彻底销毁 Session
        //session_unset("memberInfo");
        return true;
    }

    /**
     * Notes: 判断是否登录
     * User: ZhuHaili
     * Date: 2019/11/12
     */
    function judgeSessionLogin($type=''){
        $memberInfo = getSessions('memberInfo');
        $memberInfoId = isset($memberInfo['mcode']) ? $memberInfo['mcode'] : '';
        $memberInfoWeb = isset($memberInfo['website_member']) ? $memberInfo['website_member'] : '';
        $webHttpUrl = zhlEncrypt::encode(HTTP_PATH);
        if(!$memberInfoId || ($memberInfoWeb != $webHttpUrl)){
            if($type == 1){
                redirect('login.html');
            }else{
                $url = HTTP_PATH.'login.html';
                comm_alert('超时！请重新登录',$url,3,1);
            }
        }
    }

}