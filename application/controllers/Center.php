<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/12
 * Time: 下午2:26
 */

use Yaf\Controller_Abstract as Controller;
use Illuminate\Database\Capsule\Manager as DB;
use Http\Curl;
use Util\Ip;

class CenterController extends Controller
{
    /**
     * UserController::init()
     *
     * @return void
     */

    /**
     * 默认显示后台首页菜单及用户摘要信息
     *
     * @return string
     */
    public function IndexAction()
    {
//        echo (new Ip())->get_client_ip();//->get_client_ip();exit;
        $params = array();
        $this->getView()->assign($params);
    }

    /**
     * 显示用户登录页面
     *
     * @return string   html
     */
    public function LoginAction()
    {
        $session = Yaf\Session::getInstance();
        $session->set(SSN_LINE, false);
        $session->set(SSN_INFO, array('id' => 0, 'name' => '', 'dept' => '', 'sa' => 0));
        Yaf\Registry::set(SSN_LINE, $session->get(SSN_LINE));
        Yaf\Registry::set(SSN_INFO, $session->get(SSN_INFO));
    }

    /**
     * 用户登录处理
     *
     * @return string   json
     */
    public function LoginPostAction()
    {
        $request    = $this->getRequest();
        $name       = $request->getPost('loginname', '');
        $pwd        = $request->getPost('loginpwd', '');

        $return = array();
        if ($name == '' || $pwd == ''){
            $return['msg'] = '信息不完整';
            $return['code'] = 300;
        }else{
            $user_info = DB::table('admin_user')->where(['user_name'=>$name,'password'=>md5($pwd),'status'=>1])->get()->toArray();
            if($user_info){
                $user_info = $user_info[0];
                if (empty($user_info)){
                    $return['msg'] = '帐号不存在';
                    $return['code'] = 300;
                }elseif ($user_info->status == 2){
                    $return['msg'] = '您的账户暂时不可用';
                    $return['code'] = 300;
                } else{
                    $admin = array(
                        'id'    => $user_info->id,
                        'name'  => $user_info->user_name,
                        'sa'    => 0,
                    );
                    if ('admin' == $user_info->user_name){
                        $admin['sa'] = SSN_SA; //系统管理员
                    }
                    $session = Yaf\Session::getInstance();
                    $session->set(SSN_LINE, true);
                    $session->set(SSN_INFO, $admin);

                    Yaf\Registry::set(SSN_LINE, true);
                    Yaf\Registry::set(SSN_INFO, $admin);

                    $return['msg'] = '登录成功';
                    $return['code'] = 200;
                }
            }else{
                $return['msg'] = '登录失败';
                $return['code'] = 300;
            }
        }
        echo json_encode($return);
    }

    /**
     * 显示修改登录密码表单
     *
     * @return string
     */
    public function PasswdAction()
    {
        // no code
        // display html form
    }
    /**
     * 判断并修改用户的登录密码
     *
     * @return string
     */
    public function PasswdPostAction()
    {
        $request = $this->getRequest();
        $oldpwd = $request->getPost('oldPassword', '');
        $newpwd = $request->getPost('newPassword', '');
        if ($oldpwd == '' || $newpwd == '')
        {
            DWZ::R(300, "信息填写不完整。");
            Yaf\Registry::set(SSN_LOG, "信息填写不完整。");
        }
        else
        {
            $A = new AdminModel(Yaf\Registry::get('db'));

            $admin  = Yaf\Registry::get(SSN_INFO);
            $user   = $A->getUserById($admin['id']);
            if (empty($user))
            {
                DWZ::R(300, "错误的账号。");
                Yaf\Registry::set(SSN_LOG, "使用了错误的账号。");
            }
            else if ($user['au_password'] != md5(MD5_PRE . $user['au_name'] . $oldpwd . MD5_SUF))
            {
                DWZ::R(300, "旧密码错误。");
                Yaf\Registry::set(SSN_LOG, "旧密码填写错误。");
            }
            else if ($A->setUserById($admin['id'], array('au_password' => md5(MD5_PRE . $user['au_name'] . $newpwd . MD5_SUF))))
            {
                DWZ::R(200, '登录密码修改成功！');
                Yaf\Registry::set(SSN_LOG, "成功修改登录密码。");
            }
        }
    }
    public function profileAction()
    {
        $key = 'profile';
        $yac = new Yac();

        if($yac->get($key)){
            $result = unserialize($yac->get($key));
        }else{
            $result = (new Curl())->post('http://blog.zzlphp.com/?json=get_recent_posts');
            $result = object_array($result);
            $yac->set($key,serialize($result));
        }
        $blog_list = $result['posts'];
        $this->getView()->assign('blog_list',$blog_list);
    }
    public function BlogAction()
    {
        $key = 'bloglist';
        $yac = new Yac();

        if($yac->get($key)){
            $result = unserialize($yac->get($key));
        }else{
            $result = (new Curl())->post('http://blog.zzlphp.com/?json=get_recent_posts');
            $result = object_array($result);
            $yac->set($key,serialize($result));
        }
        $blog_list = $result['posts'];
        $this->getView()->assign('blog_list',$blog_list);
    }
    public function BlogdetailAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $key = 'blogdetail_'.$id;
        $yac = new Yac();

        if($yac->get($key)){
            $result = unserialize($yac->get($key));
        }else{
            $result = (new Curl())->post('http://blog.zzlphp.com/?p='.$id.'&json=1');
            $result = object_array($result);
            $yac->set($key,serialize($result));
        }
        $this->getView()->assign('detail',$result['post']);
    }
}
