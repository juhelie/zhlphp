<?php
/**
 * 功能描述：	系统用户登录
 * @datetime	2019-09-10
 * @version   	v1.0
 * @author   	ZHL
 * @copyrigh  	2019 ZHL 版权所有
 */

class W_LoginModel extends Model {

    /**
     * Notes:账号/手机/邮箱 登录 验证用户信息
     * User: ZhuHaili
     * Date: 2019/9/12
     */
    function getUserInfoOther($userName, $pwd){
        $pwd = $this->sqlStr($pwd);
        $userName = $this->sqlStr($userName);
        $sql['field'] = 'id,mcode,nickname,headimg,wechat,qq,sina,mobile,email,types,status,delflag,createdate,msex,mdescx';
        $sql['table'] = 'member';
        $sql['where'] =  "and pwd=$pwd and (nickname=$userName or mcode=$userName or mobile=$userName or email=$userName)";
        return $this->find($sql);
    }

    /**
     * Notes:获取用户信息
     * User: ZhuHaili
     * Date: 2019/9/12
     */
    function getUserInfo($where){
        $sql['field'] = 'id,mcode,nickname,headimg,wechat,qq,sina,mobile,email,types,status,delflag,createdate,msex,mdescx';
        $sql['table'] = 'member';
        $sql['where'] = $where;
        return $this->find($sql);
    }
}