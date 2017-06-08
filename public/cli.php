<?php

// demo: php cli.php "request_uri=/api/activity/runactivityjob"

// 程序启动时间
define('APP_START_TIME', microtime(true));

// 程序根目录
define("APP_PATH",  realpath(dirname(__FILE__) . '/../'));

// 视图目录
define("VIEW_PATH", APP_PATH . '/view/');

define('SSN_LINE',  'line');
define('SSN_INFO',  'admin');
define('SSN_SA',    0);

define("INI_PATH",  '/tmp/yaconf/'); /* 指向public的上一级 */
define('LIBRARY_PATH',ini_get('yaf.library'));
$app  = new Yaf\Application(INI_PATH . "application.ini", ini_get('yaf.environ'));

$request = [];
foreach ($argv as $key => $value) {
    if ($key != 0) {
        parse_str($value, $output);
        $request = array_merge($request, $output);
    }
}

$app->bootstrap()->getDispatcher()->dispatch(new Yaf\Request\Simple());