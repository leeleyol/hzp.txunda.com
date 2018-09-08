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
        $list = M('GoodsType')->where(['status'=>1,'m_id'=>$m_id])->field('id,type_name')->order('create_time desc')->select();
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
        $res = M('GoodsType')->data($data)->add();
        if($res){
            apiResponse('1','添加成功');
        }else{
            apiResponse('0','添加失败');
        }
    }

    /**
     * 删除分类
     * 传递参数的方式：post
     * 需要传递的参数：
     * m_id 用户id
     */
    public function delGoodsType(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['id'],'condition'=>'','error_msg'=>'分类id参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数

        $res = M('GoodsType')->where(['id'=>$request['id']])->data(['status'=>9])->save();
        if($res){
            apiResponse('1','删除成功');
        }else{
            apiResponse('0','删除失败');
        }
    }




    /**
     * 用户发布商品
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
            array('check_type'=>'is_null','parameter' => $request['goods_pic'],'condition'=>'','error_msg'=>'图片参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'goods_name'=>$request['goods_name'],
            'goods_type_id'=>$request['goods_type_id'],
            'goods_pic'=>$request['goods_pic'] ? $request['goods_pic'] : '',
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


    /**
     * 用户修改商品
     * 传递参数的方式：post
     * 需要传递的参数：
     */
    public function updateGoods(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['goods_id'],'condition'=>'','error_msg'=>'id参数错误'),
            array('check_type'=>'is_null','parameter' => $request['goods_type_id'],'condition'=>'','error_msg'=>'分类id参数错误'),
            array('check_type'=>'is_null','parameter' => $request['goods_name'],'condition'=>'','error_msg'=>'商品名称参数错误'),
            array('check_type'=>'is_null','parameter' => $request['bar_code'],'condition'=>'','error_msg'=>'条形码参数错误'),
            array('check_type'=>'is_null','parameter' => $request['product_from'],'condition'=>'','error_msg'=>'产地参数错误'),
            array('check_type'=>'is_null','parameter' => $request['goods_status'],'condition'=>'','error_msg'=>'货物状态参数错误'),
            array('check_type'=>'is_null','parameter' => $request['stock'],'condition'=>'','error_msg'=>'库存数量参数错误'),
            array('check_type'=>'is_null','parameter' => $request['stock_unit'],'condition'=>'','error_msg'=>'库存单位参数错误'),
            array('check_type'=>'is_null','parameter' => $request['goods_pic'],'condition'=>'','error_msg'=>'图片参数错误'),
        );
        $where['m_id'] = $m_id;
        $where['id'] = $request['goods_id'];
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'goods_name'=>$request['goods_name'],
            'goods_type_id'=>$request['goods_type_id'],
            'goods_pic'=>$request['goods_pic'] ? $request['goods_pic'] : '',
            'create_time'=>time(),
            'bar_code'=>$request['bar_code'],
            'product_from'=>$request['product_from'],
            'goods_status'=>$request['goods_status'],
            'stock'=>$request['stock'],
            'stock_unit'=>$request['stock_unit'],
        ];
        $res = M('Goods')->where($where)->data($data)->save();
        if($res){
            apiResponse('1','修改成功');
        }else{
            apiResponse('0','修改失败');
        }
    }


    public function deleteGoods(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['goods_id'],'condition'=>'','error_msg'=>'id参数错误'),
        );
        check_param($param);//检查参数
        $where['m_id'] = $m_id;
        $where['id'] = $request['goods_id'];
        $res = M('Goods')->where($where)->data(['status'=>9,'update_time'=>time()])->save();
        if($res){
            apiResponse('1','删除成功');
        }else{
            apiResponse('0','删除失败');
        }
    }





    /*
     * 获取个人商品
     *
     * */
    public function memberGoodsList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $list = M('GoodsType')->where(['status'=>1,'m_id'=>$_POST['member_id']])->order('create_time desc')->field('id,type_name')->select();
        foreach ($list as $k=>$v){
            $goods_list = M('Goods')->where(['goods_type_id'=>$v['id'],'status'=>1])->field('id goods_id,goods_name,stock,stock_unit,goods_status,goods_pic,product_from,bar_code')->select();
            foreach ($goods_list as $k1=>$v1){
                $goods_list[$k1]['goods_pic_path'] = returnImage($v1['goods_pic']);
            }
            $list[$k]['goods_list'] = $goods_list;
        }
        apiResponse('1','成功',$list);
        
    }


    public function addGoodsImage(){
        $resourceUrl = '';
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $_POST['image'], $result)){
            $type = $result[2];
            $new_file = 'Uploads/Goods/'.date('Y').'-'.date('m').'-'.date('d').'/';
            if ( !file_exists ( $new_file )) {
                mkdir ( "$new_file", 0777, true );
            }
            $name = uniqid();
            $new_file = $new_file.$name.'.'.$type;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $_POST['image'])))){
                $resourceUrl .= $new_file;
            }
        }
        $data['path'] = '/'.$resourceUrl;
        $data['ext'] = $type;
        $data['savename'] = $name.'.'.$type;
        $data['create_time'] = time();
        $id = M('File')->add($data);
        apiResponse('1','成功',['id'=>$id,'ext'=>$type,'picture_path'=>C('API_URL').$data['path']]);
    }
}