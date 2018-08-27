<?php
namespace Home\Controller;
/**
 * Class MemberLogLogic
 * @package Api\Logic
 * 聊天逻辑层
 */
class ChatController extends BaseController{

    /**
     * 初始化方法
     */
    public $member_obj = '';
    public $sms_obj    = '';
    //public $msg_obj    = '';
    public $file_obj   = '';
    public function _initialize()
    {
        parent::_initialize();
        $this->member_obj = D('Member');
        $this->sms_obj    = D('Sms');
        //$this->msg_obj    = D('Msg');
        $this->file_obj   = D('File');
    }










    /**
     * 用户发帖
     * 传递参数的方式：post
     * 需要传递的参数：
     * 用户id m_id
     * type_id 分类id
     * content 内容
     */
    public function addChat(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['type'],'condition'=>'','error_msg'=>'类型参数错误'),
            array('check_type'=>'is_null','parameter' => $request['content'],'condition'=>'','error_msg'=>'内容参数错误'),
            array('check_type'=>'is_null','parameter' => $request['from_mid'],'condition'=>'','error_msg'=>'对话用户id参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'type'=>$request['type'],
            'content'=>$request['type']==1?$_POST['content']:'报价单',
            'create_time'=>time(),
            'from_mid'=>$request['from_mid']
        ];
        $res = M('Chat')->data($data)->add();
        if($res){
            if($request['type']==2){
                $data1 = [
                    'm_id'=>$m_id,
                    'type'=>$request['type'],
                    'buy_info'=>$_POST['content'],
                    'create_time'=>time(),
                    'supply_id'=>$request['supply_id'] ? $request['supply_id'] :0,
                    'from_mid'=>$request['from_mid'],
                ];
                $res1 = M('Buy')->data($data1)->add();
                M('Chat')->where(['id'=>$res])->data(['buy_id'=>$res1])->save();
            }
            apiResponse('1','发布成功');
        }else{
            apiResponse('0','发布失败');
        }
    }




    /*
     * 报价详情
     * @param array $request
     * 传递参数的方式：post
     * 需要传递的参数：
     * m_id 用户id
     * */
    public function chatInfo(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        empty($_POST['chat_id'])&&apiResponse('0','聊天id不能为空');
        $where['id'] = $_POST['chat_id'];
        $info = M('Chat')->where($where)->find();
        $buy_info = [];
        if($info['type']==2){
            $buy_info = M('Buy')->where(['id'=>$info['buy_id']])->field('buy_info,status')->find();
            $buy_info_after = json_decode($buy_info['buy_info'],true);
            $index = [];
            foreach ($buy_info_after as $k=>$v){
                $goods_info = M('Goods')->alias('g')->join('db_goods_type gt on gt.id=g.goods_type_id','left')->where(['g.id'=>$v['goods_id']])->field('g.goods_name,g.goods_type_id,g.stock,g.stock_unit,g.goods_status,gt.type_name,g.goods_pic')->find();
                $index[$k]['goods_id'] = $v['goods_id'];
                $index[$k]['goods_type_name'] = $goods_info['type_name'];
                $index[$k]['goods_type_id'] = $goods_info['goods_type_id'];
                $index[$k]['goods_name'] = $goods_info['goods_name'];
                $index[$k]['stock'] = $goods_info['stock'];
                $index[$k]['stock_unit'] = $goods_info['stock_unit'];
                $index[$k]['goods_pic_path'] = returnImage($goods_info['goods_pic']);
                $index[$k]['goods_status'] = $goods_info['goods_status'];
                $index[$k]['buy_price'] = $v['buy_price'];
                $index[$k]['number'] = $v['number'];
            }
            $info['content'] = $index;
        }
        $info['buy_status'] = $buy_info?$buy_info['status']:"0";
        apiResponse('1','成功',$info);

    }


    /*
     * 查看聊天记录
     * */
    public function getChatList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
            array('check_type'=>'is_null','parameter' => $request['from_mid'],'condition'=>'','error_msg'=>'聊天对象id参数错误'),
        );
        check_param($param);//检查参数
        $result['self'] = getMemberInfo($m_id);
        $result['from_member'] = getMemberInfo($request['from_mid']);
        $where['_string'] = '(m_id = '.$m_id.' AND from_mid='.$request['from_mid'].') OR ( m_id = '.$request['from_mid'].' AND from_mid = '.$m_id.')';
        $list = M('Chat')->where($where)->page($request['p'].',15')->order('create_time desc')->select();
        foreach ($list as $k=>$v){
            if($v['buy_id']){
                $list[$k]['buy_status'] = M('Buy')->where(['id'=>$v['buy_id']])->getField('status');
            }else{
                $list[$k]['buy_status'] = "0";
            }
        }
        $result['list'] = $list;
        apiResponse('1','成功',$result);
    }



    /*
     *
     * 更新报价单
     * */
    public function checkBuy(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['chat_id'],'condition'=>'','error_msg'=>'留言id参数错误'),
            array('check_type'=>'is_null','parameter' => $request['status'],'condition'=>'','error_msg'=>'审核状态参数错误'),
        );
        check_param($param);//检查参数
        $where['id'] = $_POST['chat_id'];
        $info = M('Chat')->where($where)->find();
        $chat_data = [
            'm_id'=>$m_id,
            'from_mid'=>$info['m_id'],
            'content'=>$request['status']==1?"已同意报价":"已拒绝报价",
            'create_time'=>time(),
            'remark'=>$request['status']==1?"已同意报价":"已拒绝报价",
            'buy_id'=>$info['buy_id'],
            'type'=>2
        ];
        $res = M('Chat')->data($chat_data)->add();
        if($res){
            $member_info = M('MemberInfo')->where(['id'=>$m_id])->field('company_address,phone')->find();
            if($request['status']==1){
                $save_data = ['address'=>$member_info['address'],'mobile'=>$member_info['phone']];
            }
            $save_data['status'] = $request['status'];
            $save_data['update_time'] = time();
            M('Buy')->where(['id'=>$info['buy_id']])->data($save_data)->save();
            apiResponse('1','审核成功');
        }else{
            apiResponse('0','审核失败');
        }
    }




    /*
     * 商家更新报价单
     *
     * */
    public function merchantUpdate(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['content'],'condition'=>'','error_msg'=>'内容参数错误'),
            array('check_type'=>'is_null','parameter' => $request['chat_id'],'condition'=>'','error_msg'=>'留言id参数错误'),
            array('check_type'=>'is_null','parameter' => $request['status'],'condition'=>'','error_msg'=>'审核状态参数错误'),
        );
        check_param($param);//检查参数
        $where['id'] = $_POST['chat_id'];
        $info = M('Chat')->where($where)->find();
        $data = [
            'm_id'=>$m_id,
            'type'=>2,
            'content'=>'更新报价单',
            'create_time'=>time(),
            'from_mid'=>$request['m_id'],
            'buy_id'=>$info['buy_id']
        ];
        $res = M('Chat')->data($data)->add();
        if($res){
                $data1 = [
                    'm_id'=>$m_id,
                    'type'=>2,
                    'buy_info'=>$_POST['content'],
                    'create_time'=>time(),
                    'supply_id'=>$info['supply_id'] ,
                    'from_mid'=>$request['m_id'],
                    'status'=>3
                ];
                $res1 = M('Buy')->where(['id'=>$info['buy_id']])->data($data1)->save();
            apiResponse('1','发布成功');
        }else{
            apiResponse('0','发布失败');
        }
    }

    



}