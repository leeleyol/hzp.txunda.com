<?php

namespace Common\Model;
use Think\Model;

/**
 * 用户银行卡模型
 * Class AddressModel
 * @package Common\Model
 */
class MemberBankModel extends Model{

    protected $tableName = 'member_bank';

    /**
     * 新增单条数据
     */
    public function addRow($param){

        $param['create_time'] = time();
        $m_id = $this->data($param)->add();
        return $m_id;
    }

    /**
     * 获取地址列表
     * @param array $param
     * @return array|mixed
     */
    public function rowList($param = array()){
        $list = $this->where($param['where'])->field($param['field'])->order($param['order'])->select();
        return $list?$list:array();
    }

    /**
     * 查询单条数据
     */
    public function findRow($param = array()){
        if(empty($param['field'])){
            $param['field'] = '*';
        }
        $row = $this->where($param['where'])->field($param['field'])->find();
        return $row;
    }

    /**
     * 更新数据
     * @param $where
     * @param $data
     * @return bool
     */
    public function updateRow($where,$data){
        $data['update_time'] = time();
        $res = $this->where($where)->data($data)->save();
        return $res;
    }
}