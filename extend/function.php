<?php
// +----------------------------------------------------------------------
// | Function Helper
// +----------------------------------------------------------------------
// | Copyright (c) 2018
// +----------------------------------------------------------------------

/**
 * 获取13位unix时间戳
 */
if (!function_exists('time13')) {
    function time13(){
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}

/**
 * Notes: 创建目录
 * User: ZhuHaili
 */
if (!function_exists('createDirList')) {
    function createDirList($path){
        if (!file_exists($path)){
            createDirList(dirname($path));
            mkdir($path, 0777);
        }
    }
}

/**
 * Notes: 删除路径下的所有文件夹及文件
 * User: ZhuHaili
 */
if (!function_exists('del_dir')) {
    function del_dir($path){
        //如果是目录则继续
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
                //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$val)){
                        //子目录中操作删除文件夹和文件
                        del_dir($path.$val.'/');
                        //目录清空后删除空文件夹
                        @rmdir($path.$val.'/');
                    }else{
                        //如果是文件直接删除
                        unlink($path.$val);
                    }
                }
            }
        }
    }
}


/**
 * Notes: 中文字符串截取
 * User: ZhuHaili
 * Return : string
 */
if (!function_exists('substrm')) {
    function substrm($str, $len = 10, $start = 0, $type=0){
        if($type){
            $leng = mb_strlen($str,"utf8");
            if($leng > $len){
                return @mb_substr($str, $start, $len, 'utf-8').'…';
            }
        }
        return @mb_substr($str, $start, $len, 'utf-8');
    }
}

/**
 * Notes: 获取随机字符串
 * User: ZhuHaili
 * Return : string
 */
if (!function_exists('rand_strs')) {
    function rand_strs($len=4, $type=0, $str=''){
        $newStr = ''; //要获取的字符串
        if(preg_match("/[\x7f-\xff]/", $str) && $type != '8' && $type != '9'){ //类型不为8,9并且存在中文字符时强制用汉字
            $str = '';
        }
        switch($type){ //选定字符串类型
            case 1: //纯数字
                $str = '0123456789'.$str;
                break;
            case 2: //纯小写字母
                $str = 'abcdefghijklmnopqrstuvwxyz'.$str;
                break;
            case 3: //纯大写字母
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$str;
                break;
            case 4: //纯字母
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'.$str;
                break;
            case 5: //小写字母和数字
                $str = 'abcdefghijklmnopqrstuvwxyz0123456789'.$str;
                break;
            case 6: //大写字母和数字
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.$str;
                break;
            case 7: //字母和数字
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.$str;
                break;
            case 8: //预生成汉字
                $str = '大小多少左右上下白云太阳月亮星工人爸妈爷奶今明后天金木水火土红黄绿蓝紫色衣花公母哭笑苦高兴吃玩乐打豆羊牛马车水电飞鸟东西南北方向'.$str;
                break;
            case 9: //自动生成汉字
                for($i=0; $i<$len; $i++){
                    // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
                    $strNo = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
                    $str .= iconv('GB2312', 'UTF-8', $strNo); // 转码
                }
                break;
            default :
                // 默认，去掉了容易混淆的字母oOlZz和数字012
                $str = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXY3456789'.$str;
                break;
        }
        if($type != 9){ //所需大于固定长度时
            $strLen = mb_strlen($str,'UTF8');
            if($len > $strLen) { //位数过长重复字符串一定次数
                $str = str_repeat($str,$len);
            }
        }
        if($type == 8 || $type == 9){ //汉字时
            //计算最大长度-1
            $strLen = mb_strlen($str,'UTF8')-1;
            //循环 $len 次获得字符串
            for($i=0;$i<$len;$i++){
                $newStr .= mb_substr($str, floor(mt_rand(0,$strLen)),1,'UTF8'); //随机长度内数字，截取随机数向后一个长度
            }
        }else{ //普通字符串
            $newStr = substr(str_shuffle($str), 0, $len); //字符串随机排序后截取$len长度
        }
        return $newStr;
    }
}

/**
 * @param 获取html内容中img所有路径
 * @return null
 *  从HTML文本中提取所有图片
 */
if (!function_exists('html_img_path')) {
    function html_img_path($content){
        $pattern = "/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
        preg_match_all($pattern, htmlspecialchars_decode($content), $match);
        if (!empty($match[1])) {
            return $match[1];
        }
        return array();
    }
}


/**
 * @param 多维数组转换成一维数组
 * @return null
 */
if (!function_exists('arrays_array')) {
    function arrays_array($params){
        static $paramArr;
        foreach($params as $k=>$v){
            if(is_array($v)){
                arrays_array($v);
            }else{ // 参数为字符串时
                $paramArr[] = $v;
            }
        }
        return $paramArr;
    }
}

/**
 * Notes: 过滤特殊字符并转实体（防sql注入）
 * User: ZhuHaili
 * Date: 2019/10/30
 * Return : string
 */
if (!function_exists('sql_str')) {
    function sql_str($str){
        return htmlspecialchars(str_replace(array("*", "--", "=", "'", '"', "/", "\\", "{", '}', '(', ')'), "", $str));
    }
}

/**
 * Notes: 面包屑导航匹配
 * Date: 2019/11/18
 * Return : array();
 */
if (!function_exists('menu_crumb')) {
    function menu_crumb($arr, $id){
        $list = array();
        while($id){
            $flag = false;
            foreach($arr as $v){
                if($v['id']==$id){
                    $n['url'] = $v['url'];
                    $n['classname'] = $v['classname'];
                    array_unshift($list,$n);
                    $id = $v['fid'];
                    $flag = true;
                }
            }
            if(!$flag){
                break;
            }
        }
        return $list;
    }
}

/**
 * 导航等级分配
 */
/*if (!function_exists('getNavLevel')) {
    function getNavLevel($arr, $fid=0, $level=0){
        static $navRes;//静态变量 只会被初始化一次
        foreach($arr as $k=>$v){
            $vfid = intval($v['fid']);
            $id = intval($v['id']);
            $fid = intval($fid);
            if($vfid==$fid){
                $tmp = $v;
                $tmp['level'] = $level;
                $navRes[] = $tmp;
                unset($arr[$k]);
                getNavLevel($arr,$id,$level+1);
            }
        }
        return $navRes;
    }
}*/

/**
 * 导航等级分配
 */
if (!function_exists('getNavLevel')) {
    function getNavLevel($arr, $fid=0, $level=0, $flag=0){
        if(!$flag){
            $GLOBALS['valNavLevel'] = array();
            $flag = 1;
        }
        foreach($arr as $k=>$v){
            $vfid = intval($v['fid']);
            $id = intval($v['id']);
            $fid = intval($fid);
            if($vfid==$fid){
                $tmp = $v;
                $tmp['level'] = $level;
                $GLOBALS['valNavLevel'][] = $tmp;
                unset($arr[$k]);
                getNavLevel($arr,$id,$level+1,$flag);
            }
        }
        return $GLOBALS['valNavLevel'];
    }
}

/**
 * 导航无限分级引用方式
 */
if (!function_exists('getTree')) {
    function getTree($arr, $fid=0, $level=0){
        $items = array();
        foreach($arr as $v){
            $items[$v['id']] = $v;
        }
        $result = array();
        foreach($items as $k => $item){
            if(isset($items[$item['fid']])){
                $items[$item['fid']]['child'][] = &$items[$k];
            }else{
                $result[] = &$items[$k];
            }
        }
        return $result;
    }
}

/**
 * Notes: 公用提示层
 * User: ZhuHaili
 * Return : html
 */
if (!function_exists('comm_alert')) {
    function comm_alert($txt, $url = '', $time = 3, $type=false){
        $html =  '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '<meta name="viewport" content="minimal-ui,width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">';
        $html .= '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">';
        $html .= '<title>提示</title></head><body class="noselect">';
        $html .=  '<style>';
        $html .=  '*{margin:0;padding:0;}.pop_slide{position: fixed;top: 0;width: 100%;height: 100%;z-index: 999;background: rgba(0,0,0,0.5);}';
        $html .=  '.pop_slide .short_textBox{position: absolute;top: 35%;width: 300px;left: 50%;margin-left:-150px;background: #fff;z-index: 1000;text-align: center;border-radius: 10px;}';
        $html .=  '.pop_slide .short_textBox .short_text{font-size: 16px;font-size: 16px;line-height: 25px;padding: 30px 20px 30px 20px;text-align: center;}';
        $html .=  '.pop_slide .short_textBox .popbtm .left{display:inline-block;width: 50%;text-align: center;line-height: 35px;font-size: 16px;background: #ccc;color: #fff;border-radius: 0 0 0 10px;}';
        $html .=  '.pop_slide .short_textBox .popbtm .right{display:inline-block;width: 50%;text-align: center;line-height: 35px;font-size: 16px;background: #3dc6da;color: #fff;border-radius: 0 0 10px 0;}';
        $html .=  '</style>';
        $html .=  '<div class="pop_slide time"><div class="short_textBox"><div class="short_text">'.$txt.'</div></div></div>';
        $html .=  '<script>setTimeout("returnback()",'.($time*1000).');</script>';
        if($url){
            $html .=  '<script>function returnback(){window.location.href="'.$url.'";}</script>';
        }else{
            if($type){
                $html .=  '<script>function returnback(){window.location.href=window.document.referrer;}</script>';
            }else{
                $html .=  '<script>function returnback(){window.location.href=history.go(-1);}</script>';
            }
        }
        $html .=  '</body></html>';
        echo $html;exit;
    }
}

/**
 * 计数单位
 */
if (!function_exists('number_unit')) {
    function number_unit($number){
        if($number < 1000){
            return $number;
        }
        if($number >= 1000 && $number < 10000){
            return round($number/1000,1).'千';
        }
        if($number >= 10000 && $number < 1000000){
            return round($number/10000,1).'万';
        }
        /*if($number >= 100000 && $number < 1000000){
            return round($number/100000,1).'十万';
        }*/
        if($number >= 1000000 && $number < 10000000){
            return round($number/1000000).'百万';
        }
        if($number >= 10000000 && $number < 100000000){
            return round($number/10000000).'千万';
        }
        if($number >= 100000000 && $number < 10000000000){
            return round($number/100000000,1).'亿';
        }
        /*if($number >= 1000000000 && $number < 10000000000){
            return round($number/1000000000,1).'十亿';
        }*/
        if($number >= 10000000000 && $number < 100000000000){
            return round($number/10000000000).'百亿';
        }
        if($number >= 100000000000 && $number < 1000000000000){
            return round($number/100000000000).'千亿';
        }
        if($number >= 1000000000000 && $number < 10000000000000){
            return round($number/1000000000000).'万亿';
        }
        if($number >= 10000000000000){
            return '万亿+';
        }
    }
}
