<?php
/**
 * 功能描述：	会员管理模块
 * @datetime	2020-5-4
 * @version   	v1.0
 * @author   	ZHL
 * @copyrigh  	2019 ZHL 版权所有
 */

class W_MemberModel extends Model {

    /**
     * Notes: 修改信息
     * User: ZHL
     * Date: 2020/5/4
     */
    function updateInfo($data, $where){
        $sql['table'] = 'member';
        $sql['data'] = $data;
        $sql['where'] = $where;
        return $this->update($sql);
    }

    /**
     * Notes: 根据用户昵称获取用户信息
     * User: ZHL
     * Date: 2020/5/5
     */
    function getUserInfo($nickName){
        $sql['field'] = 'id,mcode,nickname,headimg,wechat,qq,sina,mobile,email,types,status,delflag,createdate,msex,mdescx';
        $sql['table'] = 'member';
        $sql['where']['nickname'] = $nickName;
        return $this->find($sql);
    }
}