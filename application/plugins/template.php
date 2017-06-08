<?php
/**
 * 系统模板插件
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/5
 * Time: 下午2:57
 */
class templatePlugin extends Yaf\Plugin_Abstract
{
    /**
     * 操作正式处理之前执行，判断输出设定
     * @return void
     */
    public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        $config = Yaf\Registry::get('config');
        $view   = new View();
        if (!empty($config->path->view)) {
            $view->setScriptPath($config->path->view);
        }
        Yaf\Dispatcher::getInstance()->setView($view);
    }
}