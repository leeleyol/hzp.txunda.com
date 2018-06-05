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
     * 初始化方法
     */
    public $member_obj = '';
    public $sms_obj    = '';
    //public $msg_obj    = '';
    public $file_obj   = '';
    public $bank_icon_obj = '';
    public $member_bank_obj = '';
    public function _initialize()
    {
        parent::_initialize();
        $this->member_obj = D('Member');
        $this->sms_obj    = D('Sms');
        //$this->msg_obj    = D('Msg');
        $this->file_obj   = D('File');
        $this->bank_icon_obj = M('BankIcon');
        $this->member_bank_obj = D('MemberBank');
    }

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



    /**
     * 支持的银行卡列表
     */
    public function supportBankList(){
        $list = $this->bank_icon_obj->where(1)->field('id as bank_icon_id,bank_name,bank_icon')->select();
        foreach($list as $k => $v){
            $list[$k]['bank_icon'] = C('API_URL').'/Uploads/BankIcon/'.$v['bank_icon'];
        }
        apiResponse('1','请求成功',$list);
    }


    /**
     * 绑定银行卡
     * bank_real_name,bank_number bank_icon_id bank_mobile,verify
     */
    public function bindBank(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['bank_real_name'],'condition'=>'','error_msg'=>'请输入真实姓名'),
            array('check_type'=>'is_null','parameter' => $request['bank_number'],'condition'=>'','error_msg'=>'请输入您的银行卡卡号'),
            array('check_type'=>'is_null','parameter' => $request['bank_icon_id'],'condition'=>'','error_msg'=>'请选择开户行'),
            array('check_type'=>'is_null','parameter' => $request['bank_mobile'],'condition'=>'','error_msg'=>'请输入手机号码'),
            array('check_type'=>'is_null','parameter' => $request['id_card'],'condition'=>'','error_msg'=>'请输入身份证号'),
        );
        check_param($param);//检查参数

        $request['m_id'] = $m_id;
        $res = $this->member_bank_obj->addRow($request);
        if($res){
            apiResponse('1','绑定银行卡成功');
        }else{
            apiResponse('0','绑定银行卡失败');
        }

    }

    /**
     * 银行卡列表
     */
    public function memberBankList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $param['where']['m_id'] = $m_id;
        $param['where']['status'] = array('neq',9);
        $param['field'] = 'id as m_bank_id,bank_icon_id,bank_real_name,bank_number,bank_mobile';
        $param['order'] = 'create_time desc';
        $m_bank_list = $this->member_bank_obj->rowList($param);
        if(!$m_bank_list){
            apiResponse('0','暂无绑定银行卡');
        }
        foreach($m_bank_list as $k => $v){
            $bank_icon_info = $this->bank_icon_obj->where(array('id'=>$v['bank_icon_id']))->find();
            $m_bank_list[$k]['bank_name'] = $bank_icon_info['bank_name'];
            $m_bank_list[$k]['bank_icon'] = C('API_URL').'/Uploads/BankIcon/'.$bank_icon_info['bank_icon'];
        }
        apiResponse('1','请求成功',$m_bank_list);
    }

    /**
     * 解绑银行卡
     * 参数：银行卡id m_bank_id
     */
    public function delBank(){
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['m_bank_id'],'condition'=>'','error_msg'=>'参数不完整'),
        );
        check_param($param);//检查参数
        $where['id'] = $request['m_bank_id'];
        $data['status'] = 9;
        $res = $this->member_bank_obj->updateRow($where,$data);
        if($res){
            apiResponse('1','解绑银行卡成功');
        }else{
            apiResponse('0','解绑银行卡失败');
        }
    }


    /**
     * 余额提现
     * 参数：money(金额)  m_bank_id（银行卡id）
     */
    public function balanceWithdraw(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['money'],'condition'=>'','error_msg'=>'请输入提现金额'),
            array('check_type'=>'is_null','parameter' => $request['m_bank_id'],'condition'=>'','error_msg'=>'请选择银行卡'),
        );
        check_param($param);//检查参数
        if($request['money']<$this->config['balance_withdraw_limit']){
            apiResponse('0','单次最少提现'.$this->config['balance_withdraw_limit'].'元');
        }
        $member_info = M('Member')->where(array('id'=>$m_id))->find();
        if($member_info['balance']<$request['money']){
            apiResponse('0','余额不足');
        }
        $m_bank_info = M('MemberBank')->where(array('id'=>$request['m_bank_id']))->find();
        $data['m_id'] = $m_id;
        $data['bank_name'] = M('bank_icon')->where(array('id'=>$m_bank_info['bank_icon_id']))->getField('bank_name');
        $data['bank_real_name'] = $m_bank_info['bank_real_name'];
        $data['bank_number'] = $m_bank_info['bank_number'];
        $data['bank_mobile'] = $m_bank_info['bank_mobile'];
        $data['money'] = $request['money'];
        $data['create_time'] = time();
        $data['bank_id'] = $request['m_bank_id'];
        $res = M('balance_withdraw')->data($data)->add();
        if(!$res){
            apiResponse('0','提现失败');
        }
        M('Member')->where(array('id'=>$m_id))->setDec('balance',$request['money']);
        $this->addPayLog($m_id,3,1,$request['money'],'余额提现');
        apiResponse('1','提现成功');
    }

    /**
     * 账单明细
     * type:1支出明细 2收入明细 3提现明细
     * p:分页参数
     */
    public function getPayLog(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $list = M('PayLog')->where($where)
            ->field('symbol,money,desc,create_time')
            ->order('create_time desc')
            ->page($request['p'].',10')
            ->select();
        if(empty($list)){
            $message =$request['p']==1?'暂无记录':'无更多记录';
            apiResponse('0',$message);
        }
        apiResponse('1','请求成功',$list);
    }
}