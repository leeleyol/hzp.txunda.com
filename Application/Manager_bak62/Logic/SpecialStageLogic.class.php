<?php

namespace Manager\Logic;

/**
 * 专场
 * Class SpecialStageLogic
 * @package Manager\Logic
 */
class SpecialStageLogic extends BaseLogic {

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

        $result = D('SpecialStage')->getList($param);
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
        $row = D('SpecialStage')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        $row['stage_icon'] = api('System/getFiles',array($row['stage_icon']));
        $row['stage_picture'] = api('System/getFiles',array($row['stage_picture']));
        return $row;
    }

}