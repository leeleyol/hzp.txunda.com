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


    
    
    
    
}