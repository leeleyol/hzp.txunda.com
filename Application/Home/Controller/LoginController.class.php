<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 登录
 * Class IndexController
 * @package Home\Controller
 */
class LoginController extends BaseController{

    /**
     * 登录界面
     */
    public function login(){
        $this->display('login');
    }

    /**
     * 执行登录方法
     */
    public function ajaxLogin(){
        $account  = $_POST['account'];
        $password = $_POST['password'];
        $where['account'] = $account;
        $where['status'] = array('neq',9);
        $member_info = M('Member')->where($where)->find();
        if(empty($member_info)){
            apiResponse('0','该手机号尚未注册');
        }
        if($member_info['password'] != md5($password)){
            apiResponse('0','密码错误');
        }
        apiResponse('1','登录成功',array('m_id'=>$member_info['id']));
    }



    /**
     * 注册界面
     */
    public function register(){
        $this->display('register');
    }
    /**
     * 执行注册方法
     */
    public function ajaxRegister(){
        $account  = $_POST['account'];
        $password = $_POST['password'];
        $verify = $_POST['verify'];
        //检查手机号是否已经被注册
        $res = M('Member')->where(array('account'=>$account,'status'=>array('neq',9)))->find();
        if($res){
            apiResponse('0','该手机号已被注册，请直接登录');
        }
        //检查验证码是否正确
        $this->checkVerify($account,$verify,'register');

        if(strlen($password)<6){
            apiResponse('0','密码至少为6位');
        }
        $data['type'] = 0;
        $data['account'] = $account;
        $data['password'] = md5($password);
        $data['nickname'] = $account;
        $data['intro'] = '有点懒 什么都没留下';
        $data['create_time'] = time();
        $res = M('Member')->data($data)->add();
        if($res){
            apiResponse('1','注册成功',array('m_id'=>$res));
        }else{
            apiResponse('0','注册失败');
        }
    }

    /**
     * 忘记密码
     */
    public function forgetPwd(){
        $this->display('forgetPwd');
    }

    public function ajaxForgetPwd(){
        $account  = $_POST['account'];
        $password = $_POST['password'];
        $verify = $_POST['verify'];
        //检查手机号是否已经被注册
        $member_info = M('Member')->where(array('account'=>$account,'status'=>array('neq',9)))->find();
        if(empty($member_info)){
            apiResponse('0','该手机号尚未注册');
        }
        //检查验证码是否正确
        $this->checkVerify($account,$verify,'forget');

        if(strlen($password)<6){
            apiResponse('0','密码至少为6位');
        }
        $where['id'] = $member_info['id'];
        $data['password'] = md5($password);
        $data['update_time'] = time();
        $res = M('Member')->where($where)->data($data)->save();
        if($res){
            apiResponse('1','找回密码成功');
        }else{
            apiResponse('0','找回密码失败');
        }
    }
}