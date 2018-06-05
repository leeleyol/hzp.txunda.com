<?php

namespace Manager\Logic;

/**
 * 账单明细管理
 * Class OrderLogic
 * @package Manager\Logic
 */
class PayLogLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        $flag = true;
        if($_REQUEST['account']){
            $m_id = M('Member')->where(['account'=>array('LIKE','%'.$request['account'].'%'),'status'=>1])->getField('id',true);
            if($m_id){
                $param['where']['pl.m_id'] = array('in',implode(',',$m_id));
            }else{
                $flag = false;
            }
        }
        $param['order']             = 'create_time DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数

        $result = D('PayLog')->getList($param);
        if(!$flag){
            $result['list'] = [];
        }
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
        $row = D('PayLog')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }

}