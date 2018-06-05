<?php

namespace Common\Model;
use Think\Model;

/**
 * 全国地址库模型
 * Class RegionModel
 * @package Common\Model
 */
class RegionModel extends Model{

    protected $tableName = 'region';

    /**
     * 获取地址列表
     * @param array $param
     * @return array|mixed
     */
    public function regionList($param = array()){
        $list = $this->where($param['where'])->field($param['field'])->select();
        return $list?$list:array();
    }

    /**
     * 获取省/市/区地址的名称
     * @param array $param
     * @return mixed|string
     */
    public function getRegionName($param = array()){
        $region_name = $this->where($param['where'])->getField('region_name');
        return $region_name?$region_name:'';
    }

}