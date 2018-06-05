<?php
namespace Manager\Model;

/**
 * Class ArticleModel
 * @package Manager\Model
 * 商品模型
 */
class GoodsModel extends BaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('title', 'require', '商品名称未填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('goods_price', 'require', '商品价格未填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array (
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    function getList($param = array()) {
        if(!empty($param['page_size'])) {
            $total      = $this->alias('g')->where($param['where'])->count();
            $Page       = $this->getPage($total, $param['page_size'], $param['parameter']);
            $page_show  = $Page->show();
        }

        $model  = $this->alias('g')
                        ->join(array('LEFT JOIN '.C('DB_PREFIX').'goods_type gt ON gt.id = g.goods_type_id',))
                        ->join(array('LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = g.m_id',))
                        ->where($param['where'])
                        ->field('g.*,gt.type_name,m.account')
                        ->order($param['order']);

        //是否分页
        !empty($param['page_size'])  ? $model = $model->limit($Page->firstRow,$Page->listRows) : '';

        $list = $model->select();

        return array('list'=>$list,'page'=>!empty($page_show) ? $page_show : '');
    }

    /**
     * @param $param
     * @return mixed
     */
    function findRow($param = array()) {
        $row = $this->where($param['where'])->find();
        $row['account'] = M('Member')->where(['id'=>$row['m_id']])->getField('account');
        return $row;
    }
}