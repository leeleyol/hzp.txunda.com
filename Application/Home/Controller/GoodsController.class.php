<?php
namespace Home\Controller;
/**
 * Class MemberLogLogic
 * @package Api\Logic
 * 商品逻辑层
 */
class GoodsController extends BaseController{

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
     * 分类列表
     * 传递参数的方式：post
     * 需要传递的参数：
     * m_id 用户id
     */
    public function goodsTypeList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $list = M('GoodsType')->where(['status'=>1,'m_id'=>$m_id])->order('create_time desc')->select();
        apiResponse('1','请求成功',$list?$list:[]);
    }


    /**
     * 添加分类
     * 传递参数的方式：post
     * 需要传递的参数：
     * m_id 用户id
     * type_name 分类名称
     */
    public function addGoodsType(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['type_name'],'condition'=>'','error_msg'=>'分类名称参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        if(M('GoodsType')->where(['type_name'=>$request['type_name'],'status'=>1,'m_id'=>$m_id])->getField('id')){
            apiResponse('110','你已添加相同的分类');
        }
        $data= [
            'm_id'=>$m_id,
            'type_name'=>$request['type_name'],
            'status'=>1,
            'create_time'=>time()
        ];
        $res = M('Post')->data($data)->add();
        if($res){
            apiResponse('1','添加成功');
        }else{
            apiResponse('0','添加失败');
        }
    }




    /**
     * 用户发帖
     * 传递参数的方式：post
     * 需要传递的参数：
     */
    public function addGoods(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['goods_type_id'],'condition'=>'','error_msg'=>'分类id参数错误'),
            array('check_type'=>'is_null','parameter' => $request['goods_name'],'condition'=>'','error_msg'=>'商品名称参数错误'),
            array('check_type'=>'is_null','parameter' => $request['bar_code'],'condition'=>'','error_msg'=>'条形码参数错误'),
            array('check_type'=>'is_null','parameter' => $request['product_from'],'condition'=>'','error_msg'=>'产地参数错误'),
            array('check_type'=>'is_null','parameter' => $request['goods_status'],'condition'=>'','error_msg'=>'货物状态参数错误'),
            array('check_type'=>'is_null','parameter' => $request['stock'],'condition'=>'','error_msg'=>'库存数量参数错误'),
            array('check_type'=>'is_null','parameter' => $request['stock_unit'],'condition'=>'','error_msg'=>'库存单位参数错误'),
            array('check_type'=>'is_null','parameter' => $request['pic'],'condition'=>'','error_msg'=>'图片参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'goods_name'=>$request['goods_name'],
            'goods_type_id'=>$request['goods_type_id'],
            'pic'=>$request['pic'] ? $request['pic'] : '',
            'create_time'=>time(),
            'bar_code'=>$request['bar_code'],
            'product_from'=>$request['product_from'],
            'goods_status'=>$request['goods_status'],
            'stock'=>$request['stock'],
            'stock_unit'=>$request['stock_unit'],
        ];
        $res = M('Goods')->data($data)->add();
        if($res){
            apiResponse('1','发布成功');
        }else{
            apiResponse('0','发布失败');
        }
    }



    /*
     * 获取个人商品
     *
     * */
    public function memberGoodsList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        
    }
}