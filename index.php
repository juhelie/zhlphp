<?php
header("Content-type: text/html; charset=utf-8");
//append annex
// 应用目录
define('SYS_PATH', __DIR__.'/');

// 开启调试模式
define('SYS_DEBUG', false);

// 是否开启伪静态
define('SYS_URL_BOGUS', false);

// 开启404
define('SYS_PAGE404', false);

// 安全访问监控
define('SYS_VISIT_SAFE', false);

// 伪静态后缀（如： .htm 或者 .html .jsp等等）
define('SYS_APP_URLFIX', '.html');

// 默认模块
//define('SYS_APP_DEFAULT', 'w');

// 加载框架
require './zhlphp/Base.php';