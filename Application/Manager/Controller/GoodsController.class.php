<?php
namespace Manager\Controller;

/**
 * Class ArticleCategoryController
 * @package Manager\Controller
 * 商品 控制器
 */
class GoodsController extends BaseController {

    /**
     * 添加时关联数据
     */
    function getAddRelation() {
        $this->assign('fir_g_t_list',M('GoodsType')->where(array('parent_id'=>'0','status'=>array('neq',9)))->select());
        $this->assign('stage',M('special_stage')->where(array('status'=>array('neq',9)))->select());
    }

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        $this->assign('fir_g_t_list',M('GoodsType')->where(array('parent_id'=>'0','status'=>array('neq',9)))->select());
        $this->assign('stage',M('special_stage')->where(array('status'=>array('neq',9)))->select());
    }

    /**
     * 列表关联数据
     */
    public function getIndexRelation()
    {
        $this->assign('select',M('GoodsType')->where(array('parent_id'=>'0','status'=>array('neq',9)))->select());
        $this->assign('stage',M('special_stage')->where(array('status'=>array('neq',9)))->select());
    }


}
