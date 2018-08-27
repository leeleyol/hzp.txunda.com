<?php
namespace Home\Controller;
/**
 * Class MemberLogLogic
 * @package Api\Logic
 * 供求逻辑层
 */
class SupplyController extends BaseController{

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
     * 添加供求
     * 传递参数的方式：post
     * 需要传递的参数：
     */
    public function addSupply(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['type'],'condition'=>'','error_msg'=>'分类标识参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_extract'],'condition'=>'','error_msg'=>'买家自提参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_md'],'condition'=>'','error_msg'=>'商家配送参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_sf'],'condition'=>'','error_msg'=>'顺丰信息参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_bp'],'condition'=>'','error_msg'=>'买家支付参数错误'),
            array('check_type'=>'is_null','parameter' => $request['description'],'condition'=>'','error_msg'=>'描述参数错误'),
            array('check_type'=>'is_null','parameter' => $request['area_name'],'condition'=>'','error_msg'=>'地区信息参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'type'=>$request['type'],
            'is_extract'=>$request['is_extract'],
            'is_md'=>$request['is_md'],
            'create_time'=>time(),
            'is_sf'=>$request['is_sf'],
            'is_bp'=>$request['is_bp'],
            'description'=>$request['description'],
            'area_name'=>$request['area_name'],
            'pic'=>$request['pic'] ? $request['pic'] :'',
            'supply_info'=>$_POST['supply_info'] ? $_POST['supply_info'] :'',
            'is_hidename'=>$request['is_hidename'] ? $request['is_hidename'] :'',
        ];
        $res = D('Supply')->addRow($data);
        if($res){
            apiResponse('1','发布成功');
        }else{
            apiResponse('0','发布失败');
        }
    }




    /**
     * 用户修改供求
     * 传递参数的方式：post
     * 需要传递的参数：
     */
    public function updateSupply(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['type'],'condition'=>'','error_msg'=>'分类标识参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_extract'],'condition'=>'','error_msg'=>'买家自提参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_md'],'condition'=>'','error_msg'=>'商家配送参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_sf'],'condition'=>'','error_msg'=>'顺丰信息参数错误'),
            array('check_type'=>'is_null','parameter' => $request['is_bp'],'condition'=>'','error_msg'=>'买家支付参数错误'),
            array('check_type'=>'is_null','parameter' => $request['description'],'condition'=>'','error_msg'=>'描述参数错误'),
            array('check_type'=>'is_null','parameter' => $request['area_name'],'condition'=>'','error_msg'=>'地区信息参数错误'),
            array('check_type'=>'is_null','parameter' => $request['id'],'condition'=>'','error_msg'=>'供求id参数错误'),
        );
        $where['m_id'] = $m_id;
        $where['id'] = $request['id'];
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'type'=>$request['type'],
            'is_extract'=>$request['is_extract'],
            'is_md'=>$request['is_md'],
            'is_sf'=>$request['is_sf'],
            'is_bp'=>$request['is_bp'],
            'description'=>$request['description'],
            'area_name'=>$request['area_name'],
            'pic'=>$request['pic'] ? $request['pic'] :'',
            'supply_info'=>$_POST['supply_info'] ? $_POST['supply_info'] :'',
            'is_hidename'=>$request['is_hidename'] ? $request['is_hidename'] :'',
        ];
        $res = D('Supply')->updateRow($where,$data);
        if($res){
            apiResponse('1','修改成功');
        }else{
            apiResponse('0','修改失败');
        }
    }


    public function deleteSupply(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['id'],'condition'=>'','error_msg'=>'id参数错误'),
        );
        check_param($param);//检查参数
        $where['m_id'] = $m_id;
        $where['id'] = $request['id'];
        $data = ['status'=>9];
        $res = D('Supply')->updateRow($where,$data);
        if($res){
            apiResponse('1','删除成功');
        }else{
            apiResponse('0','删除失败');
        }
    }


    /*
     * 查看供求详情
     * */
    public function supplyInfo(){
        $m_id = $this->member_obj->checkToken();
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['id'],'condition'=>'','error_msg'=>'id参数错误'),
        );
        check_param($param);//检查参数
        $where['s.id'] = $request['id'];
        $where['s.status'] = 1;
        $supply_info = M('Supply')->alias('s')->join('db_member m on m.id=s.m_id')->where($where)->field('s.*,m.nickname,m.head_pic')->find();
        if(!$supply_info){
            apiResponse('0','未查询到或已被删除');
        }
        $supply_info['pic_obj'] = $supply_info['pic']?returnSupplyImage($supply_info['pic']):[];
        $supply_info['head_pic_path'] = returnImage($supply_info['head_pic']);
        $info  = json_decode($supply_info['supply_info'],true);
        $index = [];
        foreach ($info as $k=>$v){
            $goods_info = M('Goods')->alias('g')->join('db_goods_type gt on gt.id=g.goods_type_id','left')->where(['g.id'=>$v['goods_id']])->field('g.goods_name,g.goods_type_id,g.stock,g.stock_unit,g.goods_status,gt.type_name,g.goods_pic')->find();
            $index[$k]['goods_id'] = $v['goods_id'];
            $index[$k]['goods_type_name'] = $goods_info['type_name'];
            $index[$k]['goods_type_id'] = $goods_info['goods_type_id'];
            $index[$k]['goods_name'] = $goods_info['goods_name'];
            $index[$k]['stock'] = $goods_info['stock'];
            $index[$k]['stock_unit'] = $goods_info['stock_unit'];
            $index[$k]['goods_pic_path'] = returnImage($goods_info['goods_pic']);
            $index[$k]['goods_status'] = $goods_info['goods_status'];
            $index[$k]['goods_price'] = $v['goods_price'];
        }
        $supply_info['supply_info'] = $index;
        if($m_id){
            $supply_info['is_collect'] = M('Collect')->where(['object_type'=>1, 'object_id'=>$_POST['id'],'m_id'=>$m_id])->find()?1:0;
        }else{
            $supply_info['is_collect'] = 0;
        }
        apiResponse('1','获取供求详情成功',$supply_info);
    }





    /*
     * 获取个人供求
     *
     * */
    public function memberSupplyList(){
        $m_id = $this->member_obj->checkToken();
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
        );
        check_param($param);//检查参数
        if($request['member_id']){
            $where['s.m_id'] = $request['member_id'];
        }
        if($request['type']){
            $where['s.type'] = $request['type'];
        }
        if($request['start_time']){
            $where['s.create_time'] = ['egt',$request['start_time']];
        }
        if($request['end_time']){
            $where['s.create_time'] = ['elt',$request['end_time']];
        }
        if($request['start_time'] && $request['end_time']){
            $where['s.create_time'] = [['egt',$request['start_time']],['elt',$request['end_time']],'AND'];
        }

        if($request['is_hidename']){
            $where['s.is_hidename'] = ['elt',$request['is_hidename']];
        }
        $where['s.status'] = 1;
        $list = D('Supply')->getList($where,$_POST['p']);
        foreach ($list as $k=>$v){
            $info  = json_decode($v['supply_info'],true);
            $index = [];
            foreach ($info as $k1=>$v1){
                $goods_info = M('Goods')->alias('g')->join('db_goods_type gt on gt.id=g.goods_type_id','left')->where(['g.id'=>$v1['goods_id']])->field('g.goods_name,g.goods_type_id,g.stock,g.stock_unit,g.goods_status,gt.type_name,g.goods_pic')->find();
                $index[$k1]['goods_id'] = $v1['goods_id'];
                $index[$k1]['goods_type_name'] = $goods_info['type_name'];
                $index[$k1]['goods_type_id'] = $goods_info['goods_type_id'];
                $index[$k1]['goods_name'] = $goods_info['goods_name'];
                $index[$k1]['stock'] = $goods_info['stock'];
                $index[$k1]['stock_unit'] = $goods_info['stock_unit'];
                $index[$k1]['goods_pic_path'] = returnImage($goods_info['goods_pic']);
                $index[$k1]['goods_status'] = $goods_info['goods_status'];
                $index[$k1]['goods_price'] = $v1['goods_price'];
            }
            $list[$k]['supply_info'] = $index;
            $list[$k]['head_pic_path'] = returnImage($v['head_pic'],'');
            $list[$k]['pic'] = $v['pic']?returnSupplyImageTwo($v['pic'],''):[];
            unset($index);unset($info);
        }
        apiResponse('1','成功',$list);
        
    }


    public function createJson(){
        $data[0]['goods_id'] = '1';
        $data[0]['goods_name'] = '纪梵希口红';
        $data[0]['goods_price'] = '500';
        $data[1]['goods_id'] = '2';
        $data[1]['goods_name'] = '迪奥口红';
        $data[1]['goods_price'] = '500';
        apiResponse('1','成功',$data);

    }



    public function getCity(){
        $list = M('Region')->where(['region_type'=>2])->page($_POST['p'].',20')->select();
        apiResponse('1','成功',$list);
    }


    /*
     * 删除供求
     * */
    public function delSupply()
    {
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['id'], 'condition' => '', 'error_msg' => '供求id参数错误'),
        );
        check_param($param);//检查参数
        $add = M('Supply')->where(['m_id' => $m_id, 'id' => $request['id']])->data(['status'=>9])->save();
        if ($add) {
            apiResponse('1', '删除成功');
        } else {
            apiResponse('0', '删除失败');
        }
    }


    //刷新供求
    public function refresh(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['id'], 'condition' => '', 'error_msg' => '供求id参数错误'),
        );
        check_param($param);//检查参数
        $refresh_num = M('Member')->where(['id' => $m_id ])->getField('refresh_num');
        if($refresh_num<=0){
            apiResponse('0','刷新次数不够');
        }
        $reduce = M('Member')->where(['id' => $m_id ])->setDec('refresh_num',1);
        if ($reduce) {
            M('Supply')->where(['m_id' => $m_id, 'id' => $request['id']])->data(['create_time'=>time()])->save();
            apiResponse('1', '刷新成功');
        } else {
            apiResponse('0', '刷新失败');
        }
    }


    public function refreshNum(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $refresh_num = M('Member')->where(['id' => $m_id])->getField('refresh_num');
        apiResponse('1','返回成功',['id'=>$refresh_num]);
    }

}