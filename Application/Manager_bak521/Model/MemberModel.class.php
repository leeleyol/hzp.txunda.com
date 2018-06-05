<?php
namespace Manager\Model;

/**
 * Class MemberModel
 * @package Manager\Model
 * 会员模型
 */
class MemberModel extends BaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('account', 'require', '账号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('account', '/^0?(13[0-9]|15[0-9]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/', '账号格式不正确', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('account', 'checkUnique', '绑定手机号已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_INSERT, array('account')),

    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    function getList($param = array()) {
        if(!empty($param['page_size'])) {
            $total      = $this->alias('m')->where($param['where'])->count();
            $Page       = $this->getPage($total, $param['page_size'], $param['parameter']);
            $page_show  = $Page->show();
        }

        $model  = $this->where($param['where'])->order($param['order']);

        //是否分页
        !empty($param['page_size'])  ? $model = $model->limit($Page->firstRow,$Page->listRows) : '';

        $list = $model->select();

        return array('list'=>$list,'page'=>!empty($page_show) ? $page_show : '');
    }

    function findRow($param = array()) {
        $row = $this->alias('m')
                    ->field('m.*')
                    ->where($param['where'])
                    ->find();
        return $row;
    }
}