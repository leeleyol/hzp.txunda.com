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
        $supply_info['pic'] = $supply_info['pic']?returnImage($supply_info['pic']):[];
        $supply_info['head_pic_path'] = returnImage($supply_info['head_pic']);
        $supply_info['supply_info'] = json_decode($supply_info['supply_info'],true);
        if($m_id){
            $supply_info['is_collect'] = M('Collect')->where(['object_type'=>2,'object_id'=>$_POST['post_id'],'m_id'=>$m_id])->find()?1:0;
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
            array('check_type'=>'is_null','parameter' => $request['member_id'],'condition'=>'','error_msg'=>'浏览对象参数错误'),
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
        );
        check_param($param);//检查参数
        $where['s.id'] = $request['member_id'];
        $where['s.status'] = 1;
        $list = D('Supply')->getList($where,$_POST['p']);
        foreach ($list as $k=>$v){
            $list[$k]['supply_info'] = $v['supply_info']?json_decode($v['supply_info'],true):[];
            $list[$k]['head_pic_path'] = returnImage($v['head_pic'],'');
            $list[$k]['pic'] = returnImage($v['pic']);
        }
        apiResponse('1','成功',$list);
        
    }


    public function createJson(){
        $data[0]['type_name'] = '纪梵希';
        $data[0]['type_id'] = '1';
        $data[0]['goods_list'][0] = ['goods_name'=>'纪梵希口红1','goods_id'=>1,'stock'=>500,'stock_unit'=>'件','goods_status'=>'现货','goods_pic_path'=>''];
        $data[0]['goods_list'][1] = ['goods_name'=>'纪梵希口红2','goods_id'=>2,'stock'=>400,'stock_unit'=>'件','goods_status'=>'现货','goods_pic_path'=>''];
        $data[1]['type_name'] = '迪奥';
        $data[1]['type_id'] = '2';
        $data[1]['goods_list'][0] = ['goods_name'=>'迪奥1','goods_id'=>3,'stock'=>520,'stock_unit'=>'支','goods_status'=>'现货','goods_pic_path'=>''];
        apiResponse('1','成功',$data);

    }



    public function getCity(){
        $list = M('Region')->where(['region_type'=>2])->page($_POST['p'].',1')->select();
        apiResponse('1','成功',$list);
    }
}