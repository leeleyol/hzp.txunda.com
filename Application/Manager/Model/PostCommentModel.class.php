<?php
namespace Manager\Model;

/**
 * Class CommoditynModel
 * @package Manager\Model
 */
class PostCommentModel extends BaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

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
            $total      = $this->alias('pc')->where($param['where'])->count();
            $Page       = $this->getPage($total, $param['page_size'], $param['parameter']);
            $page_show  = $Page->show();
        }

        $model  = $this->alias('pc')->where($param['where'])
                        ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = pc.from_mid')
                        ->order($param['order'])
                        ->field('pc.*,m.account');

        //是否分页
        !empty($param['page_size'])  ? $model = $model->limit($Page->firstRow,$Page->listRows) : '';

        $list = $model->select();

//        dump($list);
        return array('list'=>$list,'page'=>!empty($page_show) ? $page_show : '');
    }

    /**
     * @param array $param
     * @return mixed
     */
    function findRow($param = array()) {
        $row = $this->alias('pc')->where($param['where'])
            ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = pc.from_mid')
            ->order($param['order'])
            ->field('pc.*,m.account')->find();
        return $row;
    }
}