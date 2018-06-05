<?php
namespace Manager\Logic;

/**
 * Class MemberLogic
 * @package Manager\Logic
 * 会员管理逻辑层
 */
class MemberLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['id']   = $request['id'];
        }
        if(!empty($request['account'])) {
            $param['where']['account']   = $request['account'];
        }
        if($request['start_time'] && $request['end_time']){
            $param['where']['create_time'] = array(array('egt',strtotime($request['start_time'])),array('elt',strtotime($request['end_time'])+86399),'AND');
        }else if($request['start_time']){
            $param['where']['create_time'] = array('egt',strtotime($request['start_time']));
        }else if($request['end_time']){
            $param['where']['create_time'] = array('elt',strtotime($request['end_time'])+86399);
        }
        $param['order']             = 'create_time DESC';   //排序
        $param['where']['status']   = array('lt',9);        //状态
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数
        $result = D('Member')->getList($param);
        return $result;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['m.id'] = $request['id'];
        } else {
            $this->setLogicError('参数错误！'); return false;
        }
        $param['where']['m.status'] = array('lt',9);
        $row = D('Member')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }

    /**
     * @param array $request
     * @return bool|mixed
     * 修改密码
     */
    function rePass($request = array()) {
        if(empty($request['new_password'])) {
            $this->setLogicError('请输入新密码！'); return false;
        } if(strlen($request['new_password']) < 6 || strlen($request['new_password']) > 18) {
            $this->setLogicError('新密码长度在6--18位之间！'); return false;
        } if($request['re_new_password'] != $request['new_password']) {
            $this->setLogicError('确认新密码与新密码不一致！'); return false;
        }

        //修改
        $data['password'] = MD5($request['new_password']);
        $where['id'] = $request['id'];
        $result = D('Member')->where($where)->data($data)->save();
        if($result){
            $this->setLogicSuccess('修改密码成功！'); return true;
        }else{
            $this->setLogicError('修改密码失败！'); return false;
        }
    }

    /**
     * @param array $data
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array()) {
        if(empty($data['id'])) {
            $data['password'] = MD5($data['password']);
            $data['status']   = 1;
        }
        return $data;
    }
}