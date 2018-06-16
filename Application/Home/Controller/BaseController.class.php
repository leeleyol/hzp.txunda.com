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
        $num = M('Attention')->where(['m_id'=>$m_id])->count('id');
        return $num?$num:"0";
    }

    /**
     * 获取收藏的数量
     * @param $m_id
     * @return string
     */
    public function getCollectNum($m_id){
        $num = M('Collect')->where(['m_id'=>$m_id])->count('id');
        return $num?$num:"0";
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


    /**
     * 增加账单明细
     */
    public function addPayLog($m_id,$type,$symbol,$money,$desc){
        $l_data['m_id'] = $m_id;
        $l_data['type'] = $type;
        $l_data['symbol'] = $symbol;
        $l_data['money'] = $money;
        $l_data['desc'] = $desc;
        $l_data['create_time'] = time();
        M('PayLog')->data($l_data)->add();
    }
}