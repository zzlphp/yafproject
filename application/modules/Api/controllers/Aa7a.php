<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/24
 * Time: 上午9:41
 */
use Illuminate\Database\Capsule\Manager as DB;

class Aa7aController extends Yaf\Controller_Abstract
{
    public function SearchPhoneAction() {

        $detailurl = "http://www.136136.com/shop_ph.asp";

        $chd = curl_init ();
        curl_setopt ( $chd, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)' );
        curl_setopt ( $chd, CURLOPT_NOBODY, 0 );
        curl_setopt ( $chd, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $chd, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $chd, CURLOPT_TIMEOUT, 200 );
        curl_setopt ( $chd, CURLOPT_URL, $detailurl );
        $htmsd = curl_exec ( $chd );
        curl_close ( $chd );

        $html_detail = htm_fields ($htmsd,'<td valign="top" class="width750">','<td valign="top" class="width750">','<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:10px">');
        $find1 = "shop_info.asp?proid=";
        $find2 = '</a></td>';

        $findLen1 = strlen ( $find1 );
        $findLen2 = strlen ( $find2 );
        $cxnums = substr_count($html_detail,'shop_info.asp?proid=');
        for($j=1;$j<=$cxnums;$j++){
            $tmpp[0] = 0;
            $xxx = 0;
            if ($j == 1) {
                $xxx = 0;
            } else {
                $xxx = $tmpp [$j - 1] + $findLen1;
            }
            $tmpp [$j] = stripos ( $html_detail, $find1, $xxx );
            $divv = substr ( $html_detail, $tmpp [$j] + $findLen1 );
            $poz = strpos ( $divv, "</a>" );
            $res= substr ( $divv, 0, $poz );
            //$res= substr($res,7);
            //$res ="【".$res;
            //echo $res."\r\n";
            $arrre = explode('>',$res);
            //print_r($arrre);
            //echo $arrre[1]."\r\n";

            $tmp['title'] = iconv('gb2312','utf-8',$arrre[1]);//$arrre[1];


            $tmpp1[0] = 0;
            $xxx1 = 0;
            if ($j == 1) {
                $xxx1 = 0;
            } else {
                $xxx1 = $tmpp1 [$j - 1] + $findLen2;
            }
            $tmpp1 [$j] = stripos ( $html_detail, $find2, $xxx1 );
            //echo $tmpp1 [$j].'-';
            $divv1  = substr ( $html_detail, $tmpp1 [$j] + $findLen2 );
            $poz1   = strpos ( $divv1, "</td>" );
            $res1   = substr ( $divv1, 0, $poz1 );


            //echo substr($res1,54)."\r\n";

            $tmp['price']   = substr($res1,54);
            $tmp['time']    = date('Y-m-d');

            $res = DB::table('phone_log')->where('title',$tmp['title'])->where('time',$tmp['time'])->get()->toArray();
            if(empty($res)){
                var_dump(DB::table('phone_log')->insert($tmp));
            }else{
                echo "跳过".$tmp['title']."\r\n";
            }
        }
    }
    public function searchmacAction()
    {
        phpQuery::newDocumentFile("http://www.aa7a.cn/category-269-b0.html");
        $companies = pq('#goods')->find('.item_box');
        $data = array();
        foreach($companies as $k=>$company)
        {
            $data['title']  = pq($company)->find('.name a')->text();
            $data['detail'] = $data['title'];
            $data['price']  = pq($company)->find('.price')->text();
            $data['time']   = date('Y-m-d');

            $res = DB::table('mac_log')->where('title',$data['title'])->where('time',$data['time'])->get()->toArray();
            if(empty($res)){
                $result = DB::table('mac_log')->insert($data);
                var_dump($result);
            }else{
                echo "跳过".$data['title']."\r\n";
            }
        }
    }
}