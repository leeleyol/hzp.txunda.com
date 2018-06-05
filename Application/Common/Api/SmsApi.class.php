<?php
namespace Common\Api;
require_once './ThinkPHP/Library/Vendor/BasalticSms/nusoap.php';

/**
 * Class SmsApi
 * @package Common\Api
 * 消息发送接口
 */
class SmsApi {
    /*
     * 助通短信Api
     */
    public  static function sendSms($receiver,$content){
        $tkey = date('YmdHis',time());
        //短信接口相关参数
        $sms_account    = 'asn88hy'; //用户账号
        $sms_password   = md5(md5('zknDE3').$tkey); //用户密码
        $sms_phone      = $receiver; //手机号
        $sms_template   = $content.'【优进】'; //发信内容
        $sms_template   = iconv("UTF-8", "UTF-8", $sms_template);
        $sms_url        = "http://www.api.zthysms.com/sendSms.do?"; //发送的网址
        $sms_url       .= "username=$sms_account&password=$sms_password&mobile=$sms_phone&content=$sms_template&tkey=$tkey&xh=";

        $sms_content    = file_get_contents($sms_url); //发送短信  获取返回信息
        $sms_response   = explode(",",$sms_content);  //处理返回信息
        if($sms_response[0] != 1) {
            return array('error' => '发送失败');
        } else {
            return true;
        }
    }
}