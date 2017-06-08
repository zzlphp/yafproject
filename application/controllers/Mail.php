<?php

use Illuminate\Database\Capsule\Manager as DB;

class MailController extends AbstractController
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

    /**
     * 信箱
     */
    public function mailboxAction()
    {
        $list   = DB::table('mail')->where(['user_id'=>$this->user['id']])->get()->toArray();
        $total  = DB::table('mail')->where(['user_id'=>$this->user['id']])->count();
        $this->getView()->assign('total',$total);
        $this->getView()->assign('list',$list);
    }
    /**
     * 查看信箱
     */
    public function detailAction()
    {
    }
    /**
     * 写信
     */
    public function composeAction()
    {
    }
    public function sendAction()
    {
        $request = $this->getRequest();
        $data = array();
        $data['user_id']        = $this->user['id']??0;
        $data['to_email']       = $request->getPost('to_email','');
        $data['title']          = $request->getPost('title','');
        $data['content']        = $request->getPost('content','');
        $data['create_time']    = time();
        $data['status']         = $request->getPost('status',0);

        if(is_email($data['to_email'])){
            $res = DB::table('mail')->insert($data);
            if($res){
                $arr['msg'] = 'success';
                $arr['code'] = 200;
            }else{
                $arr['msg'] = 'error';
                $arr['code'] = 300;
            }
        }else{
            $arr['msg'] = 'Email格式不正确';
            $arr['code'] = 300;
        }

        echo json_encode($arr);
    }

}
