<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/24
 * Time: 上午9:41
 */
use Illuminate\Database\Capsule\Manager as DB;

class TodayController extends Yaf\Controller_Abstract
{
    public function oilpriceforshAction()
    {
        $this->todayprice();
        $city = '上海';
        //取7天的数据
        $y    = intval(date('Y'));
        $m    = intval(date('m'));
        $day  = intval(date('d'));
        $d   = $day-7;
        if($d<=0){
            $d = 1;
        }

        $res = DB::table('oilprice')->where('city','like',$city)
            ->where('date','>=',$y.'-'.$m.'-'.$d)->where('date','<=',$y.'-'.$m.'-'.$day)
            ->get()->toArray();
        if(empty($res)){
            $result = $this->getoilprice($city);
            if(isset($result['showapi_res_body']['list']) && !empty($result['showapi_res_body']['list'])){
                foreach ($result['showapi_res_body']['list'] as $key=>$value){
                    $data           = array();
                    $data['city']   = $city;
                    $data['p0']     = $value['p0'];
                    $data['p89']    = $value['p89'];
                    $data['p90']    = $value['p90'];
                    $data['p92']    = $value['p92'];
                    $data['p93']    = $value['p93'];
                    $data['p95']    = $value['p95'];
                    $data['p97']    = $value['p97'];
                    $data['date']   = date('Y-m-d');

                    DB::table('oilprice')->insert($data);
                }
            }
            $res = DB::table('oilprice')->where('city','like',$city)
                ->where('date','>=',$y.'-'.$m.'-'.$d)->where('date','<=',$y.'-'.$m.'-'.$day)
                ->get()->toArray();
        }
        $_92 = '';
        $_95 = '';
        if(!empty($res)){
            foreach ($res as $k=>$v){
                $_92 .= $v->p92.',';
                $_95 .= $v->p95.',';
            }
            if($_92)
                $_92 = substr($_92,0,-1);

            if($_95)
                $_95 = substr($_95,0,-1);

        }
        $return['s92'] = $_92;
        $return['s95'] = $_95;
        echo json_encode($return);
    }
    public function todayprice()
    {
        $city = '上海';
        $res = DB::table('oilprice')->where('city','like',$city)->where('date','like',date('Y-m-d'))->get()->toArray();
        if(empty($res)){
            $result = $this->getoilprice($city);
            if(isset($result['showapi_res_body']['list']) && !empty($result['showapi_res_body']['list'])){
                foreach ($result['showapi_res_body']['list'] as $key=>$value){
                    $data           = array();
                    $data['city']   = $city;
                    $data['p0']     = $value['p0'];
                    $data['p89']    = $value['p89'];
                    $data['p90']    = $value['p90'];
                    $data['p92']    = $value['p92'];
                    $data['p93']    = $value['p93'];
                    $data['p95']    = $value['p95'];
                    $data['p97']    = $value['p97'];
                    $data['date']   = date('Y-m-d');

                    DB::table('oilprice')->insert($data);
                }
            }
        }
    }
    public function oilpriceAction()
    {
        $city = isset($_GET['city'])?$_GET['city']:'上海';
        $res = DB::table('oilprice')->where('city','like',$city)->where('date','like',date('Y-m-d'))->get()->toArray();
        if(empty($res)){
            $result = $this->getoilprice($city);
            if(isset($result['showapi_res_body']['list']) && !empty($result['showapi_res_body']['list'])){
                foreach ($result['showapi_res_body']['list'] as $key=>$value){
                    $data           = array();
                    $data['city']   = $city;
                    $data['p0']     = $value['p0'];
                    $data['p89']    = $value['p89'];
                    $data['p90']    = $value['p90'];
                    $data['p92']    = $value['p92'];
                    $data['p93']    = $value['p93'];
                    $data['p95']    = $value['p95'];
                    $data['p97']    = $value['p97'];
                    $data['date']   = date('Y-m-d');

                    DB::table('oilprice')->insert($data);
                }
            }
            $res = DB::table('oilprice')->where('city','like',$city)->where('date','like',date('Y-m-d'))->get()->toArray();
        }
        echo json_encode($res);
    }
    public function getoilprice($city)
    {
        header("Content-Type:text/html;charset=UTF-8");
        date_default_timezone_set("PRC");
        $showapi_appid = '39489';  //替换此值,在官网的"我的应用"中找到相关值
        $showapi_secret = 'e69d936cac4b4daea7cbe6d0db6a3ed9';  //替换此值,在官网的"我的应用"中找到相关值
        $paramArr = array(
            'showapi_appid'=> $showapi_appid,
            'prov'=> $city
            //添加其他参数
        );

        $param = $this->createParam($paramArr,$showapi_secret);
        $url = 'http://route.showapi.com/138-46?'.$param;
        $result = file_get_contents($url);
        $result = json_decode($result,true);
        return $result;
    }
    //创建参数(包括签名的处理)
    public function createParam ($paramArr,$showapi_secret) {
        $paraStr = "";
        $signStr = "";
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $signStr .= $key.$val;
                $paraStr .= $key.'='.urlencode($val).'&';
            }
        }
        $signStr .= $showapi_secret;//排好序的参数加上secret,进行md5
        $sign = strtolower(md5($signStr));
        $paraStr .= 'showapi_sign='.$sign;//将md5后的值作为参数,便于服务器的效验
        return $paraStr;
    }
    public function regionlistAction()
    {
        $list = DB::table('sys_region')->where('sr_parent_id',1)->get()->toArray();
        echo json_encode($list);
    }
}