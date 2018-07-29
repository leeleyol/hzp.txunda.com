<?php
namespace Manager\Model;

/**
 * 订单
 * Class OrderModel
 * @package Manager\Model
 */
class SupplyModel extends BaseModel {

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
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        if(!empty($param['page_size'])) {
            $total      = $this->where($param['where'])->count();
            $Page       = $this->getPage($total, $param['page_size'], $param['parameter']);
            $page_show  = $Page->show();
        }

        $model  = $this->where($param['where'])->order($param['order']);

        //是否分页
        !empty($param['page_size'])  ? $model = $model->limit($Page->firstRow,$Page->listRows) : '';

        $list = $model->select();
        foreach ($list as &$col){
            $col['account'] = M('Member')->where(['id'=>$col['m_id']])->getField('nickname');
        }
        return array('list'=>$list,'page'=>!empty($page_show) ? $page_show : '');
    }

    /**
     * @param array $param
     * @return mixed
     */
    function findRow($param = array()) {
        $row = $this->where($param['where'])->find();

        $info  = json_decode($row['supply_info'],true);
        $index = [];
        foreach ($info as $k=>$v){
            $goods_info = M('Goods')->alias('g')->join('db_goods_type gt on gt.id=g.goods_type_id','left')->where(['g.id'=>$v['goods_id']])->field('g.goods_name,g.goods_type_id,g.stock,g.stock_unit,g.goods_status,gt.type_name,g.goods_pic')->find();
            $index[$k]['goods_id'] = $v['goods_id'];
            $index[$k]['goods_type_name'] = $goods_info['type_name'];
            $index[$k]['goods_type_id'] = $goods_info['goods_type_id'];
            $index[$k]['goods_name'] = $goods_info['goods_name'];
            $index[$k]['stock'] = $goods_info['stock'];
            $index[$k]['stock_unit'] = $goods_info['stock_unit'];
            $index[$k]['goods_pic_path'] = returnImage($goods_info['goods_pic']);
            $index[$k]['goods_status'] = $goods_info['goods_status'];
            $index[$k]['goods_price'] = $v['goods_price'];
        }
        $row['supply_info'] = $index;
        $row['account'] = M('Member')->where(['id'=>$row['m_id']])->getField('nickname');
        return $row;
    }
}