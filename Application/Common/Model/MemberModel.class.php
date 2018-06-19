<?php

namespace Common\Model;
use Think\Model;

/**
 * 用户模型
 * Class MemberModel
 * @package Common\Model
 */
class MemberModel extends Model{

    protected $tableName = 'member';

    /**
     * 新增单条数据
     */
    public function addRow($param){
        $data['account']  = $param['account'];
        $data['create_time'] = time();
        $m_id = $this->data($data)->add();
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

    /**
     * 生成token
     */
    public function createToken(){
        $arr['token'] = time().rand(10000,99999);
        $arr['expired_time'] = time()+86400*7;
        return $arr;
    }

    /**
     * 验证token
     */
    function checkToken()
    {
        unset($w);
        $m_id = $_POST['m_id'];
        if (empty($m_id)) {
            return 0;
        }
        $w['id'] = $m_id;
        $w['status'] = array('neq', 0);
        $m_id = $this->where($w)->getField('id');
        return $m_id ? $m_id : 0;
    }

    /**
     * token过期的时返回错误信息
     */
    function errorTokenMsg($m_id){
        if($m_id==0){
            apiResponse('-1','登录失效，请重新登录');
        }
    }

    
}