<?php
include SYS_PATH.'extend/function.php';
class Index_IndexController extends Controller {
 
    // 首页方法
    function index() {
        $this->set('title','index  index index');
        $this->set('title2','title2');
        $this->set('title3','title3');
        $this->display('w_index/index');
        //$this->display();
    }
    function about(){
        exit('index index about');
    }
}