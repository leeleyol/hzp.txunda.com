<?php

namespace Manager\Logic;

/**
 * Class SendLogLogic
 * @package Manager\Logic
 * 拍卖商品逻辑层
 */
class AuctionGoodsLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     */
    function getList($request = array()) {

        $param['where']['ag.status']   = array('lt',9);        //状态
        $param['order']             = 'create_time DESC';   //排序
        $param['page_size']         = C('LIST_ROWS');       //页码
        $param['parameter']         = $request;             //拼接参数

        $result = D('AuctionGoods')->getList($param);

        return $result;
    }

    /**
     * @param array $request
     * @return bool
     */
    function findRow($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['ag.id'] = $request['id'];
        } else {
            $this->setLogicError('参数错误！'); return false;
        }
        $param['where']['ag.status'] = array('lt',9);
        $row = D('AuctionGoods')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        return $row;
    }



    function setStatus($request = array()) {
        //判断参数
        if(empty($request['model']) || empty($request['ids']) || !isset($request['status'])) {
            $this->setLogicError('参数错误！'); return false;
        }
        //执行前操作
        if(!$this->beforeSetStatus($request)) { return false; }
        //判断是数组ID还是字符ID
        if(is_array($request['ids'])) {
            //数组ID
            $where['id'] = array('in',$request['ids']);
            $ids = implode(',',$request['ids']);
        } elseif (is_numeric($request['ids'])) {
            //数字ID
            $where['id'] = $request['ids'];
            $ids = $request['ids'];
        }

        $data = array(
            'status'        => $request['status'],
            'update_time'   => time()
        );

        $result = D($request['model'])->where($where)->data($data)->save();

        if($result) {
            //执行后操作
            if(!$this->afterSetStatus($result,$request)) { return false; }
            $this->setLogicSuccess('操作成功！'); return true;
        } else {
            $this->setLogicError('操作失败！'); return false;
        }
    }
    /**
     * @param array $request
     * @return bool|mixed
     * 新增 或 修改
     */
    function update($request = array()) {
        //执行前操作
        if(!$this->beforeUpdate($request)) { return false; }
        $model = $request['model'];
        unset($request['model']);
        //获取数据对象
        $data = D($model)->create($request);
        if(!$data) {
            $this->setLogicError(D($model)->getError()); return false;
        }
        //处理数据
        $data = $this->processData($data);
        $data['auc_desc'] = $_POST['auc_desc'];
        $data['goods_name'] = M('Goods')->where(['id'=>$_POST['goods_id']])->getField('goods_name');
        $data['goods_attr'] = M('GoodsProduct')->where(['id'=>$_POST['product_id']])->getField('goods_attr_name_str');
        //判断增加还是修改
        if(empty($data['id'])) {
            //新增数据
            $result = D($model)->data($data)->add();
            if(!$result) {
                $this->setLogicError('新增时出错！'); return false;
            }
        } else {
            //创建修改参数
            $where['id'] = $request['id'];
            $result = D($model)->where($where)->data($data)->save();
            if(!$result) {
                $this->setLogicError('您未修改任何值！'); return false;
            }
        }
        //执行后操作
        if(!$this->afterUpdate($result,$request)) { return false; }

        $this->setLogicSuccess($data['id'] ? '更新成功！' : '新增成功！'); return true;
    }

}