<?php
include SYS_PATH.'extend/function.php';
class Index_AboutController extends Controller {
 
    // 首页方法
    function index() {
        for($i=0;$i<10000;$i++){
            $val = 'title'.$i;
            $this->set($val,$val);
        }

        $this->display('w_index/index');
        //$this->display();
    }
    function about(){
        exit('index  about  about');
    }
}