<?php
/**
 * 同步服务器上的数据到本地数据
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/26
 * Time: 上午10:36
 */
use Illuminate\Database\Capsule\Manager as DB;

class SyncDBController extends Yaf\Controller_Abstract{

    public function SyncTagAction()
    {
        //获取同步的位置
        $id = DB::connection('local')->table('sync_info')->get();
        var_dump($id);
    }
}