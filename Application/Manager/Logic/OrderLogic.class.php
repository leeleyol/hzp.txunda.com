<?php

namespace Manager\Logic;

/**
 * 订单管理
 * Class OrderLogic
 * @package Manager\Logic
 */
class OrderLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        if($_REQUEST['order_sn']){
            $param['where']['order_sn'] = $_REQUEST['order_sn'];
        }
        if($request['start_time'] && $request['end_time']){
            $param['where']['create_time'] = array(array('egt',strtotime($request['start_time'])),array('elt',strtotime($request['end_time'])+86400),'AND');
        }else if ($request['start_time']){
            $param['where']['create_time'] = array('egt',strtotime($request['start_time']));
        }else if($request['end_time']){
            $param['where']['create_time'] = array('elt',strtotime($request['end_time'])+86400);
        }
        $param['where']['type']     = $_REQUEST['type'];
        $param['where']['status']   = array('lt',9);        //状态
        $param['order']             = 'create_time DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数

        $result = D('Order')->getList($param);

        return $result;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicError('参数错误！'); return false;
        }
        $param['where']['status'] = array('lt',9);
        $row = D('Order')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }



    /**
     * @param array $request  model 模型  ids操作的主键ID  status要改为的状态
     * @return bool
     * 修改状态
     */
    function refuse($request = array()) {
        //判断参数
        if(empty($request['model']) || empty($request['order_id']) ) {
            $this->setLogicError('参数错误！'); return false;
        }
        //执行前操作
        if(!$this->beforeSetStatus($request)) { return false; }
        //判断是数组ID还是字符ID
        $order_id = I('get.order_id');
        $before_status = M('Order')->where(['id'=>$order_id])->getField('cancel_order_status');
        if($before_status==0){
            $this->setLogicError('拒绝失败 当前订单不是审核状态！'); return false;
        }
        $data = [
            'status'=>$before_status,
            'cancel_order_status'=>0
        ];
        $result = M('Order')->where(['id'=>$order_id])->data($data)->save();

        if($result) {
            //行为日志
            api('Manager/ActionLog/actionLog', array('change_status',$request['model'],$order_id,AID));
            //执行后操作
            if(!$this->afterSetStatus($result,$request)) { return false; }
            $this->setLogicSuccess('操作成功！'); return true;
        } else {
            $this->setLogicError('操作失败！'); return false;
        }
    }
}