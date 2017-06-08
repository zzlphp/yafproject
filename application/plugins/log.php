<?php
/**
 * 系统模板插件
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/5
 * Time: 下午2:57
 */
class logPlugin extends Yaf\Plugin_Abstract
{
    /**
     * 路由执行之后
     * @param \Yaf\Request_Abstract $request
     * @param \Yaf\Response_Abstract $response
     */
    public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
//        var_dump($request->getControllerName());
//        var_dump($request->getActionName());exit;
    }
    /**
     * 操作正式处理之前执行，判断输出设定
     * @return void
     */
    public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        /*
         * CLI模式下禁用日志
         * */
        if (!$request->isCli ()) {
            $user = Yaf\Registry::get(SSN_INFO);
            $log = array();
            $log['user_id'] = isset($user['id'])??0;
            $log['controller'] = $request->getControllerName();
            $log['action'] = $request->getActionName();
            $log['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
            $log['time'] = time();
            $log['ip'] = get_client_ip();
            Log::addDBLog($log);
        }
    }
}