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
            ->field('id as m_id,type,account,head_pic,nickname,intro,balance,vip_end_time,refresh_num')
            ->find();
        if(empty($member_info)){
            apiResponse('-1','登录失效，请重新登录');
        }
        $member_info['follow_num'] = $this->getFollowNum($m_id);
        $member_info['collect_num']   = $this->getCollectNum($m_id);
        $member_info['head_pic_path'] = returnImage($member_info['head_pic']);
        $member_status = M('MemberInfo')->where(['m_id'=>$m_id])->field('merchant_approve,refuse_content')->find();
        $member_info['merchant_approve'] = $member_status?$member_status['merchant_approve']:"0";
        $member_info['refuse_content'] = $member_status?$member_status['refuse_content']:"";

        $member_info['no_read_msg'] = D('Msg')->isHaveMsg($m_id);
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



    /*
     * 申请成为商家
     * */
    public function merchantApply(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['realname'],'condition'=>'','error_msg'=>'真实姓名参数错误'),
            array('check_type'=>'is_null','parameter' => $request['phone'],'condition'=>'','error_msg'=>'电话参数错误'),
            array('check_type'=>'is_null','parameter' => $request['id_card_number'],'condition'=>'','error_msg'=>'身份证号参数错误'),
            array('check_type'=>'is_null','parameter' => $request['company_address'],'condition'=>'','error_msg'=>'公司地址参数错误'),
            array('check_type'=>'is_null','parameter' => $request['business_license'],'condition'=>'','error_msg'=>'营业执照参数错误'),
            array('check_type'=>'is_null','parameter' => $request['front_id_card'],'condition'=>'','error_msg'=>'身份证正面参数错误'),
            array('check_type'=>'is_null','parameter' => $request['back_id_card'],'condition'=>'','error_msg'=>'身份证背面参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'realname'=>$request['realname'],
            'id_card_number'=>$request['id_card_number'],
            'phone'=>$request['phone'],
            'company_address'=>$request['company_address'],
            'business_license'=>$request['business_license'],
            'front_id_card'=>$request['front_id_card'],
            'back_id_card'=>$request['back_id_card'],
            'merchant_approve'=>1
        ];
        $res = M('MemberInfo')->data($data)->add();
        if($res){
            apiResponse('1','提交成功');
        }else{
            apiResponse('0','提交失败');
        }

    }



    /*
     * 上传文件
     * type 1用户
     * */
    public function uploadImage(){
        if($_POST['type'] == 1){
            $savePath = 'Member';
        }else if($_POST['type'] == 2){
            $savePath = 'Post';
        }else if($_POST['type'] == 3){
            $savePath = 'Goods';
        }else if($_POST['type'] == 4){
            $savePath = 'Supply';
        }else if($_POST['type'] == 5){
            $savePath = 'Advert';
        }
        $result_data = array();
        if($_FILES['picture']['name']){
            $res = api('UploadPic/upload', array(array('save_path' => $savePath)));
                if(is_array($res)){
                    foreach($res as $k =>$v){
                        $result_data[$k]['picture_id'] = $v['id'];
                        $result_data[$k]['picture_path'] = C('API_URL').$v['path'];
                        $result_data[$k]['ext'] = $v['ext'];
                    }
                    apiResponse('200','',$result_data);
                }else{
                    apiResponse('110',$res);
                }
        }
    }



    /*
     * 商家列表
     * */
    public function memberList(){
        $m_id = $this->member_obj->checkToken();
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'参数错误'),
        );
        $where['status'] = 1;
        $where['type'] = $request['type'] ?$request['type'] :0;
        check_param($param);//检查参数
        $list = M('Member')->where($where)
            ->field('id as member_id,nickname,head_pic,attention_num,type')
            ->order('create_time desc')
            ->page($request['p'].',10')
            ->select();
        if(empty($list)){
            $message =$request['p']==1?'暂无记录':'无更多记录';
            apiResponse('0',$message);
        }
        foreach ($list as $k=>$v){
            $list[$k]['head_pic_path'] = returnImage($v['head_pic']);
            $list[$k]['goods_num'] = getMemberGoodsNum($v['member_id']);
            $list[$k]['need_num'] = getMemberSupplyNum($v['member_id']);
        }
        apiResponse('1','请求成功',$list);
    }


    /*
     * 商家详情
     * */
    public function memberInfo(){
        $m_id = $this->member_obj->checkToken();
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['member_id'],'condition'=>'','error_msg'=>'商家id参数错误'),
        );
        $where['status'] = 1;
        $where['id'] = $request['member_id'];
        check_param($param);//检查参数
        $info = M('Member')->where($where)->field('id as m_id,nickname,head_pic,attention_num,intro,type')->find();
        $info['head_pic_path'] = returnImage($info['head_pic']);
        $info['goods_num'] = getMemberGoodsNum($info['m_id']);
        $info['need_num'] = getMemberSupplyNum($info['m_id']);
        $info['no_read_msg'] = D('Msg')->isHaveMsg($info['m_id']);
        if($m_id){
            $info['is_attention'] = M('Attention')->where(['object_id'=>$_POST['member_id'],'m_id'=>$m_id])->find()?1:0;
        }else{
            $info['is_attention'] = 0;
        }
        apiResponse('1','请求成功',$info);
    }



    /**
     * 修改个人资料
     * head_pic nickname  head_pic_id
     */
    public function modBaseData(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        if($request['head_pic']){
            $data['head_pic'] = $request['head_pic'];
        }
        if($request['intro']){
            $data['intro'] = $request['intro'];
        }
        if($request['nickname']){
            $data['nickname'] = $request['nickname'];
        }
        $where['id'] = $m_id;
        $res = $this->member_obj->updateRow($where,$data);
        if($res){
            apiResponse('1','修改个人资料成功');
        }else{
            apiResponse('0','修改个人资料失败');
        }
    }



    /*
     * 我的进货记录
     *
     * */
    public function myBuy(){
        $m_id = $this->member_obj->checkToken();
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'参数错误'),
        );
        $where['b.m_id'] = $m_id;
        $where['b.status'] = 1;
        check_param($param);//检查参数
        $list = M('Buy')->alias('b')
            ->join('db_member m on m.id=b.from_mid')
            ->where($where)
            ->field('b.*,m.nickname,m.head_pic,m.type member_type')
            ->order('create_time desc')
            ->page($request['p'].',10')
            ->select();
        if(empty($list)){
            $message =$request['p']==1?'暂无记录':'无更多记录';
            apiResponse('0',$message);
        }
        foreach ($list as $k=>$v){
            $list[$k]['head_pic_path'] = returnImage($v['head_pic']);
            $info = json_decode($v['buy_info'],true);
            $list[$k]['goods_num'] = $info?count($info):"0";
        }
        apiResponse('1','请求成功',$list);
    }


    /*
     * 报价详情
     *
     * */
    public function buyInfo(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['buy_id'],'condition'=>'','error_msg'=>'报价id参数错误'),
        );
        check_param($param);//检查参数
        $buy_info = M('Buy')->where(['id'=>$request['buy_id']])->find();
        $buy_info_after = json_decode($buy_info['buy_info'],true);
        $index = [];
        foreach ($buy_info_after as $k=>$v){
            $goods_info = M('Goods')->alias('g')->join('db_goods_type gt on gt.id=g.goods_type_id','left')->where(['g.id'=>$v['goods_id']])->field('g.goods_name,g.goods_type_id,g.stock,g.stock_unit,g.goods_status,gt.type_name,g.goods_pic')->find();
            $index[$k]['goods_id'] = $v['goods_id'];
            $index[$k]['goods_type_name'] = $goods_info['type_name'];
            $index[$k]['goods_type_id'] = $goods_info['goods_type_id'];
            $index[$k]['goods_pic_path'] = returnImage($goods_info['goods_pic']);
            $index[$k]['goods_status'] = $goods_info['goods_status'];
            $index[$k]['buy_price'] = $v['buy_price'];
            $index[$k]['number'] = $v['number'];
        }
        $buy_info['buy_info'] = $index;
        $result['buy'] = $buy_info;
        $result['from_member'] = getMemberInfo($buy_info['from_mid']);
        apiResponse('1','成功',$result);
    }





    /**
     * 新增充值订单
     */
    public function addRechargeOrder(){
        /*if(!session('openid')){
            apiResponse('error','请前往首页获取授权code');
        }

        if(!session('m_id')){
            apiResponse('error','登录已过期，请重新登录');
        }*/

        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        //将用户ID  订单编号  转入金额  转账形式  创建时间新增到Recharge表

        $type = $_POST['type'] ? $_POST['type']:1;
        $data['type'] = $type;
        $data['m_id']        = $_POST['m_id'];
        $data['order_sn']    = date('YmdHi').rand(100,999);
        $data['money']       = $_POST['money'];
        $data['month'] = $_POST['month'];
        $data['create_time'] = time();
        $data['supply_id'] = $_POST['id'] ? $_POST['id']:0;
        $result = M('Recharge') -> add($data);
        //查询新增状态
        if($result){
            Vendor('WxpayApi.lib.WxPay#Api');
            Vendor('WxpayApi.WxPay#JsApiPay');

            //①、获取用户openid
            $tools = new \JsApiPay();
//            $openId = $tools->GetOpenidFromMp(session('wx_code'));
            $openId = session('openid');

            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody("美妆支付");
            $input->SetAttach("美妆支付");
            $input->SetOut_trade_no($data['order_sn']);
            $input->SetTotal_fee($_POST['money']*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://hzp.txunda.com/index.php/Home/Member/weiXinNotify?type=".$type);
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = \WxPayApi::unifiedOrder($input);
            /*var_dump($input);
            var_dump($order);*/
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $jsApiParameters = stripslashes($jsApiParameters);
            apiResponse('success','充值下单成功',array('jsApiParameters'=>$jsApiParameters));
        }else{
            apiResponse('error','充值下单失败');
        }
    }

    /**
     * 充值下单-微信回调
     */
    public function weiXinNotify(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml_res = $this->xmlToArray($xml);
        $where['order_sn'] =$xml_res["out_trade_no"];
        $where['status'] = array('eq',0);
        $recharge_info =M('Recharge')->where($where)->find();
        if($recharge_info){
            //修改充值订单状态
            unset($where);
            $where['id'] = $recharge_info['id'];
            $data['update_time'] = time();
            $data['status']      = 1;
            M('Recharge')->where($where)->data($data)->save();
            unset($data);
            $data['type'] = 1;
            $data['m_id']   = $recharge_info['m_id'];
            $data['title']       = '微信';
            $data['symbol']      = 0;
            $data['money']       = $recharge_info['money'];
            $data['create_time'] = time();
            if($recharge_info['type'] ==1){
                //添加用户过期时间
                unset($where);
                $where['id'] = $recharge_info['m_id'];
                $member_info = M('Member')->where($where)->field('vip_end_time')->find();
                if($member_info['vip_end_time'] > time()){
                    M('Member')->where($where)->data(['type'=>'2','vip_end_time'=>$member_info['vip_end_time']+$recharge_info['month']*30*86400])->save();
                }else{
                    M('Member')->where($where)->data(['type'=>'2','vip_end_time'=>time()+$recharge_info['month']*30*86400])->save();
                }
                $data['content']     = '充值会员';
            }elseif($recharge_info['type'] ==2){
                M('Supply')->where(['id'=>$recharge_info['supply_id']])->data(['create_time'=>time()])->save();
                $data['content']     = '充值刷新次数';
            }
            //添加账单明细
            M('PayLog')->data($data)->add();

        }
    }
    public function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
}