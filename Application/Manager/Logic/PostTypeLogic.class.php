<?php

namespace Manager\Logic;

/**
 * Class GoodsTypeLogic
 * @package Manager\Logic
 */
class PostTypeLogic extends BaseLogic{

    /**
     * @param array $request
     * @return array
     * 获取文章分类列表
     */
    function getList($request = array()) {
        $param['where']['status']   = array('lt',9);         //状态
        $param['order']             = 'sort DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数

        $result = D('PostType')->getList($param);
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
        $row = D('PostType')->findRow($param);

        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        //获取文件
        $row['picture'] = api('System/getFiles',array($row['picture']));
        return $row;
    }
}