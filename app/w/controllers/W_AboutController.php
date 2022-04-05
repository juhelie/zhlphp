<?php
include SYS_PATH.'extend/function.php';
class W_AboutController extends Controller {

    /**
     * Notes: 链接数据库实例
     * User: ZhuHaili
     */
    function index() {
        $id = $this->fun->input('id','3','','','');
        $commSer = new W_CommServe();
        $list1 = $commSer->getClassLists($id);
        $list2 = $commSer->getClassListsV2($id);
        $list3 = $commSer->getClassListsV3($id);
        $r = runCosts();
        array_push($list1, $r);
        $this->fun->json($list1,true);
        //loger('1111111111');
        //$list4 = $commSer->getClassListsV3('2022-01-01');
        echo '<pre>';
        print_r($list1);
        echo '</pre><br><br>';
        echo '<pre>';
        print_r($list2);
        echo '</pre><br><br>';
        loger_r($list3);
        exit;
    }

    /**
     * Notes:
     * User: ZhuHaili
     */
    function body(){
        $this->display(); // 默认使用w_about/body.php文件
    }

    /**
     * Notes:
     * User: ZhuHaili
     */
    function body2(){
        $this->displays();  // 默认使用w_about/body2.php文件，同时启用views/common.php作为父级页面
    }
}