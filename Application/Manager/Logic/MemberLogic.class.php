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

        $approve_status = M('MemberInfo')->where(['m_id'=>$request['id']])->getField('approve_status');
        $row['approve_status'] = $approve_status?$approve_status:"0";
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



    /**
     * @param array $request
     * @return bool|mixed
     * 新增 或 修改
     */
    function update($request = array()) {
        //执行前操作
        if(!$this->beforeUpdate($request)) { return false; }
        $model = $request['model'];
        unset($request['model']);
        //获取数据对象
        $data = D($model)->create($request);
        if(!$data) {
            $this->setLogicError(D($model)->getError()); return false;
        }
        //处理数据
        $data = $this->processData($data);
        //判断增加还是修改
        if(empty($data['id'])) {
            //新增数据
            $result = D($model)->data($data)->add();
            if(!$result) {
                $this->setLogicError('新增时出错！'); return false;
            }
            //行为日志
            api('Manager/ActionLog/actionLog', array('add',$model,$result,AID));
        } else {
            $is_edit = false;
            //创建修改参数
            $where['id'] = $request['id'];

            if($request['approve_status']){
                if($request['approve_status']==2){
                    $data['type'] = 1;
                }
                $is_edit = true;
            }
            $result = D($model)->where($where)->data($data)->save();
            if(!$result) {
                $this->setLogicError('您未修改任何值！'); return false;
            }
            if($is_edit){
                M('MemberInfo')->where(['m_id'=>$request['id']])->data(['approve_status'=>$request['approve_status']])->save();
            }
            //行为日志
            api('Manager/ActionLog/actionLog', array('edit',$model,$data['id'],AID));
        }
        //执行后操作
        if(!$this->afterUpdate($result,$request)) { return false; }

        $this->setLogicSuccess($data['id'] ? '更新成功！' : '新增成功！'); return true;
    }
}