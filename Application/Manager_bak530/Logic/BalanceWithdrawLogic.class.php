<?php

namespace Manager\Logic;

/**
 * 余额提现管理
 * Class OrderLogic
 * @package Manager\Logic
 */
class BalanceWithdrawLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        if($_REQUEST['bank_real_name']){
            $param['where']['bank_real_name'] = array('LIKE','%'.$request['bank_real_name'].'%');
        }
        if($_REQUEST['bank_name']){
            $param['where']['bank_name'] = array('LIKE','%'.$request['bank_name'].'%');
        }
        if($_REQUEST['bank_mobile']){
            $param['where']['bank_mobile'] = array('LIKE','%'.$request['bank_mobile'].'%');
        }
        if($_REQUEST['bank_number']){
            $param['where']['bank_number'] = array('LIKE','%'.$request['bank_number'].'%');
        }
        $param['order']             = 'create_time DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数

        $result = D('BalanceWithdraw')->getList($param);
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
        $row = D('BalanceWithdraw')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }

}