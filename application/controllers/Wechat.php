<?php
use Illuminate\Database\Capsule\Manager as DB;
use Http\Baidupan;
use Http\Ysmb;
class WechatController extends AbstractController
{
    public $user;
    public function init()
    {
        parent::init();
        $this->user = Yaf\Registry::get(SSN_INFO);
    }

    /**
     * 微信对话
     */
    public function ChatmsgAction()
    {
    }
    public function ChatmsglistAction()
    {
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        $wechatmsglist = DB::table('wx_message_accept')->orderBy('CreateTime','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('user/url')->toArray();
        $wechatmsglist['rows'] = $wechatmsglist['data'];

        echo json_encode($wechatmsglist);
    }
    public function SearchAction()
    {

    }
    public function SearchlistAction()
    {
        $title= $_GET['title'];
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        if($title){
            $searchlist = DB::table('baidupan')->where('title','like','%'.$title.'%')->orderBy('create_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('wechat/url')->toArray();
            if(!$searchlist['total']){
                $this->SearchResource($title);
                $searchlist = DB::table('baidupan')->where('title','like','%'.$title.'%')->orderBy('create_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('wechat/url')->toArray();
            }
        }else{
            $searchlist = DB::table('baidupan')->orderBy('create_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('wechat/url')->toArray();
        }

        $searchlist['rows'] = $searchlist['data'];

        echo json_encode($searchlist);
    }
    public function SearchResource($title)
    {
        $url = "http://ysmb.hghjt.cn/?s={$title}";
        phpQuery::newDocumentFile($url);
        $companies = pq('#post_container')->find('.article');
        $data = array();
        foreach($companies as $k=>$company)
        {
            $data['title'] = pq($company)->find('h2')->text();

            foreach (pq($company)->find('a') as $t) {
                $href = $t -> getAttribute('href');
            }
            $data['url'] = $this->findUrl($href);

            $data['create_time'] = time();
            $result = DB::table('baidupan')->insert($data);
        }
    }
    public function findUrl($href)
    {
        phpQuery::newDocumentFile($href);
        $companies = pq('#post_content')->find('p span');
        $data = array();
        foreach($companies as $k=>$company)
        {
            foreach(pq($company)->find('a') as $f){
                $href = $f -> getAttribute('href');
                break;
            }
        }
        return $href;
    }
}
