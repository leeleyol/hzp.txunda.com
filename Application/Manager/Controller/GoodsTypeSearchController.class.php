<?php
namespace Manager\Controller;

/**
 * Class GoodsTypeSearchController
 * @package Manager\Controller
 */
class GoodsTypeSearchController extends BaseController {

    /**
     * 添加时关联数据
     */
    function getAddRelation() {
        $this->assign('select',M('GoodsType')->select());
    }

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        $this->assign('select',M('GoodsType')->where(1)->select());
    }
    

}
