<?php
use Illuminate\Database\Capsule\Manager as DB;

class WebController extends AbstractController
{
    public $user;
    public function init()
    {
        parent::init();
        $this->user = Yaf\Registry::get(SSN_INFO);
    }

    /**
     * music
     */
    public function MusicAction()
    {
    }
    public function MusiclistAction()
    {
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        $mp3list = DB::table('mp3')->orderBy('create_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('web/url')->toArray();
        $mp3list['rows'] = $mp3list['data'];

        echo json_encode($mp3list);
    }

    /**
     * 手机报价
     */
    public function phoneAction()
    {
    }
    public function phonelistAction()
    {
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        $phonelist = DB::table('phone_log')->orderBy('time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('web/url')->toArray();
        $phonelist['rows'] = $phonelist['data'];

        echo json_encode($phonelist);
    }

    /**
     * mac 报价
     */
    public function macAction()
    {
    }
    public function maclistAction()
    {
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        $maclist = DB::table('mac_log')->orderBy('time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('web/url')->toArray();
        $maclist['rows'] = $maclist['data'];

        echo json_encode($maclist);
    }

    /**
     * 糗事百科
     */
    public function qsbkAction()
    {

    }
    public function qsbklistAction()
    {
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        $qsbklist = DB::table('content')->orderBy('article_publish_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('web/url')->toArray();
        $qsbklist['rows'] = $qsbklist['data'];

        echo json_encode($qsbklist);
    }

    /**
     * 时间轴
     */
    public function timelineAction()
    {
        $list = DB::table('admin_user_log')->orderBy('time','desc')->limit(100)->get()->toArray();
        $this->getView()->assign('list',$list);
    }
    public function loglistAction()
    {
        header("Content-type:text/html;charset=utf-8");

        $file = APP_PATH.'/log/error_log_17_05_26.log';
        $list = array();
        $fp = @fopen($file,'r');
        if($fp){
            $i=0;
            while(!feof($fp)){
                $line = fgets($fp, 1024);
                if(trim($line))
                    $list[$i]['info'] = $line;
                $i++;
            }
        }
        $this->getView()->assign('list',$list);
    }
}
