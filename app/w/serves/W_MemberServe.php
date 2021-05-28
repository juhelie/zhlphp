<?php
/**
 * 功能描述：	会员模块
 * @datetime	2020-05-04
 * @version   	v1.0
 * @author   	ZHL
 * @copyrigh  	2019 ZHL 版权所有
 */

include SYS_PATH.'/extend/zhlUpload.php';
class W_MemberServe extends Model {

    /**
     * Notes: 根据用户昵称获取用户信息
     * User: ZHL
     * Date: 2020/5/5
     */
    function getUserInfo($nickName){
        if(!$nickName){
            return array();
        }
        $memberM = new W_MemberModel();
        return $memberM->getUserInfo($nickName);
    }

    /**
     * Notes:用户信息修改
     * User: ZHL
     * Date: 2020/5/5
     */
    function updateInfo($param){
        if(empty($param)){
            return array('code'=>40000,'msg'=>'参数不能为空');
        }
        if(!isset($param['flag']) || $param['flag'] != 'Y') {
            return array('code' => 40001, 'msg' => '非法提交');
        }
        if(!isset($param['still']) || $param['still'] != 1) {
            return array('code' => 40002, 'msg' => '非法提交');
        }
        if(!isset($param['mcode']) || !$param['mcode']){
            return array('code'=>40003,'msg'=>'网络错误！刷新重试');
        }
        if(!isset($param['uname']) || !$param['uname']) {
            return array('code' => 40004, 'msg' => '昵称不能为空');
        }

        $updateFlag = cookiesCount('member_update_flag',3);
        if(!$updateFlag) {
            return array('code' => 40005, 'msg' => '连续修改次数已经超过三次');
        }
        $info = $this->getUserInfo($param['uname']);
        if(!empty($info)){
            if($info['nickname'] == $param['uname'] && $info['mcode'] != $param['mcode']){
                return array('code' => 30000, 'msg' => '当前昵称已存在');
            }
        }
        $where['mcode'] = $param['mcode'];
        $data['nickname'] = $param['uname'];
        $data['msex'] = intval($param['usex']) ? intval($param['usex']) : 1;
        $data['mdescx'] = $param['udesc'];
        $memberM = new W_MemberModel();
        $request = $memberM->updateInfo($data, $where);
        if(!$request){
            return array('code' => 30001, 'msg' => '修改失败');
        }
        $find = getSessions('memberInfo');
        $find['nickname'] = $param['uname'];
        $find['mdescx'] = $param['udesc'];
        setSessions('memberInfo', $find);
        return array('code'=>10000,'msg'=>'修改成功');
    }

    /**
     * Notes:修改用户头像
     * User: ZHL
     * Date: 2020/5/5
     */
    function updateHeadImg($param){
        if(empty($param)){
            return array('code'=>40000,'msg'=>'参数不能为空');
        }
        if(!isset($param['flag']) || $param['flag'] != 'Y') {
            return array('code' => 40001, 'msg' => '非法提交');
        }
        if(!isset($param['still']) || $param['still'] != 1) {
            return array('code' => 40002, 'msg' => '非法提交');
        }
        if(!isset($param['mcode']) || !$param['mcode']){
            return array('code'=>40003,'msg'=>'网络错误！刷新重试');
        }
        if(!isset($param['headeimg']) || !$param['headeimg']) {
            return array('code' => 40004, 'msg' => '头像不能为空');
        }
        $updateFlag = cookiesCount('member_update_img',3);
        if(!$updateFlag) {
            return array('code' => 40005, 'msg' => '连续修改次数已经超过三次');
        }
        $size = isset($GLOBALS['SYS']['update_pic_size']) ? $GLOBALS['SYS']['update_pic_size']*1024 : 1024*1024*2;
        $type = isset($GLOBALS['SYS']['update_pic_type']) ? $GLOBALS['SYS']['update_pic_type'] : 'pjpeg|jpeg|jpg|gif|bmp|png';
        $arr = array(
            'fileDate' => $param['headeimg'],
            'fileType' => $type,
            'fileSize' => $size,
            'filePath' => './uploads/member/'.date("Ym").'/'
        );
        $up = new zhlupload();
        $res = $up->uploads($arr);
        if($res['code'] != 10000){
            return array('code'=>30000,'msg'=>'头像上传失败');
        }
        $where['mcode'] = $param['mcode'];
        $login = new W_LoginModel();
        $find = $login->getUserInfo($where);
        $upSer = new A_SysServe();
        $upSer->updataFileLogStatus($find['headimg'],3);
        $upSer->addFileLog($res['data']['file']);
        $data['headimg'] = $res['data']['file'];
        $memberM = new W_MemberModel();
        $request = $memberM->updateInfo($data, $where);
        if(!$request){
            return array('code' => 30001, 'msg' => '修改失败');
        }
        $find = getSessions('memberInfo');
        $find['headimg'] = $data['headimg'];
        setSessions('memberInfo', $find);
        return array('code'=>10000,'msg'=>'修改成功','path'=>$data['headimg']);
    }

    /**
     * Notes: 修改密码
     * User: ZHL
     * Date: 2020/5/5
     */
    function updatePWD($param){
        if(empty($param)){
            return array('code'=>40000,'msg'=>'参数不能为空');
        }
        if(!isset($param['flag']) || $param['flag'] != 'Y') {
            return array('code' => 40001, 'msg' => '非法提交');
        }
        if(!isset($param['still']) || $param['still'] != 1) {
            return array('code' => 40002, 'msg' => '非法提交');
        }
        if(!isset($param['mcode']) || !$param['mcode']){
            return array('code'=>40003,'msg'=>'网络错误！刷新重试');
        }
        if(!isset($param['pwd']) || !$param['pwd']) {
            return array('code' => 40004, 'msg' => '密码不能为空');
        }
        if(!isset($param['pwddo']) || !$param['pwddo']) {
            return array('code' => 40005, 'msg' => '确认密码不能为空');
        }
        if($param['pwd'] != $param['pwddo']){
            return array('code' => 40006, 'msg' => '两次输入密码不一致');
        }
        $updateFlag = cookiesCount('member_update_pwd',3);
        if(!$updateFlag) {
            return array('code' => 40007, 'msg' => '连续修改次数已经超过三次');
        }
        $where['mcode'] = $param['mcode'];
        $pwd = md5(md5(md5($param['pwd'])));
        $data['pwd'] = strtoupper(substr($pwd,0,strlen($pwd)-2));
        $memberM = new W_MemberModel();
        $request = $memberM->updateInfo($data, $where);
        if(!$request){
            return array('code' => 30000, 'msg' => '修改失败');
        }
        $loginSer = new W_LoginServe();
        $loginSer->userOut();
        return array('code'=>10000,'msg'=>'修改成功,重新登录');
    }
}