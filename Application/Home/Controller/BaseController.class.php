<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller{
    /**
     * 初始化
     */
    public function _initialize(){

    }

    /**
     * 获取关注的数量
     * @param $m_id
     * @return string
     */
    public function getFollowNum($m_id){
        return '0';
    }

    /**
     * 获取收藏的数量
     * @param $m_id
     * @return string
     */
    public function getCollectNum($m_id){
        return '0';
    }

    /**
     * 检查验证码是否正确
     * @param $account
     * @param $verify
     * @param $send_type
     */
    public  function  checkVerify($account,$verify,$send_type){
        $res = D('Sms')->checkVerify($account,$verify,$send_type);
        if($res['error']){
            apiResponse('0',$res['error']);
        }
    }
}