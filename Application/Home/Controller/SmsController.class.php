<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 登录
 * Class IndexController
 * @package Home\Controller
 */
class SmsController extends Controller{

    /**
     * 发送验证码
     *  account手机号
     * send_type 1注册发送验证码
     */
    public function sendVerify(){
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['account'],'error_msg'=>'请输入手机号'),
            array('check_type'=>'in_array','parameter' =>$request['send_type'],'error_msg'=>'类型错误','condition'=>array('register','forget','modify_pwd','modify_account')),
            array('check_type'=>'regex','parameter' => $request['account'],'error_msg'=>'手机号格式错误','condition'=>C('MOBILE')),
        );
        check_param($param);//判断参数的合法性
        $result = D('Sms')->sendVerify($request['account'],$request['send_type']);
        if($result['success']){
            apiResponse('1',$result['success']);
        }else{
            apiResponse('0',$result['error']);
        }
    }
}