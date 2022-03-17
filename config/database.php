<?php
// +----------------------------------------------------------------------
// | Class  数据库配置 - 支持多数据库连接
// +----------------------------------------------------------------------
// | Copyright (c) 2022
// +----------------------------------------------------------------------

return array(
    array(
        'db_type' => 'mysql',
        'db_link' => 'mysqli',
        'db_host' => '127.0.0.1',
        'db_port' => 3306,
        'db_name' => 'zhl_cms2_20210320',
        'db_user' => 'root',
        'db_pwd' => '123456',
        'db_fix' => 'zhl_',
        'db_char' => 'utf8',
    ),
    array(
        'db_type' => 'mysql',
        'db_link' => 'pdo',
        'db_host' => '127.0.0.1',
        'db_port' => 3306,
        'db_name' => 'lc_wuyexiehui',
        'db_user' => 'root',
        'db_pwd' => '123456',
        'db_fix' => 'zhl_',
        'db_char' => 'utf8',
    ),
    /*array(
        'db_type' => 'oracle',
        'db_link' => 'oci',
        'db_host' => '***.***.***.***',
        'db_port' => 1521,
        'db_name' => 'orcl',
        'db_user' => '***',
        'db_pwd' => '***',
        'db_fix' => '',
        'db_char' => 'utf8',
    ),*/
);
