<?php
namespace Manager\Controller;

/**
 * Class GoodsTypeController
 * @package Manager\Controller
 */
class GoodsTypeController extends BaseController {

    /**
     * 添加时关联数据
     */
    function getAddRelation() {
        $this->assign('select',D('GoodsType','Logic')->getSelect('parent_id',I('get.id')));
    }

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        $this->assign('select',D('GoodsType','Logic')->getSelect('parent_id',I('get.parent_id')));
    }
}
