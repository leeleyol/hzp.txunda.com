<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 关注
 * Class CollectController
 * @package Home\Controller
 */
class AttentionController extends BaseController
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




    /*
     * 添加关注
     * */
    public function addAttention()
    {
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['object_id'], 'condition' => '', 'error_msg' => 'id参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id' => $m_id,
            'object_id' => $request['object_id'],
            'create_time' => time(),
        ];
        $add = M('Attention')->data($data)->add();
        if ($add) {
            M('Member')->where(['id'=>$request['object_id']])->setInc('attention_num',1);
            apiResponse('1', '添加关注成功');
        } else {
            apiResponse('0', '添加关注失败');
        }
    }


    /*
     * 取消关注
     * */
    public function delAttention()
    {
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type' => 'is_null', 'parameter' => $request['object_id'], 'condition' => '', 'error_msg' => '对象id参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $add = M('Attention')->where(['m_id' => $m_id, 'object_id' => $request['object_id']])->delete();
        if ($add) {
            M('Member')->where(['id'=>$request['object_id']])->setDec('attention_num',1);
            apiResponse('1', '取消关注成功');
        } else {
            apiResponse('0', '取消关注失败');
        }
    }


    /*
     * 我的关注
     * */
    public function myAttention()
    {
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $where['a.m_id'] = $m_id;
        $where['m.type'] = $request['type']?$request['type']:0;
        $list = M('Attention')->alias('a')
                ->join('db_member as m on m.id = a.object_id', 'inner')
                ->where($where)
                ->field('m.head_pic,m.nickname,m.id as member_id,m.type,a.id attention_id,m.attention_num')
                ->page($request['p'].',10')
                ->select();
        if(empty($list)){
            $message =$request['p']==1?'暂无记录':'无更多记录';
            apiResponse('0',$message);
        }
        foreach ($list as $k=>$v){
            $list[$k]['head_pic_path'] = returnImage($v['head_pic']);
            $list[$k]['goods_num'] = getMemberGoodsNum($v['m_id']);
            $list[$k]['need_num'] = getMemberSupplyNum($v['m_id']);
        }
        apiResponse('1','请求成功',$list);

    }




}