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
        $this->add($sql);
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
        $this->add($sql);
        return $this->rowCount();
    }

    /**
     * Notes: 获取导航
     * User: ZhuHaili
     * Date: 2019/9/29
     */
    function getClassLists($where){
        //$sql['field'] = 'id,fid,mouldcode,classname,status,nofollow,url,gourl';
        $sql['field'] = '*';
        $sql['table'] = 'classify';
        $sql['order'] = 'viewseq desc,id asc,createdate asc';
        $sql['where'] = $where;
        return $this->select($sql);
    }

    /**
     * Notes: 栏目详情
     * User: ZHL
     * Date: 2019/9/16
     * @param $id
     * @return array|bool
     */
    function getClassInfos($id){
        $params['table'] = 'classify';
        $params['field'] = '*';
        $params['where']['id'] = $id;
        return $this->find($params);
    }

    /**
     * Notes: 栏目简单信息
     * User: ZHL
     * Date: 2019/11/05
     * @param $id
     * @return array|bool
     */
    function getClassInfoNo($id){
        $params['field'] = 'url,mouldcode,pagelist';
        $params['table'] = 'classify';
        $params['where']['id'] = $id;
        return $this->find($params);
    }

    /**
     * Notes:广告列表
     * User: ZHL
     * Date: 2019/9/29
     */
    function getAdvertLists($where, $limit){
        $sql['field'] = '*';
        $sql['table'] = 'advert_detail';
        $sql['where'] = $where;
        $sql['order'] = 'viewseq desc,id desc,createdate desc';
        if($limit){
            $sql['limit'] = $limit;
        }
        return $this->select($sql);
    }

    /**
     * Notes:单条广告详情
     * User: ZHL
     * Date: 2019/9/29
     */
    function getAdvertInfos($where){
        $sql['field'] = '*';
        $sql['table'] = 'advert_detail';
        $sql['where'] = $where;
        $sql['order'] = 'viewseq desc,id desc,createdate desc';
        return $this->find($sql);
    }

    /**
     * Notes:散文详情
     * User: ZHL
     * Date: 2019/9/29
     * @param $id
     * @return array|bool
     */
    function getEssayInfos($mould, $where){
        $sql['field'] = '*';
        $sql['table'] = $mould;
        $sql['as'] = 'a';
        $sql['leftjoin'][$mould.'_detail'] = 'b on a.id=b.aid';
        $sql['where'] = $where;
        $sql['order'] = 'a.viewseq desc,a.id desc,a.createdate desc';
        return $this->find($sql);
    }

    /**
     * Notes: 获取上一条记录
     * User: ZhuHaili
     * Date: 2019/9/30
     */
    function prevInfo($mould,$classid,$id){
        $sql['where']= "status='2' and id < $id";
        if($classid){
            $sql['where']= "and status='2' and classid='$classid' and id < $id";
        }
        $sql['field'] = 'id,title,createdate';
        $sql['table'] = $mould;
        $sql['order'] = 'id desc';
        return $this->find($sql);
    }

    /**
     * Notes: 获取下一条记录
     * User: ZhuHaili
     * Date: 2019/9/30
     */
    function nextInfo($mould,$classid,$id){
        $sql['where']= "status='2' and id > $id";
        if($classid){
            $sql['where']= "and status='2' and classid='$classid' and id > $id";
        }
        $sql['field'] = 'id,title,createdate';
        $sql['table'] = $mould;
        $sql['order'] = 'id asc';
        return $this->find($sql);
    }

    /**
     * Notes:散文列表
     * User: ZHL
     * Date: 2019/9/28
     * Desc: $limit格式整数或者一维数组，$taids格式为指定键值对的一维数组 如array('indexflag'=>'1','hotflag'=>'1')
     */
    function getEssayLists($mould, $where, $locate=array(), $limit=array(), $order='', $body=false, $gt=array(), $lt=array(), $gts=array(), $lts=array()){
        $sql['field'] = 'a.*,c.url,c.mouldcode,c.infohtml';
        $sql['table'] = $mould;
        $sql['as'] = 'a';
        if($body){
            $sql['field'] = 'a.*,b.*,c.url,c.mouldcode,c.infohtml';
            $sql['leftjoin'][$mould.'_detail'] = ' b on a.id=b.aid';
        }
        $sql['leftjoin']['classify'] = ' c on a.classid=c.id';
        $sql['where'] = $where;
        if(!empty($locate)){
            $sql['locate'] = $locate;
        }
        if($order){
            $sql['order'] = $order;
        }
        if($limit){
            $sql['limit'] = $limit;
        }
        if($gt){
            $sql['gt'] = $gt;
        }
        if($lt){
            $sql['lt'] = $lt;
        }
        if($gts){
            $sql['gts'] = $gts;
        }
        if($lts){
            $sql['lts'] = $lts;
        }
        $result = $this->select($sql);
        return $result;
    }

    /**
     * Notes:散文列表-多栏目展示
     * User: ZHL
     * Date: 2019/11/05
     * Desc: $limit格式整数或者一维数组，$taids格式为指定键值对的一维数组 如array('indexflag'=>'1','hotflag'=>'1')
     */
    function getEssayListsMore($mould, $where, $locate=array(), $limit=array(), $order='', $body=false){
        $sql['field'] = 'a.*,c.url,c.mouldcode,c.infohtml';
        $sql['table'] = $mould;
        $sql['as'] = 'a';
        if($body){
            $sql['field'] = 'a.*,b.*,c.url,c.mouldcode,c.infohtml';
            $sql['leftjoin'][$mould.'_detail'] = ' b on a.id=b.aid';
        }
        $sql['leftjoin']['classify'] = ' c on a.classid=c.id';
        $sql['leftjoin']['essay_class'] = ' ac on a.id=ac.aid';
        $sql['where'] = $where;
        if(!empty($locate)){
            $sql['locate'] = $locate;
        }
        if($order){
            $sql['order'] = $order;
        }
        if($limit){
            $sql['limit'] = $limit;
        }
        $result = $this->select($sql);
        return $result;
    }

    /**
     * Notes:列表总数
     * User: ZHL
     * Date: 2019/9/30
     * @return array|bool
     */
    function essayListCount($mould, $where, $locate, $gt, $lt, $gts, $lts){
        if($locate){
            $sql['locate'] = $locate;
        }
        if($gt){
            $sql['gt'] = $gt;
        }
        if($lt){
            $sql['lt'] = $lt;
        }
        if($gts){
            $sql['gts'] = $gts;
        }
        if($lts){
            $sql['lts'] = $lts;
        }
        $sql['where'] = $where;
        $sql['field'] = 'count(1) counts';
        $sql['table'] = $mould;
        $sql['as'] = 'a';
        $result = $this->find($sql);
        return isset($result['counts']) ? $result['counts'] : 0;
    }

    /**
     * Notes:列表总数
     * User: ZHL
     * Date: 2019/11/05
     * @return array|bool
     */
    function essayListCountMore($mould, $where, $locate){
        if($locate){
            $sql['locate'] = $locate;
        }
        $sql['where'] = $where;
        $sql['field'] = 'count(1) counts';
        $sql['table'] = $mould;
        $sql['as'] = 'a';
        $sql['leftjoin']['essay_class'] = ' ac on a.id=ac.aid';
        $result = $this->find($sql);
        return isset($result['counts']) ? $result['counts'] : 0;
    }

    /**
     * Notes:散文详情
     * User: ZHL
     * Date: 2019/9/29
     * @param $id
     * @return array|bool
     */
    function essayInfoNo($mould, $where){
        $sql['field'] = 'id,visit,laud,oppose';
        $sql['table'] = $mould;
        $sql['where'] = $where;
        return $this->find($sql);
    }

    /**
     * Notes: 获取公共位信息
     * User: ZhuHaili
     * Date: 2020/3/17
     * @return array
     */
    function unitTxt($id){
        $sql['field'] = '*';
        $sql['table'] = 'webunit';
        $sql['as'] = 'a';
        $sql['leftjoin']['webunit_detail'] = ' b on a.id=b.aid';
        $sql['order'] = 'a.id desc';
        $sql['where']['a.id'] = $id;
        return $this->find($sql);
    }
}