<?php
/**
 * 功能描述：	前端公共部分
 * @datetime	2019-09-28
 * @version   	v1.0
 * @author   	ZHL
 * @copyrigh  	2019 ZHL 版权所有
 */

include SYS_PATH.'extend/function.php';
class W_CommServe extends Model {

    // 数据库1_mysqli
    function getClassLists($id){
        $where['id'] = $id;
        $commM = new W_CommModel();
        return $commM->getClassLists($where);
    }

    // 数据库2_pdo
    function getClassListsV2($id){
        $commM = new W_CommModel();
        return $commM->getClassListsV2($id);
    }

    // 数据库3_oracle_pdo
    function getClassListsV3($date){
        $commM = new W_CommModel();
        return $commM->getClassListsV3($date);
    }
}