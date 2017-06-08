<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/24
 * Time: 上午9:41
 */
use Illuminate\Database\Capsule\Manager as DB;

class BaiduController extends Yaf\Controller_Abstract
{
    public function searchAction()
    {
        $title = '车展';
        phpQuery::newDocumentFile("https://www.baidu.com/s?wd={$title}");
        $companies = pq('#page')->find('span');
        $data = array();
        foreach($companies as $company)
        {
            echo 1;continue;
//            var_dump($company);exit;
//            $title = pq($company)->find('a')->text();
//            echo $title."\r\n";
        }
    }
}