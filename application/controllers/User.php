<?php
use Illuminate\Database\Capsule\Manager as DB;

class UserController extends AbstractController
{
    public $user;
    public function init()
    {
        parent::init();
        $this->user = Yaf\Registry::get(SSN_INFO);
    }
    /**
     * 显示整个后台页面框架及菜单
     *
     * @return string
     */
    public function IndexAction()
    {
    }
    public function UserlistAction()
    {
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        $users = DB::table('f_user')->Paginate($pagesize,['*'],'page', $page)->withPath('user/url')->toArray();
        $users['rows'] = $users['data'];

        echo json_encode($users);
    }

    /**
     * 开房数据
     */
    public function MembersAction()
    {

    }
    public function MemeberlistAction()
    {
        $request = $this->getRequest();

        $name    = $_GET['name'];
        $code    = $_GET['code'];

        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];

        //设置缓存
        $key = md5('memberlist_'.$_GET['pageOffset'].$_GET['pageSize'].$name.$code);
        $yac = new Yac();
        if(!$yac->get($key)){
            if($name && $code){
                $members = DB::connection('kf')->table('members')->where('name','like',$name.'%')
                    ->where('code','like',$code.'%')
                    ->Paginate($pagesize,['*'],'page', $page)->withPath('user/url')->toArray();
            }else if($name && !$code){
                $members = DB::connection('kf')->table('members')->where('name','like',$name.'%')
                    ->Paginate($pagesize,['*'],'page', $page)->withPath('user/url')->toArray();
            }else if(!$name && $code){
                $members = DB::connection('kf')->table('members')->where('code','like',$code.'%')
                    ->Paginate($pagesize,['*'],'page', $page)->withPath('user/url')->toArray();
            }else{
                $members = DB::connection('kf')->table('members')->Paginate($pagesize,['*'],'page', $page)->withPath('user/url')->toArray();
            }
            $members['rows'] = $members['data'];

            $yac->set($key,$members);
        }else{
            $members = $yac->get($key);
        }

        echo json_encode($members);
    }
}
