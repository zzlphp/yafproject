<?php
define("INI_PATH",  '/tmp/yaconf/'); /* 指向public的上一级 */
define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
define('WEB_ROOT',  '/');
define("VIEW_PATH",  realpath(dirname(__FILE__) . '/../view/')); /* 指向public的上一级 */

define('SSN_LINE',  'line');
define('SSN_INFO',  'admin');
define('SSN_SA',    0);
define('LIBRARY_PATH',ini_get('yaf.library'));

$app  = new Yaf\Application(INI_PATH . "application.ini", ini_get('yaf.environ'));

//Yaf\Dispatcher::getInstance()->catchException(TRUE);
$app->bootstrap()->run();

