<?php
namespace Manager\Model;

/**
 * Class GoodsTypeModel
 * @package Manager\Model
 */
class GoodsTypeModel extends BaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array (
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        $list  = $this->where($param['where'])->order($param['order'])->select();
        return $list;
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