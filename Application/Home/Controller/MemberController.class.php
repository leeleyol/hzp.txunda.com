<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 用户
 * Class MemberController
 * @package Home\Controller
 */
class MemberController extends BaseController{

    /**
     * 我的
     */
    public function memberCenter(){
        $this->display('memberCenter');
    }

    /**
     * ajax获取个人中心数据
     */
    public function ajaxMemberCenter(){
        $m_id = $_POST['m_id'];
        if(empty($m_id)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $member_info = M('Member')
            ->where(array('id'=>$m_id,'status'=>array('neq',9)))
            ->field('id as m_id,type,account,head_pic,nickname,intro,balance')
            ->find();
        if(empty($member_info)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $member_info['follow_num'] = $this->getFollowNum($m_id);
        $member_info['collect_num']   = $this->getCollectNum($m_id);
        $member_info['head_pic'] = D('File')->getOnePath($member_info['head_pic'],C('API_URL').'/Uploads/Member/default.png');
        apiResponse('1','请求成功',$member_info);

    }

    /**
     * 用户基本资料
     */
    public function memberBaseData(){
        $this->display('memberBaseData');
    }


    /**
     * 我的钱包首页
     */
    public function myWalletIndex(){
        $this->display('myWalletIndex');
    }

    /**
     * 我的银行卡
     */
    public function bankList(){
        $this->display('bankList');
    }

    /**
     * 添加银行卡
     */
    public function addBank(){
        $this->display('addBank');
    }

    /**
     * 支持的银行
     */
    public function supportBank(){
        $this->display('supportBank');
    }

    /**
     * 提现
     */
    public function withdraw(){
        $this->display('withdraw');
    }

    /**
     * 账单明细
     */
    public function payLog(){
        $this->display('payLog');
    }
}