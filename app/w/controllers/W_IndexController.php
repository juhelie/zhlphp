<?php
include SYS_PATH.'extend/function.php';
class W_IndexController extends Controller {
    // 首页方法
    function index() {
        $this->set('title','w  index  index');
        $this->display('w_index/index'); // 使用w_index/index.php,不启用公共文件common.php
    }

    /**
     * Notes:
     * User: ZhuHaili
     */
    function item(){
        $this->displays(); // 公共页面使用w_index文件下common.php
    }

}