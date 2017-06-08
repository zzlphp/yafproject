<?php
/**
 * Created by PhpStorm.
 * User: ziziliang
 * Date: 2017/5/12
 * Time: 下午2:26
 */
use Yaf\Controller_Abstract as Controller;
use Illuminate\Database\Capsule\Manager as DB;

require APP_PATH.'/application/library/Mail/PHPMailerAutoload.php';

class ScriptController extends Controller
{
    public function sendMailAction()
    {
        $list = DB::table('mail')->where(['status'=>1])->get()->toArray();
        if(is_array($list) && !empty($list)){
            foreach ($list as $k=>$v){
                $res = $this->send($v->to_email,$v->title,$v->content);
                if($res){
                    $status = 2;
                }else{
                    $status = 3;
                    Log::write($res,'log','mail');
                }
                DB::table('mail')->where('id', $v->id)->update(['status' => $status]);
            }
        }
    }
    public function send($email,$title,$content)
    {
        $mail = new PHPMailer;
        $mail->SMTPDebug = 3;
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.163.com"; // 这个要到qq邮箱，设置-账户，开启smtp
        $mail->Port = 25; //设置邮件服务器的端口，默认为25
        $mail->Username = "zhangziliangs@163.com"; // 邮箱名称
        $mail->Password = "liang123"; // 邮箱密码
        $mail->From = 'zhangziliangs@163.com'; // 邮箱名称
        $mail->FromName = 'zhangziliang'; // 发信人名称

        $mail->addAddress($email,$email); // 发给谁
        $mail->isHTML(true);
        $mail->CharSet = "utf-8";
        $mail->Subject = $title;
        $mail->Body    = $content;

        if(!$mail->send()) {
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }
}
