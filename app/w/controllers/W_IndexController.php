<?php
include SYS_PATH.'extend/function.php';
class W_IndexController extends Controller {
 
    // 首页方法
    function index() {
        $this->set('title','w  index  index');
        $this->display('w_index/index');
    }

    function about(){
        exit('w  index  about');
    }
}