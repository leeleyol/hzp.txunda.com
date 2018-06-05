<?php

namespace Manager\Logic;

/**
 * Class GoodsTypeLogic
 * @package Manager\Logic
 */
class GoodsTypeLogic extends BaseLogic{

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

        $result = D('GoodsType')->getList($param);
        return $result;


    }

    /**
     * @param string $field_name 隐藏文本框name名称
     * @param string $index_value 默认选中的值
     * @param string $index_field 默认选中字段
     * @return string
     * 获取分类树状下拉菜单
     */
    function getSelect($field_name = '', $index_value = '', $index_field = 'id') {
        //状态
        $param['where']['status']   = array('lt',9);
        $result = D('GoodsType')->getList($param);
        return api('Manager/Category/getSelectOne',array($result,$field_name,$index_value,$index_field));
    }

    /**
     * @param array $request
     * @return bool
     * 删除分类前操作 验证是否该分类下存在文章
     */
    protected function beforeSetStatus($request = array()) {
        if($request['status'] == 9) {
            //判断给分类下是否存在文章
            $where['goods_type_id'] = $request['ids'];
            $where['status']  = array('lt',9);
            $count = D('Goods')->where($where)->count();
            if($count > 0) {
                $this->setLogicError('该分类下存在上，请先删除该分类下的全部商品！'); return false;
            }
        }
        return true;
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 修改状态后执行
     */
    protected function afterSetStatus($result = 0, $request = array()) {
        S('GoodsType_Cache', null);
        return true;
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 更新后执行
     */
    protected function afterUpdate($result, $request = array()) {
        S('GoodsType_Cache', null);
        return true;
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
        $row = D('GoodsType')->findRow($param);

        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        //获取文件
        $row['type_picture'] = api('System/getFiles',array($row['type_picture']));
        $row['banner'] = api('System/getFiles',array($row['banner']));
        return $row;
    }
}