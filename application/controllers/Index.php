<?php
use Illuminate\Database\Capsule\Manager as DB;
use Http\Curl;
class IndexController extends AbstractController
{
    /**
     * 显示整个后台页面框架及菜单
     *
     * @return string
     */
    public function IndexAction()
    {
//        $this->redirect(WEB_ROOT . 'center/');
        $key = 'bloglist';
        $yac = new Yac();

        if(0){//$yac->get($key)){
            $result = unserialize($yac->get($key));
        }else{
            $result = (new Curl())->post('http://blog.zzlphp.com/?json=get_recent_posts');
            $result = object_array($result);
            $yac->set($key,serialize($result));
        }
        $blog_list = $result['posts'];
//        print_r($blog_list);
        $this->getView()->assign('blog_list',$blog_list);
    }

    public function mongoAction()
    {
        $client = new MongoDB\Client("mongodb://120.55.164.243:27017");
        $collection = $client->tracker->vistLog;
        $result = $collection->distinct("_uid");
        $i=0;
        foreach ($result as $entry) {
            ++$i;
            $_uid = $entry;
            $res[$i]['_uid'] = $_uid;
            $res[$i]['list'] = $collection->find( ['_uid'=>$_uid],[ '_id','_uid','url','title','view_ts','browser_name','available_resolution' ] )->toArray();
        }
        $params['result'] = $res;
        $this->getView()->assign($params);
    }
}
