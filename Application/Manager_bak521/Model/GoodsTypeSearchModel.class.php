<?php
namespace Manager\Model;

/**
 * Class GoodsTypeSearchModel
 * @package Manager\Model
 */
class GoodsTypeSearchModel extends BaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('keywords', 'require', '关键字未填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('logo', 'require', '图片未上传', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
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
            $total      = $this->alias('gth')->where($param['where'])->count();
            $Page       = $this->getPage($total, $param['page_size'], $param['parameter']);
            $page_show  = $Page->show();
        }

        $model  = $this->alias('gth')
                        ->join(array('LEFT JOIN '.C('DB_PREFIX').'goods_type gt ON gt.id = gth.goods_type_id',))
                        ->where($param['where'])
                        ->field('gth.*,gt.type_name')
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
        return $row;
    }
}