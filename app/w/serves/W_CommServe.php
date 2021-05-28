<?php
/**
 * 功能描述：	    前端公共部分
 * @datetime	2019-09-28
 * @version   	v1.0
 * @author   	ZHL
 * @copyrigh  	2019 ZHL 版权所有
 */

include SYS_PATH.'extend/function.php';
class W_CommServe extends Model {

    /**
     * Notes:提交留言
     * User: ZHL
     * Date: 2019/9/28
     */
    function addMssage($param,$mould,$classid,$dayFlag){
        $applyFlag = getCookies('applyFlag');
        if(!$mould){
            return array('code'=>'40000','msg'=>'网络错误');
        }
        if($applyFlag == 'Y' && $dayFlag == 'Y'){ // 一天只能一次
            return array('code'=>'40000','msg'=>'你已经提交过了，我会尽快处理');
        }
        $classid = set_intval($classid);
        $hint = array('code'=>'40000','msg'=>'提交失败，稍后再试！');
        if(!$classid){
            $hint['code'] = '40001';
            $hint['msg'] = '出错了，刷新重试！';
            return $hint;
        }
        // 安全防护，禁止重复多次提交
        $messageFlag = getSessions('messageFlag');
        if(!$messageFlag){
            $counts = base64_encode(base64_encode(1));
            setSessions('messageFlag', $counts);
        }else{
            $counts = base64_decode(base64_decode($messageFlag));
            if($counts > 10){
                $hint['code'] = '40002';
                $hint['msg'] = '你已经提交过多次了，明天再来！';
                return $hint;
            }
            $counts = base64_encode(base64_encode($counts+1));
            setSessions('messageFlag', $counts);
        }
        $fieldSer = new A_FieldServe();
        $fields1 = $fieldSer->getClassSortFields($classid,'2','1');
        $fields2 = $fieldSer->getClassSortFields($classid,'2','2');
        $data = $this->dataArr($fields1, $param);
        $data['title'] = isset($param['title']) ? $param['title'] : date('Y-m-d H:i:s').'留言';
        $data['status'] = 1;
        $data['viewseq'] = 0;
        $data['classid'] = $classid;
        $data['createdate'] = date('Y-m-d H:i:s');
        $commM = new W_CommModel();
        $id = $commM->addData($mould, $data);
        if(!$id){
            $hint['code'] = '40003';
            return $hint;
        }
        $dataDetail = $this->dataArr($fields2, $param);
        $dataDetail['aid'] = $id;
        $result = $commM->addDataDetail($mould, $dataDetail);
        $hint['code'] = '40004';
        if($result){
            setCookies('applyFlag','Y',86400); //24小时
            $hint = array('code'=>'10000','msg'=>'提交成功');
        }
        return $hint;
    }

    /**
     * Notes:提交数据有效字段匹配
     * User: ZHL
     * Date: 2019/9/28
     */
    private function dataArr($fieldDate,$param){
        if(empty($fieldDate) || empty($param)){
            return array();
        }
        $data = array();
        foreach($fieldDate as $k=>$v){
            $field = $v['field'];
            if(isset($param[$field])){
                if(is_array($param[$field])){
                    $fieldArr = array();
                    foreach($param[$field] as $key=>$val){
                        $fieldArr[$key] = urldecode($val);
                    }
                    $fieldStr = implode('|',$fieldArr);
                    $data[$field] = $fieldStr;
                }else {
                    $fieldVal = urldecode($param[$field]);
                    $data[$field] = $fieldVal;
                }
            }
        }
        return $data;
    }

    /**
     * Notes:获取全部导航
     * User: ZHL
     * Date: 2019/9/29
     */
    function classList($status=0){
        $status = set_intval($status);
        $commM = new W_CommModel();
        $where = array();
        $where['navtype'] = '1';
        if($status){
            $where['status'] = $status;
        }
        $list = $commM->getClassLists($where);
        return $this->navset($list);
    }

    /**
     * Notes:获取子导航列表
     * User: ZHL
     * Date: 2019/9/29
     */
    function classListFind($fid, $status=0){
        $fid = set_intval($fid);
        $status = set_intval($status);
        $commM = new W_CommModel();
        $where['fid'] = $fid;
        if($status){
            $where['status'] = $status;
        }
        $list = $commM->getClassLists($where);
        return $this->navset($list);
    }

    /**
     * Notes:获取面包屑导航
     * User: ZHL
     * Date: 2019/11/18
     */
    function menuCrumb($id,$status=2){
        $commM = new W_CommModel();
        $where['status'] = $status;
        $list = $commM->getClassLists($where);
        $arr = $this->navseturl($list);
        return menu_crumb($arr, $id);
    }

    /**
     * Notes: 导航路由设置
     * User: ZhuHaili
     * Date: 2019/11/18
     */
    private function navseturl($list){
        foreach($list as $k=>$v){
            $setUrl = setClassUrl($v);
            $list[$k]['url'] = HTTP_PATH.$setUrl;
        }
        return $list;
    }

    /**
     * Notes:_导航设置-无限导航树
     * User: ZHL
     * Date: 2019/9/29
     */
    private function navset($list){
        if(empty($list)){
            return array();
        }
        $list = $this->navseturl($list);
        return getTree($list);
    }

    /**
     * Notes:导航详情
     * User: ZHL
     * Date: 2019/9/29
     */
    function classInfo($id){
        $commM = new W_CommModel();
        $info = $commM->getClassInfos($id);
        if(!empty($info)){
            $url = trim($info['url']);
            $gourl = trim($info['gourl']);
            if(!$gourl){
                $gourl = HTTP_PATH.$url.SYS_APP_URL_FIX;
            }
            $info['url'] = $gourl;
        }
        return $info;
    }

    /**
     * Notes:广告列表
     * User: ZHL
     * Date: 2019/9/29
     * Desc: $limit格式整数或者一维数组，
     */
    function advertList($typeId, $limit=0){
        $typeId = set_intval($typeId);
        if(!$typeId){
            return array();
        }
        $where['fid'] = $typeId;
        $where['status'] = '2';
        $commM = new W_CommModel();
        return $commM->getAdvertLists($where, $limit);
    }

    /**
     * Notes: 单条广告详情
     * User: ZHL
     * Date: 2019/9/29
     */
    function advertInfo($id){
        $id = set_intval($id);
        if(!$id){
            return array();
        }
        $where['id'] = $id;
        $where['status'] = '2';
        $commM = new W_CommModel();
        return $commM->getAdvertInfos($where);
    }

    /**
     * Notes:散文详情
     * User: ZHL
     * Date: 2019/9/29
     */
    function essayInfo($mould, $id){
        $mould = set_strval($mould);
        $id = set_intval($id);
        if(!$mould || !$id){
            return array();
        }
        $where['id'] = $id;
        $commM = new W_CommModel();
        $info = $commM->getEssayInfos($mould, $where);
        if(!empty($info)){
            $classid = $info['classid'];
            $cinfo = $commM->getClassInfoNo($classid);
            $param['tpl'] = isset($cinfo['infohtml']) ? $cinfo['infohtml'] : '';
            $param['mould'] = $mould;
            $param['id'] = $info['id'];
            $info['url'] = setEssayUrl($param);
        }
        return $info;
    }

    /**
     * Notes: 获取散文上一条记录
     * User: ZhuHaili
     * Date: 2019/9/30
     */
    function prevInfo($id,$mould,$classid=0){
        $id = set_intval($id);
        $mould = set_strval($mould);
        $classid = $classid ? set_intval($classid) : 0;
        if(!$mould || !$id){
            return array();
        }
        $commM = new W_CommModel();
        $info = $commM->prevInfo($mould,$classid,$id);
        if(!empty($info)){
            if($classid){
                $cinfo = $commM->getClassInfoNo($classid);
                $param['tpl'] = isset($cinfo['infohtml']) ? $cinfo['infohtml'] : '';
            }
            $param['mould'] = $mould;
            $param['id'] = $info['id'];
            $info['url'] = setEssayUrl($param);
        }
        return $info;
    }

    /**
     * Notes: 获取散文下一条记录
     * User: ZhuHaili
     * Date: 2019/9/30
     */
    function nextInfo($id,$mould,$classid=0){
        $id = set_intval($id);
        $mould = set_strval($mould);
        $classid = $classid ? set_intval($classid) : 0;
        if(!$mould || !$id){
            return array();
        }
        $commM = new W_CommModel();
        $info = $commM->nextInfo($mould,$classid,$id);
        if(!empty($info)){
            if($classid){
                $cinfo = $commM->getClassInfoNo($classid);
                $param['tpl'] = isset($cinfo['infohtml']) ? $cinfo['infohtml'] : '';
            }
            $param['mould'] = $mould;
            $param['id'] = $info['id'];
            $info['url'] = setEssayUrl($param);
        }
        return $info;
    }

    /**
     * Notes:文章列表/散文列表
     * User: ZHL
     * Date: 2019/9/30
     * Desc: $limit格式整数或者一维数组，$taids格式为指定键值对的一维数组 如array('indexflag'=>'1','hotflag'=>'1')
     */
    function essayList($mould, $classid, $limit=array(), $whereArr=array(), $orderby=array(), $word=array(), $body = false){
        // $mould，$classid 效验
        $classid = set_intval($classid);
        if(!$mould || !is_string($mould)){
            return array();
        }
        // 锁定栏目id
        if($classid){
            $where['a.classid'] = $classid;
        }
        // 默认为已发布状态
        $where['a.status'] = '2';

        // 其他条件
        if(!empty($whereArr) && is_array($whereArr)){
            foreach($whereArr as $k=>$v){
                $where[$k] = $v;
            }
        }

        // 模糊查询
        $locate = array();
        if(is_array($word) && !empty($word)){
            foreach($word as $k=>$v){
                $locate[$k] = $v;
            }
        }else if(is_string($word) && !empty($word)){
            $locate['a.title'] = $word;
        }

        // 排序
        $order = 'a.viewseq desc,a.id desc,a.createdate desc';
        if(!empty($orderby) && is_array($orderby)){
            $order = '';
            foreach($orderby as $k=>$v){
                $order .= "$k $v,";
            }
            $order = substr($order,0,-1);
        }
        $commM = new W_CommModel();
        $list = $commM->getEssayLists($mould, $where, $locate, $limit, $order, $body);
        if(empty($list)){
            return array();
        }
        foreach($list as $k=>$v){
            $param['tpl'] = $v['infohtml'];
            $param['mould'] = $v['mouldcode'];
            $param['id'] = $v['id'];
            $list[$k]['url'] = setEssayUrl($param);
        }
        return $list;
    }

    /**
     * Notes:文章列表/散文列表-多栏目展示
     * User: ZHL
     * Date: 2019/11/05
     * Desc: $limit格式整数或者一维数组，$taids格式为指定键值对的一维数组 如array('indexflag'=>'1','hotflag'=>'1')
     */
    function essayListMore($mould, $classid, $limit=array(), $whereArr=array(), $orderby=array(), $word=array(), $body = false){
        $classid = set_intval($classid);
        if(!$mould || !is_string($mould)){
            return array();
        }
        // 栏目id
        if($classid){
            $where['ac.classid'] = $classid;
        }
        // 默认为已发布状态
        $where['a.status'] = '2';

        // 其他条件
        if(!empty($whereArr) && is_array($whereArr)){
            foreach($whereArr as $k=>$v){
                $where[$k] = $v;
            }
        }

        // 模糊查询
        $locate = array();
        if(is_array($word) && !empty($word)){
            foreach($word as $k=>$v){
                $locate[$k] = $v;
            }
        }else if(is_string($word) && !empty($word)){
            $locate['a.title'] = $word;
        }

        // 排序
        $order = 'a.viewseq desc,a.id desc,a.createdate desc';
        if(!empty($orderby) && is_array($orderby)){
            $order = '';
            foreach($orderby as $k=>$v){
                $order .= "$k $v,";
            }
            $order = substr($order,0,-1);
        }
        $commM = new W_CommModel();
        $list = $commM->getEssayListsMore($mould, $where, $locate, $limit, $order, $body);
        if(empty($list)){
            return array();
        }
        foreach($list as $k=>$v){
            $param['tpl'] = $v['infohtml'];
            $param['mould'] = $v['mouldcode'];
            $param['id'] = $v['id'];
            $list[$k]['url'] = setEssayUrl($param);
        }
        return $list;
    }

    /**
     * Notes: 获取分页数据-动态分页通用
     * User: ZHL
     * Date: 2019/9/30
     */
    function pageLists($mould, $classid, $page, $param=array(), $related=false, $body = false){
        if($GLOBALS['SYS']['classmore_flag'] != 1){
            $related = false;
        }
        $where = isset($param['w']) ? $param['w'] : array();
        $orderby = isset($param['o']) ? $param['o'] : array();
        $keyword = isset($param['k']) ? $param['k'] : array();

        $result = array('code'=>40000,'msg'=>'数据获取失败','data'=>array(),'pages'=>array());
        $page = set_intval($page) > 0 ? set_intval($page) : 1;
        $pageSize = 10; //可以根据栏目初始化每页条数
        if(!$mould || !is_string($mould)){
            $mould = 'article';
        }
        // 其他条件
        $whereArr['a.status'] = '2';
        //可以根据栏目初始化其他条件
        if(!empty($where) && is_array($where)){
            foreach($where as $k=>$v){
                $whereArr[$k] = $v;
            }
        }

        $commM = new W_CommModel();

        // 栏目id条件
        $classid = set_intval($classid);
        if($classid){
            $classInfo = $commM->getClassInfoNo($classid);
            if(!empty($classInfo)){
                $pageSize = $classInfo['pagelist']; // 获取后台设置的分页数
            }
            if($related){
                // 散文多栏目展示时
                $whereArr['ac.classid'] = $classid;
            }else{
                // 散文单栏目展示时
                $whereArr['a.classid'] = $classid;
            }
        }

        // 模糊查询条件
        $locate = array();
        if(is_array($keyword) && !empty($keyword)){
            foreach($keyword as $k=>$v){
                $locate[$k] = $v;
            }
        }else if(is_string($keyword) && !empty($keyword)){
            $locate['title'] = $keyword;
        }

        // 排序
        $order = 'a.viewseq desc,a.id desc,a.createdate desc';
        if(!empty($orderby) && is_array($orderby)){
            $order = '';
            foreach($orderby as $k=>$v){
                $order .= "$k $v,";
            }
            $order = substr($order,0,-1);
        }

        // 分页数据总数
        if($related){
            // 多栏目
            $dataCount = $this->essayListCountMore($mould, $whereArr, $locate);
        }else{
            // 单栏目
            $dataCount = $this->essayListCount($mould, $whereArr, $locate);
        }

        // 加载分页扩展
        $pageE = new zhlPages($page, $dataCount, $pageSize);
        $limitArr = $pageE->limitArr;

        // 获取分页数据
        if($related){
            // 多栏目
            $list = $commM->getEssayListsMore($mould, $whereArr, $locate, $limitArr, $order, $body);
        }else{
            // 单栏目
            $list = $commM->getEssayLists($mould, $whereArr, $locate, $limitArr, $order, $body);
        }

        foreach($list as $k=>$v){
            $param['tpl'] = $v['infohtml'];
            $param['mould'] = $v['mouldcode'];
            $param['id'] = $v['id'];
            $list[$k]['url'] = setEssayUrl($param);
        }
        $result['code'] = 10000;
        $result['msg'] = '获取数据成功';
        $result['data'] = $list;
        $result['pages'] = $pageE->show();
        return $result;
    }

    /**
     * Notes: 获取分页公共数据-支持异步无限加载
     * Type: ajax
     * User: ZHL
     * Date: 2019/9/30
     */
    function pageListsAjax($param){
        $mould = isset($param['m']) ? $param['m'] : '';
        $classid = isset($param['c']) ? $param['c'] : 0;
        $page = isset($param['p']) ? $param['p'] : 1;
        $limit = isset($param['n']) ? $param['n'] : 0;
        $where = isset($param['w']) ? $param['w'] : array();
        $orderby = isset($param['o']) ? $param['o'] : array();
        $keyword = isset($param['k']) ? $param['k'] : array();
        $related = isset($param['r']) ? set_intval($param['r']) : 0;
        $gt = isset($param['gt']) ? $param['gt'] : array();
        $lt = isset($param['lt']) ? $param['lt'] : array();
        $gts = isset($param['gts']) ? $param['gts'] : array();
        $lts = isset($param['lts']) ? $param['lts'] : array();

        $hint = array('code'=>'40000','msg'=>'失败，稍后再试！');
        if(!$mould || !is_string($mould)){
            $hint['code'] = '40001';
            $hint['msg'] = '请选择模型分类';
            return $hint;
        }
        $where['a.status'] = '2';
        // 其他条件
        if(!empty($where) && is_array($where)){
            foreach($where as $k=>$v){
                $whereArr[$k] = $v;
            }
        }
        // 栏目和初始分页数
        $defaultLimit = 10;
        $commM = new W_CommModel();
        $classid = set_intval($classid);
        if($classid){
            $classInfo = $commM->getClassInfoNo($classid);
            if(!empty($classInfo)){
                $defaultLimit = $classInfo['pagelist'];
            }
            if($related){
                // 散文多栏目展示时
                $whereArr['ac.classid'] = $classid;
            }else{
                // 散文单栏目展示时
                $whereArr['a.classid'] = $classid;
            }
        }

        // 模糊查询
        $locate = array();
        if(is_array($keyword) && !empty($keyword)){
            foreach($keyword as $k=>$v){
                $locate[$k] = $v;
            }
        }else if(is_string($keyword) && !empty($keyword)){
            $locate['title'] = $keyword;
        }
        // 排序
        $order = 'a.viewseq desc,a.id desc,a.createdate desc';
        if(!empty($orderby) && is_array($orderby)){
            $order = '';
            foreach($orderby as $k=>$v){
                $order .= "$k $v,";
            }
            $order = substr($order,0,-1);
        }
        // 分页数据总数
        if($related){
            // 多栏目
            $sumCount = $this->essayListCountMore($mould, $whereArr, $locate);
        }else{
            // 单栏目
            $sumCount = $this->essayListCount($mould, $whereArr, $locate, $gt, $lt, $gts, $lts);
        }
        $page = set_intval($page) > 0 ? set_intval($page) : 1;
        $limit = set_intval($limit) > 0 ? set_intval($limit) : $defaultLimit;
        $pages = ceil($sumCount/$limit);
        $page = $page >= $pages ? $pages : $page;
        $limitArr[0] = ($page-1)*$limit;
        $limitArr[1] = $limit;

        // 获取分页数据
        if($related){
            // 多栏目
            $list = $commM->getEssayListsMore($mould, $whereArr, $locate, $limitArr, $order);
        }else{
            // 单栏目
            $list = $commM->getEssayLists($mould, $whereArr, $locate, $limitArr, $order, $gt, $lt, $gts, $lts);
        }

        foreach($list as $k=>$v){
            $param['tpl'] = $v['infohtml'];
            $param['mould'] = $v['mouldcode'];
            $param['id'] = $v['id'];
            $list[$k]['url'] = setEssayUrl($param);
        }
        $pageDate['page'] = $page;
        $pageDate['pages'] = $pages;
        $pageDate['counts'] = $sumCount;
        $result['code'] = '10000';
        $result['msg'] = 'success';
        $result['pageinfo'] = $pageDate;
        $result['data'] = $list;
        return $result;
    }

    /**
     * Notes: 获取散文总数-单栏目
     * User: ZhuHaili
     * Date: 2019/9/30
     */
    private function essayListCount($mould, $where, $locate, $gt=array(), $lt=array(), $gts=array(), $lts=array()){
        $commM = new W_CommModel();
        return $commM->essayListCount($mould, $where, $locate, $gt, $lt, $gts, $lts);
    }

    /**
     * Notes: 获取散文总数-多栏目
     * User: ZhuHaili
     * Date: 2019/11/5
     */
    private function essayListCountMore($mould, $where, $locate){
        $commM = new W_CommModel();
        return $commM->essayListCountMore($mould, $where, $locate);
    }

    /**
     * Notes: 文章阅读量统计
     * User: ZhuHaili
     * Date: 2019/11/6
     * @return string
     */
    function essayReadCount($mould, $id){
        $result = array('code'=>40000,'msg'=>'Essay count param error', 'readsum'=>0);
        $id = set_intval($id);
        $mould = set_strval($mould);
        if(!$id || !$mould){
            return $result;
        }
        $where['id'] = $id;
        $commM = new W_CommModel();
        $info = $commM->essayInfoNo($mould, $where);
        $readsum = isset($info['visit']) ? $info['visit']+1 : 1;
        $table = SYS_DB_PREFIX.$mould;
        $sql = "update $table set visit=visit+1 where id = $id";
        $result = $this->query($sql);
        $rowCount = $this->rowCount();
        if($rowCount){
            $result['code'] = 10000;
            $result['msg'] = 'success';
            $result['readsum'] = $readsum;
            return $result;
        }
        $result['code'] = 40001;
        $result['msg'] = 'Essay count fail';
        return $result;
    }

    /**
     * Notes: 文章踩赞统计更新
     * User: ZhuHaili
     * Date: 2019/11/6
     * @return string
     */
    function essayOpsCount($mould, $id, $type){
        $id = set_intval($id);
        $type = set_intval($type);
        $mould = set_strval($mould);
        if(($type != 1 && $type != 2) || !$id || !$mould){
            return 'Essay count param error';
        }
        $cookieName = 'essayopsflag_'.$id.'_'.$type;
        $essayopsflag = getCookies($cookieName);
        if($essayopsflag == 'Y'){
            return $type == 1 ? '您已经点赞过了，谢谢你的支持' : '您已经点踩过了，我会更加努力';
        }
        $table = SYS_DB_PREFIX.$mould;
        if($type == 1){
            $sql = "update $table set laud=laud+1 where id = $id";
        }else{
            $sql = "update $table set oppose=oppose+1 where id = $id";
        }
        $this->query($sql);
        $result = $this->rowCount();
        if($result){
            setCookies($cookieName,'Y',86400); //24小时
            return 'Y';
        }
        return '操作失败，稍后再试';
    }

    /**
     * Notes: 获取公共位信息
     * User: ZhuHaili
     * Date: 2020/3/17
     * @return array
     */
    function unitTxt($classId = 0){
        $commM = new W_CommModel();
        $info = $commM->unitTxt(1);
        if($classId){
            $classInfo = $this->classInfo($classId);
            if(!empty($classInfo)){
                $info['seo_title'] = $classInfo['seotitle'];
                $info['seo_key'] = $classInfo['seokeyword'];
                $info['seo_desc'] = $classInfo['seodesc'];
            }
        }
        return $info;
    }

    /**
     * Notes: 文章赞踩数据获取
     * User: ZhuHaili
     * Date: 2020/4/9
     */
    function essaySupportCount($mould, $id){
        $result = array('code'=>40000,'data'=>array());
        if(!$mould || !$id){
            return $result;
        }
        $where['id'] = $id;
        $commM = new W_CommModel();
        $arr = $commM->essayInfoNo($mould, $where);
        if(empty($arr)){
            return $result;
        }
        $laud = intval($arr['laud']) > 9999 ? '9999+' : intval($arr['laud']);
        $oppose = intval($arr['oppose']) > 9999 ? '9999+' : intval($arr['oppose']);
        $result['code'] = 10000;
        $result['data']['laud'] = $laud;
        $result['data']['oppose'] = $oppose;
        return $result;
    }
}