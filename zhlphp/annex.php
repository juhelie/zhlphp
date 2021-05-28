<?php

// +----------------------------------------------------------------------
// | Class  核心入口-组件扩展
// +----------------------------------------------------------------------
// | Copyright (c) 2018
// +----------------------------------------------------------------------

// 安全访问监控
defined('SYS_VISIT_SAFE') or define('SYS_VISIT_SAFE', false);
// 安全访问检测（开启安全开关 & 不是接口）
if(SYS_VISIT_SAFE && !isset($_REQUEST['asktype'])){
    //if(SYS_PRO_PATH == SYS_APP_DEFAULT){ // 检测默认模块
    include 'public/safeverify/safemain.php';
    //}
}