<?php
include SYS_PATH.'extend/function.php';
class W_AboutController extends Controller {
 
    // 首页方法
    function index() {
        $commSer = new W_CommServe();
        $list1 = $commSer->getClassLists('3');
        $list2 = $commSer->getClassListsV2('3');
        //$list3 = $commSer->getClassListsV3('2022-01-01');
        echo '<pre>';
        print_r($list1);
        echo '</pre><br><br>';
        echo '<pre>';
        print_r($list2);
        echo '</pre><br><br>';
        //loger_r($list3);
        exit;
    }

    function about(){
        exit('w_about_about');
    }
}