<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 设置
 * Class SetController
 * @package Home\Controller
 */
class SetController extends BaseController{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 设置首页
     */
    public function setIndex(){
        $this->display('setIndex');
    }

    /**
     * 获取设置界面的数据
     */
    public function ajaxSetIndex(){
        $m_id = $_POST['m_id'];
        if(empty($m_id)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $member_info = M('Member')
            ->where(array('id'=>$m_id,'status'=>array('neq',9)))->field('id as m_id,account')->find();
        if(empty($member_info)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $config = D('Config')->parseList();
        $member_info['service_phone'] = $config['WEB_SITE_TEL'];
        apiResponse('1','请求成功',$member_info);
    }

    /**
     * 修改手机号第一步
     */
    public function modAccountOne(){
        $this->display('modAccountOne');
    }

    /**
     * 获取界面数据
     */
    public function ajaxModAccountOne(){
        $m_id = $_POST['m_id'];
        if(empty($m_id)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $account = $_POST['account'];
        $verify  = $_POST['verify'];

        //检查验证码是否正确
        $this->checkVerify($account,$verify,'modify_account');

        apiResponse('1','验证通过');
    }


    /**
     * 修改手机号第二步
     */
    public function modAccountTwo(){
        $this->display('modAccountTwo');
    }

    public function ajaxModAccountTwo(){
        $m_id = $_POST['m_id'];
        if(empty($m_id)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $account = $_POST['account'];
        $verify  = $_POST['verify'];

        //检查验证码是否正确
        $this->checkVerify($account,$verify,'modify_account');

        //判断手机号是否已经被注册了
        $res = M('Member')->where(array('account'=>$account,'status'=>array('neq',9)))->find();
        if($res){
            apiResponse('0','该手机号已被绑定');
        }

        $res = M('Member')->where(array('i'=>$m_id))->data(array('account'=>$account))->save();
        if($res){
            apiResponse('1','修改绑定手机号成功');
        }else{
            apiResponse('0','修改绑定手机号失败');
        }

    }

    /**
     * 修改密码第一步
     */
    public function modPasswordOne(){
        $this->display('modPasswordOne');
    }

    public function ajaxModPasswordOne(){
        $m_id = $_POST['m_id'];
        if(empty($m_id)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $account = $_POST['account'];
        $verify  = $_POST['verify'];

        //检查验证码是否正确
        $this->checkVerify($account,$verify,'modify_pwd');

        apiResponse('1','验证通过');
    }

    /**
     * 修改密码第二步
     */
    public function modPasswordTwo(){
        $this->display('modPasswordTwo');
    }

    public function ajaxModPasswordTwo(){
        $m_id = $_POST['m_id'];
        if(empty($m_id)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $password = md5($_POST['password']);
        $res = M('Member')->where(array('id'=>$m_id))->data(array('password'=>$password,'update_time'=>time()));
        if($res){
            apiResponse('1','修改密码成功');
        }else{
            apiResponse('0','修改密码失败');
        }
    }
}