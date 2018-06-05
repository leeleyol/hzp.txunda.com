<?php

namespace Manager\Logic;

/**
 * Class PlatformCurrencyLogLogic
 * @package Manager\Logic
 */
class PlatformCurrencyLogLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        $param['where']['status']   = array('neq',9);
        $param['order']             = 'create_time DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数

        $result = D('PlatformCurrencyLog')->getList($param);
        foreach($result['list'] as $k => $v){
            $result['list'][$k]['account'] = M('Member')->where(array('id'=>$v['m_id']))->getField('account');
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
        $row = D('PlatformCurrencyLog')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }

}