<?php

namespace Manager\Logic;

/**
 * Class NewBagLogLogic
 * @package Manager\Logic
 */
class NewBagLogLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        if($request['account']){
            $param['where']['m_id'] = M('member')->where(array('account'=>$request['account'],'status'=>array('neq',9)))->getField('id');
        }
        $param['where']['status']   = array('lt',9);        //状态
        $param['order']             = 'create_time DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数

        $result = D('NewBagLog')->getList($param);
        foreach($result['list'] as $k => $v){
            $result['list'][$k]['account'] = M('Member')->where(array('id'=>$v['m_id']))->getField('account');
            if($v['status']==1){
                $result['list'][$k]['status'] = '已领取';
            }else{
                if($v['end_time']>time()){
                    $result['list'][$k]['status'] = '未领取';
                }else{
                    $result['list'][$k]['status'] = '已领取';
                }
            }
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
        $row = D('NewBagLog')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }
}