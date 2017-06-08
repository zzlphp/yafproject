<?php

use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

class Bootstrap extends Bootstrap_Abstract
{

    public function _initLoader()
    {
        set_error_handler(
            create_function(
                '$severity, $message, $file, $line',
                'throw new ErrorException($message, $severity, $severity, $file, $line);'
            )
        );

        register_shutdown_function(array($this, 'cleanup'));

        Yaf\Loader::import(APP_PATH . "/vendor/autoload.php");
        Yaf\Loader::import(APP_PATH . "/application/function.php");

//        var_dump(Yaf\Loader::getInstance('/home/wwwroot/yaflibrary'));exit;
        // 注册本地类名前缀, 这部分类名将会在本地类库查找
        Yaf\Loader::getInstance(LIBRARY_PATH)->registerLocalNameSpace(array('Log', 'Cache', 'Upload', 'Http', 'Util'));
    }

    public function _initConfig()
    {
        $config = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $config);
    }

    public function _initDefaultName(Dispatcher $dispatcher)
    {
        $dispatcher->setDefaultModule('Index')->setDefaultController('Index')->setDefaultAction('index');
    }
    public function _initDatabaseEloquent()
    {
//        $ini =  Yaconf::get('application');

        $capsule = new Capsule;

        // 创建默认链接
        $capsule->addConnection(Yaf\Application::app()->getConfig()->database->toArray());

        // biz业务链接
        $capsule->addConnection(Yaf\Application::app()->getConfig()->database_kf->toArray(), 'kf');
        $capsule->addConnection(Yaf\Application::app()->getConfig()->database_local->toArray(), 'local');

        // 设置全局静态可访问
        $capsule->setAsGlobal();

        // 启动Eloquent
        $capsule->bootEloquent();

//        var_dump($capsule);exit;
        // define('DT', 'dt');
        // $capsule::connection('dt')->enableQueryLog();

    }

    public function _initRoute(Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        $router->addConfig(Yaf\Registry::get("config")->routes);

//        $unknow = new Yaf\Route\Rewrite('/404$', array('controller'=>'Public','action'=>'unknow'));
//        $router->addRoute('404', $unknow);
    }

    public function _initSession()
    {
//        try {
//            $redis = redisConnect();
//            $redis->ping();
//            $session = new Util_Session();
//            session_set_save_handler($session, true);
//        } catch (Exception $e) {
//            Log_Log::info('[Bootstrap] session init error:' . $e->getMessage(), true, true);
//        }
    }

    /**
     * 注册全局插件
     */
    public function _initPlugin(Dispatcher $dispatcher)
    {
        $t = new templatePlugin();
        $dispatcher->registerPlugin($t);

        $log = new logPlugin();
        $dispatcher->registerPlugin($log);
    }
    public function _initVariables(Dispatcher $dispatcher)
    {
        $session = Yaf\Session::getInstance();
        if ($session->has(SSN_LINE)){
            Yaf\Registry::set(SSN_LINE,  $session->get(SSN_LINE));
        }else{
            Yaf\Registry::set(SSN_LINE,  false);
        }
        if ($session->has(SSN_INFO)){
            Yaf\Registry::set(SSN_INFO,  $session->get(SSN_INFO));
        }else{
            Yaf\Registry::set(SSN_INFO,  array());
        }
    }
    public function cleanup()
    {

        restore_error_handler();

        // 捕获fatal error
        $e = error_get_last();
        if ($e['type'] == E_ERROR) {
            $str = <<<TYPEOTHER
[message] {$e['message']}
[file] {$e['file']}
[line] {$e['line']}
TYPEOTHER;
            // todo 发送邮件、短息、写日志报警……
        }

        // 定义了开关，便关闭log
//        if (!defined('SHUTDOWN')) {
//            Log::write(var_export($_REQUEST, true),'error','error.log');
//            Log::info('receive:' . var_export($_REQUEST, true), true, true);
//
//            // DEFAULT
////            $this->log(Capsule::getQueryLog(), 'DEFAULT');
//            Log::write(var_export(Capsule::getQueryLog(),true),'error','DEFAULT.log');
//            // 业务库相关SQL
//            if (defined('BIZ'))
////                $this->log(Capsule::connection(BIZ)->getQueryLog(), 'BIZ');
//                Log::write(Capsule::connection(BIZ)->getQueryLog(),'error','BIZ.log');
//        }

    }

    /**
     * @param $info
     * @param $link
     */
//    public function log($info, $link)
//    {
//        foreach ($info as $val) {
//            Log_Log::info('[' . $link . ' query] ' . $val['query'] . ' [bindings] ' . implode(' ', $val['bindings']) . ' [time] ' . $val['time'], 1, 1);
//        }
//    }
}