<?php
use Yaf\Controller_Abstract as Controller;

class PublicController extends Controller
{

    /**
     * 404 not found
     */
    public function unknowAction()
    {
//        die('Oops,你访问的页面不存在!');
    }
    public function cutimgAction()
    {

        $filename = APP_PATH."/log/1.jpg";
        $im = imagecreatefromjpeg($filename);

        $new_img_width = 200;
        $new_img_height= 80;

        $newim = imagecreatetruecolor($new_img_width,$new_img_height);

        imagecopyresampled($newim,$im,0,0,140,0,$new_img_width,$new_img_height,$new_img_width,$new_img_height);

        $to_file = APP_PATH."/log/2.jpeg";

        imagejpeg($newim,$to_file,100);
        imagedestroy($newim);
        imagedestroy($im);
        echo 1;
    }
    public function loginbaiduAction()
    {
        $url = "http://index.baidu.com/?tpl=trend&word=%B3%B5%D5%B9";
        $ss = file_get_contents('http://index.baidu.com/?tpl=trend&word=%B3%B5%D5%B9');
        var_dump($ss);exit;
//$url = "http://www.baidu.com/";

//        $cookdir = "d:/www/html/mndl/cookie.txt";
        $cookdir = APP_PATH."/log/cookie.txt";

        $ch = curl_init();

        curl_setopt ($ch, CURLOPT_URL, $url);

        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //这句是必须有的  设定为不验证证书 下面是不验证host。

        curl_setopt ($ch, CURLOPT_HEADER, 0);

        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookdir); //保存cookies 发送用户浏览器信息 发送访问来源

        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");

        curl_setopt ($ch, CURLOPT_REFERER, "http://www.baidu.com/");

        $contents = curl_exec($ch);
        curl_close($ch);
        $post_fields = array();

//        $post_fields = $this->preg_data($contents,'aid',$post_fields);
//        $post_fields = $this->preg_data($contents,'isphone',$post_fields);

//        $post_fields['mem_pass'] = 'on'; //post 数据数组  记住我的登录状态
//
//        $post_fields = $this->preg_data($contents,'more_param',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'need_coin',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'need_pay',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'next_target',$post_fields);

        $post_fields['password'] = 'liang123123'; //post 数据数组  记住我的登录状态

//        $post_fields = $this->preg_data($contents,'pay_method',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'psp_tt',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'return_method',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'return_type',$post_fields);
//
//        $post_fields = $this->preg_data2($contents,'safeflg',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'skip_ok',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'tpl',$post_fields);
//
//        $post_fields = $this->preg_data($contents,'tpl_ok',$post_fields);

        $post_fields['u'] = 'http://www.baidu.com/';

        $post_fields['username'] = '18616680539'; //post 数据数组  记住我的登录状态

        $post_fields['verifycode'] = '';


        $ch = curl_init();

        curl_setopt ($ch, CURLOPT_URL, $url);
        $ss = file_get_contents('http://index.baidu.com/?tpl=trend&word=%B3%B5%D5%B9');
        var_dump($ss);
        echo 222;exit;
    }

    public function preg_data($source,$chr,$array){
        preg_match('/<input\s*type="hidden"\s*name="'.$chr.'"\s*value="(.*?)">/i', $source, $matches);
        if(!empty($matches)) {
            $a = $matches[1];
        } else {
            die('Not found the '.$chr.'.');
        }
        $array[$chr]=$a;
        return $array;
    }
    public function preg_data2($source,$chr,$array){
        preg_match('/<input\s*type="hidden"\s*name="'.$chr.'"\s*id="'.$chr.'"\s*value="(.*?)">/i', $source, $matches);
        if(!empty($matches)) {
            $a = $matches[1];
        } else {
            die('Not found the '.$chr.'.');
        }
        $array[$chr]=$a;
        return $array;
    }
}