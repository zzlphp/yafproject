<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/24
 * Time: 上午9:41
 */
use Illuminate\Database\Capsule\Manager as DB;
use Http\Baidupan;
use Http\Ysmb;
//use Wechat\Weixin;

class WechatController extends Yaf\Controller_Abstract
{
    public $dyh_msg;
    public function init()
    {
        $this->dyh_msg = "欢迎关注是谁打了个喷嚏/可爱\r\n回复:电影名称 \r\n获取网络资源!";
    }
    public function dingyuehaoAction()
    {
        $options = array (
            'token' => 'ziziliang', // 填写你设定的key
            'appid' => 'wx3c8dfdb384f2b925', // 填写高级调用功能的appid
            'appsecret' => '51085cd5ca92ffa5f4dd271b7da85935' // 填写高级调用功能的密钥
        );
        $redis_option['expire'] = 7200;

        $Wechat = new Wechat\Weixin($options);

        $fromUser       = $Wechat->getRev()->getRevFrom();
        $content        = $Wechat->getRev()->getRevContent();
        $rcontent        = '';

        //获取消息发送者的用户openid
        $msgtype       = $Wechat->getRev()->getRevType();

        if($msgtype == $Wechat::MSGTYPE_EVENT) {
            $action = $Wechat->getRev()->getRevEvent();
            if ($action['event'] == "subscribe") {
                $str = $this->dyh_msg;
                $Wechat->text($str)->reply();
            }
        }

        $title= $content;
        $page = 1;
        $pagesize = 5;
        if($title){
            $searchlist = DB::table('baidupan')->where('title','like','%'.$title.'%')->orderBy('create_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('wechat/url')->toArray();
            if(!$searchlist['total']){
                $this->SearchResource($title);
                $searchlist = DB::table('baidupan')->where('title','like','%'.$title.'%')->orderBy('create_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('wechat/url')->toArray();
            }
        }else{
            $searchlist = DB::table('baidupan')->orderBy('create_time','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('wechat/url')->toArray();
        }
        if(is_array($searchlist['data']) && !empty($searchlist['data'])){
            foreach ($searchlist['data'] as $k=>$v){
                $rcontent .= $v->title."\r\n".$v->url."\r\n";
            }
        }else{
            $rcontent = '暂无资源...';
        }

        $log                   = array();
        $log['FromUserName']   = $fromUser;
        $log['CreateTime']     = time();
        $log['MsgType']        = $msgtype;
        $log['Content']        = $content;
        $log['Rcontent']       = $rcontent;
        DB::table('wx_message_accept')->insert($log);

        $Wechat->text($rcontent)->reply();
        echo '';
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