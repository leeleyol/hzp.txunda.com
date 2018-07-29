<?php

namespace Manager\Logic;

/**
 * 供求管理
 * Class OrderLogic
 * @package Manager\Logic
 */
class SupplyLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        /*if($_REQUEST['order_sn']){
            $param['where']['order_sn'] = $_REQUEST['order_sn'];
        }
        if($request['start_time'] && $request['end_time']){
            $param['where']['create_time'] = array(array('egt',strtotime($request['start_time'])),array('elt',strtotime($request['end_time'])+86400),'AND');
        }else if ($request['start_time']){
            $param['where']['create_time'] = array('egt',strtotime($request['start_time']));
        }else if($request['end_time']){
            $param['where']['create_time'] = array('elt',strtotime($request['end_time'])+86400);
        }*/
        $param['where']['status']   = array('lt',9);        //状态
        $param['order']             = 'create_time DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数
        $result = D('Supply')->getList($param);

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
        $row = D('Supply')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }

}