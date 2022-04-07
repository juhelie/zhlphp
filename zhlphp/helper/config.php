<?php
// +----------------------------------------------------------------------
// | Class  系统配置
// +----------------------------------------------------------------------
// | Copyright (c) 2022
// +----------------------------------------------------------------------

return array(
    'SYS_APP_PATH' => 'app',        // 应用路径
    'SYS_APP_DEFAULT' => 'w',       // 程序指向默认模块
    'SYS_DEBUG' => true,            // 调试开关(系统错误是否输出到页面)
    'SYS_DEBUG_LOG' => true,        // 系统日志开关(系统错误是否打印日志)
    'SYS_FLAG_LOG' => true,         // 程序日志开关(程序自定义日志开关)
    'SYS_PAGE404' => false,         // 404页面开关
    'SYS_URL_BOGUS' => true,        // 伪静态开关
    'SYS_APP_URL_FIX' => '.html',   // 伪静态后缀初始值(必须.开头)
    'SYS_TMPL_FIX' => '.php',       // view视图加载模版文件后缀(可修改,必须.开头)
    'SYS_TMPL_PARENT' => 'common',  // view视图加载父级公共模版名称(可修改)
    'SYS_LOG_PATH' => 'runtime/logs/',      // 程序日志目录
    'SYS_ERR_PATH' => 'runtime/error/',     // 系统错误日志目录
    'SYS_VISIT_SAFE' => true,       // 安全访问监控开关(扩展组件)
);