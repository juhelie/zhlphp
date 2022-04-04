<?php
// +----------------------------------------------------------------------
// | Class 系统内置辅助方法
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

class Fun {

    //定义静态变量保存当前类的实例
    private static $instance;

    //防止在外部实例化
    private function __construct(){

    }

    //防止在外部克隆
    private function __clone(){

    }

    //通过静态公有的方法获取这个类的实例
    public static function getInstance(){
        //当前对象不属于当前例就实例化
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @fun   过滤函数
     * @hint  默认,s=字符串
     * @desc  $type:d=数字,b=布尔型,f=小数,h=html标签,s=字符串;$leng:截取长度,$type为小数时代表小数点后;$start:截取字符串时开始位置
     */
    public function input($name='', $val = null, $type='s', $leng=0, $start=0){
        if(!isset($_REQUEST)){
            return $val;
        }
	    $default = $_REQUEST;
        if($name){
            if(!isset($_REQUEST[$name])){
                return $val;
            }
	        $default = $_REQUEST[$name];
        }
        $default = $default ? $default : $val;
        return $this->varArr($default, $type, $leng, $start);
    }

    /**
     * Notes: 参数遍历
     */
    public function varArr($var, $type, $leng, $start){
        $param = array();
        if(is_array($var)){
            foreach($var as $k=>$v){
                $param[$k] = $this->varArr($v, $type, $leng, $start);
            }
        }else{
            return $this->varStr($var, $type, $leng, $start);
        }
        return $param;
    }

    /**
     * @fun   过滤操作
     */
    public function varStr($var, $type, $leng, $start){
        if(!$var){
            return $var;
        }
        if($type == 'd'){           // 整形
            $value = intval($var);
        }else if($type == 'b'){     // 布尔
            $value = $var === true ? true : false;
        }else if($type == 'f') {    // 浮点
            $value = (float)$var;
            //$value = floatval($var);
        }else if($type == 'h'){     // html标签
            $var =  !get_magic_quotes_gpc() ? addslashes($var) : $var;
            $value = trim($var);
        }else{                      // 字符串
            $value = htmlspecialchars(trim($var), ENT_QUOTES);
            if(!get_magic_quotes_gpc()){
                $value = addslashes($value);
            }
        }
        // 截取长度
        if($value){
            if($leng > 0){
                if($type == 'f'){
                    $value = round($value, $leng);
                }else{
                    $value = mb_substr($value,intval($start),intval($leng),'utf-8');
                }
            }
        }
        return $value;
    }

    /**
     * @fun 去除空白字符
     */
    public function trimStr($str){
        $qian = array(" ","　","\t","\n","\r");
        $hou = array("","","","","");
        return str_replace($qian,$hou,$str);
    }

    /**
     * @fun 邮政编码验证
     */
    public function isZipcode($str){
        return preg_match('/^[0-9]{6}$/', $str);
    }

    /**
     * @fun 邮箱验证
     */
    public function isEmail($str){
        return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $str);
    }

    /**
     * @fun 手机号验证
     */
    public function isPhone($str){
        return preg_match("/^1[23456789]{1}\d{9}$/",str);
    }

    /**
     * @fun 数组转json
     */
    public function json($arr, $echo = false){
        $json = json_encode($arr,320);
        $jsonErr = json_last_error();
        if($jsonErr){
            sysloger('json_encode(),Error:'.$jsonErr,dirname(__FILE__).'/Fun.class.php',__LINE__);
        }
        if($echo){
            echo $json; exit;
        }else{
            return $json;
        }
    }

    /**
     * @fun json转数组
     */
    public function arr($json){
        $arr = json_decode($json,true);
        $jsonErr = json_last_error();
        if($jsonErr){
            sysloger('json_decode(),Error:'.$jsonErr,dirname(__FILE__).'/Fun.class.php',__LINE__);
        }
        return $arr;
    }
}