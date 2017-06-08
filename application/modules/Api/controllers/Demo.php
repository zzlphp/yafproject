<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/16
 * Time: 下午3:54
 */
use Illuminate\Database\Capsule\Manager as DB;

class DemoController extends Yaf\Controller_Abstract {


    public function listAction() {
        $yac        = new Yac();
        $request    = $this->getRequest();
        $name       = $request->getParam('name','张');
        if($name){
            if(!$yac->get($name)){
                $res = DB::connection('kf')->table('members')->where('name','like','%'.$name.'%')->limit(100)->get()->toArray();
                $yac->set($name,serialize($res));
            }else{
                $res = unserialize($yac->get($name));
            }
            $result = array();
            foreach($res as $k=>$v){
                $result[$k]['Name'] = $v->name;
                $result[$k]['Version'] = $v->time;
                $result[$k]['Mobile'] = $v->phone;
                $result[$k]['Address'] = $v->address;
                $result[$k]['CtfId'] = $v->code;
                $result[$k]['EMail'] = $v->email;
            }
            echo json_encode($result);
        }
    }
    public function list2Action() {
        $yac        = new Yac();

        $request    = $this->getRequest();
        $name       = $request->getPost('name','');
        $code       = $request->getPost('code',0);
        $key = 'member_list_'.$code.$name;

        if(!$yac->get($key)){
            if($name && $code){
                $res = DB::connection('kf')->table('members')->where('name','like',$name.'%')->where('code','like',$code.'%')->limit(100)->get()->toArray();
                $yac->set($key,json_encode($res));
                echo json_encode($res);exit;
            }
            if($name){
                $res = DB::connection('kf')->table('members')->where('name','like',$name.'%')->limit(100)->get()->toArray();
                $yac->set($key,json_encode($res));
                echo json_encode($res);exit;
            }
            if($code){
                $res = DB::connection('kf')->table('members')->where('code','like',$code.'%')->limit(100)->get()->toArray();
                $yac->set($key,json_encode($res));
                echo json_encode($res);exit;
            }
        }else{
            echo $yac->get($key);
        }

    }
    public function list3Action() {
        $name = 'Z';
        $code = 110228;
        $res = DB::connection('kf')->table('members')->where('name','like','%'.$name.'%')->where('code','like','%'.$code.'%')->limit(100)->get()->toArray();
        print_r($res);
    }

    public function memberAction()
    {
        $pp         = DB::connection('kf')->table('pp')->where('id', 1)->find(1);
        $id         = $pp->num;
        $pagesize   = 10000;
        for($page=1;$page<=2000;$page++){
            $res = DB::connection('kf')->table('demo')->where('id','>',$id)->Paginate($pagesize,['*'],'page', $page)->toArray();
            if(is_array($res['data']) && !empty($res['data'])){
                foreach ($res['data'] as $key=>$value){
                    $new_data               = array();
                    $new_data['name']       = $value->Name;
                    $new_data['phone']      = $value->Mobile;
                    $new_data['email']      = $value->EMail;
                    $new_data['code']       = $value->CtfId;
                    $new_data['birthday']   = $value->Birthday;
                    $new_data['zip']        = $value->Zip;
                    $new_data['tel']        = $value->Tel;
                    $new_data['address']    = $value->Address;
                    $new_data['time']       = $value->Version;
                    DB::connection('kf')->table('members')->insert($new_data);
                    DB::connection('kf')->table('pp')->where('id', 1)->update(['num' => $value->id]);
                    echo 'success:'.$value->id."\r\n";
                }
            }
        }
        echo 'done:'."\r\n";
    }
    public function chatmsgAction()
    {
        $res = DB::table('wx_message_accept')->orderBy('id','desc')->limit(20)->get()->toArray();
        echo json_encode($res);
    }
    public function detailAction()
    {
        $yac        = new Yac();
        $request    = $this->getRequest();
        $id = $request->getParam('id',0);
        $key = 'detail_'.$id;
        if($id){
            if(!$yac->get($key)) {
                $result = DB::connection('kf')->table('members')->find($id);
                $res = object_array($result);
                $res['name'] = $res['name']==''?'-':$res['name'];
                $res['phone'] = $res['phone']==''?'-':$res['phone'];
                $res['email'] = $res['email']==''?'-':$res['email'];
                $res['code'] = $res['code']==''?'-':$res['code'];
                $res['birthday'] = $res['birthday']==''?'-':$res['birthday'];
                $res['zip'] = $res['zip']==''?'-':$res['zip'];
                $res['tel'] = $res['tel']==''?'-':$res['tel'];
                $res['address'] = $res['address']==''?'-':$res['address'];
                $res['time'] = $res['time']==''?'-':$res['time'];
                $yac->set($key,serialize($res));
            }else{
                $res = unserialize($yac->get($key));
            }
            echo json_encode($res);
        }
    }
    public function searchlistAction()
    {
        $title= $_GET['title'];
        if($title){
            $searchlist = DB::table('baidupan')->where('title','like','%'.$title.'%')->get()->toArray();
            if(!$searchlist){
                $this->SearchResource($title);
                $searchlist = DB::table('baidupan')->where('title','like','%'.$title.'%')->get()->toArray();
            }
        }else{
            $searchlist = DB::table('baidupan')->orderBy('create_time','desc')->limit(10)->get()->toArray();
        }

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