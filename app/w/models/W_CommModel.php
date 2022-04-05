<?php
/**
 * 功能描述：	    前端公共部分
 * @datetime	2019-09-28
 * @version   	v1.0
 * @author   	ZHL
 * @copyrigh  	2019 ZHL 版权所有
 */

class W_CommModel extends Model {

    /**
     * Notes:添加数据
     * User: ZHL
     * Date: 2019/9/22
     * @param $data
     * @return mixed
     */
    function addData($mould, $data){
        $sql['data'] = $data;
        $sql['table'] = $mould;
        $this->db[0]->add($sql);
        $return  = $this->rowCount();
        if($return){
            return $this->returnId();
        }
        return 0;
    }

    /**
     * Notes:添加数据详情
     * User: ZHL
     * Date: 2019/9/22
     * @param $data
     * @return mixed
     */
    function addDataDetail($mould, $data){
        $sql['data'] = $data;
        $sql['table'] = $mould.'_detail';
        $this->db[0]->add($sql);
        return $this->rowCount();
    }

    /**
     * Notes: 获取导航
     * User: ZhuHaili
     * Date: 2019/9/29
     */
    function getClassLists($where){
        $sql['field'] = 'id,fid,mouldcode,classname,status,nofollow,url,gourl';
        $sql['field'] = '*';
        $sql['table'] = 'classify';
        $sql['order'] = 'viewseq desc,id asc,createdate asc';
        $sql['where'] = $where;
        $db = $this->conn('mysql1');
        return $db->select($sql);
    }

    /**
     * Notes: 获取导航
     * User: ZhuHaili
     * Date: 2019/9/29
     */
    function getClassListsV2($id){
        $sql = 'select id,fid,mouldcode,classname,status,nofollow,url,gourl from zhl_classify where id=:id';
        $db = $this->conn('mysql2');
        $stmt = $db->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getClassListsV3($id){
        $db = $this->conn('mysql2');
        $sql = 'select id,fid,mouldcode,classname,status,nofollow,url,gourl from '.$this->db_fix.'classify where id=:id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Notes: 获取导航Oracle
     * User: ZhuHaili
     * Date: 2019/9/29
     */
    function getClassListsV4($date){
        $sql = "select * from kehudingdan where dingdanshij > to_date(:datestr,'yyyy-mm-dd')";
        $db = $this->conn('oracle');
        $stmt = $db->prepare($sql);
        $stmt->bindValue('datestr', $date);
        $stmt->execute();
        return $stmt->fetch();
    }

}