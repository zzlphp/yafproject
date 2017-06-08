<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/14
 * Time: 下午12:48
 */
use Illuminate\Database\Capsule\Manager as DB;

class StatisticsController extends AbstractController
{
    public function init()
    {
        parent::init();
        $this->user = Yaf\Registry::get(SSN_INFO);
    }
    public function indexAction()
    {
        $phone_list = DB::table('phone_log')->where('title','like','【港版】苹果 iPhone 6S%')->get()->toArray();
        $phone = array();
        foreach ($phone_list as $k=>$v){
            $month = date('m',strtotime($v->time));
            if($month<=3){
                $q = ' Q1';
            }elseif($month>3 && $month<=6){
                $q = ' Q2';
            }elseif($month>6 && $month<=9){
                $q = ' Q3';
            }elseif($month>9 && $month<=12){
                $q = ' Q4';
            }
            $phone[$k]['period'] = date('Y',strtotime($v->time)).$q;
            $phone[$k]['iphone'] = $v->price;
        }
//        echo json_encode($phone);exit;
        $this->getView()->assign('phone',json_encode($phone));
//        $this->getView()->assign('phone',$str);
    }
}