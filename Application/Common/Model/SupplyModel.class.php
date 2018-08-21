<?php

namespace Common\Model;
use Think\Model;

/**
 * 供求模型
 * Class MemberModel
 * @package Common\Model
 */
class SupplyModel extends Model{

    protected $tableName = 'supply';

    /**
     * 新增单条数据
     */
    public function addRow($param){
        $m_id = $this->data($param)->add();
        return $m_id;
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


    public function getList($where,$page){
        if($page){
            $data = $this->alias('s')->join('db_member m on m.id=s.m_id')->where($where)->field('s.*,m.nickname,m.head_pic')->page($page.',10')->order('create_time desc')->select();
        }else{
            $data = $this->alias('s')->join('db_member m on m.id=s.m_id')->where($where)->field('s.*,m.nickname,m.head_pic')->order('create_time desc')->limit(3)->select();

        }
        return $data?$data:[];

    }

}