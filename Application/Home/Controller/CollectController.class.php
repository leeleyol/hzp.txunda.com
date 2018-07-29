<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 收藏
 * Class CollectController
 * @package Home\Controller
 */
class CollectController extends BaseController
{
    /**
    * 初始化方法
    */
    public $member_obj = '';
    public $file_obj   = '';
    public function _initialize()
    {
        parent::_initialize();
        $this->member_obj = D('Member');
        $this->file_obj   = D('File');
    }


    /**
     * 我的关注
     */
    public function myCollect()
    {
        $this->display('myCollect');
    }

    /*
     * 添加收藏
     * */
    public function addCollect()
    {
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['object_id'], 'condition' => '', 'error_msg' => 'id参数错误'),
            array('check_type' => 'is_null', 'parameter' => $request['object_type'], 'condition' => '', 'error_msg' => '类型参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id' => $m_id,
            'object_type' => $request['object_type'],
            'object_id' => $request['object_id'],
            'create_time' => time(),
        ];
        $add = M('Collect')->data($data)->add();
        if ($add) {
            apiResponse('1', '收藏成功');
        } else {
            apiResponse('0', '收藏失败');
        }
    }


    /*
     * 取消收藏
     * */
    public function delCollect()
    {
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['object_id'], 'condition' => '', 'error_msg' => '对象id参数错误'),
            array('check_type' => 'is_null', 'parameter' => $request['object_type'], 'condition' => '', 'error_msg' => '类型参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $add = M('Collect')->where(['m_id' => $m_id, 'object_id' => $request['object_id'],'object_type'=>$request['object_type']])->delete();
        if ($add) {
            apiResponse('1', '删除成功');
        } else {
            apiResponse('0', '删除失败');
        }
    }


    /*
     * 取消收藏
     * */
    public function delMoreCollect()
    {
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['col_id'], 'condition' => '', 'error_msg' => '收藏id参数错误')
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $add = M('Collect')->where(['m_id' => $m_id, 'id' => array('in',$request['col_id'])])->delete();
        if ($add) {
            apiResponse('1', '删除成功');
        } else {
            apiResponse('0', '删除失败');
        }
    }


    /*
     * 获取收藏的帖子
     * */
    public function myCollectPost(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['p'], 'condition' => '', 'error_msg' => '分页参数错误')
        );
        check_param($param);//检查参数
        $where['c.m_id'] = $m_id;
        $where['c.object_type'] = 2;
        $list = M('Collect')->alias('c')
            ->join('db_post as p on p.id = c.object_id', 'left')
            ->join('db_post_type as pt on pt.id = p.type_id', 'left')
            ->join('db_member as m on m.id = p.m_id', 'left')
            ->where($where)
            ->field('c.id col_id,p.id post_id,p.title,p.type_id,p.content,p.pic,p.view,p.comment_num,p.create_time,p.m_id,m.nickname,m.head_pic,pt.type_name')
            ->page($request['p'].',10')
            ->select();
        if(empty($list)){
            $message =$request['p']==1?'暂无记录':'无更多记录';
            apiResponse('0',$message);
        }
        foreach ($list as $k=>$v){
            $list[$k]['head_pic'] = returnImage($v['head_pic'],'');
            $pic = returnPostImage($v['pic']);
            $list[$k]['pic_list'] = $pic;
        }
        apiResponse('1','请求成功',$list);

    }




    /*
     * 获取收藏的供求
     * */
    public function myCollectSupply(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['p'], 'condition' => '', 'error_msg' => '分页参数错误')
        );
        check_param($param);//检查参数
        $where['c.m_id'] = $m_id;
        $where['c.object_type'] = 1;
        $list = M('Collect')->alias('c')
            ->join('db_supply as s on s.id = c.object_id', 'left')
            ->join('db_member as m on m.id = c.m_id', 'left')
            ->where($where)
            ->field('c.id col_id,s.id supply_id,s.area_name,s.pic,s.description,s.type,s.m_id,s.supply_info,s.create_time,s.is_extract,s.create_time,s.is_md,s.is_sf,s.is_bp,s.is_hidename,m.nickname,m.head_pic')
            ->page($request['p'].',10')
            ->select();
        if(empty($list)){
            $message =$request['p']==1?'暂无记录':'无更多记录';
            apiResponse('0',$message);
        }
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
            $list[$k]['pic'] = $v['pic']?returnImage($v['pic']):[];
            unset($index);unset($info);
        }
        apiResponse('1','请求成功',$list);

    }


    
    
    
    
}