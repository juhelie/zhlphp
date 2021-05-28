<?php
include SYS_PATH.'extend/function.php';
class W_AboutController extends Controller {
 
    // 首页方法
    function index() {
        $this->set('title','w  about  index');
        $this->display('w_index/index');
    }

    function about(){
        exit('w  about  about');
    }
}