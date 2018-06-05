<?php
namespace Manager\Logic;

/**
 * Class MemberLogic
 * @package Manager\Logic
 * 商品管理逻辑层
 */
class GoodsLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        if($_REQUEST['title']){
            $param['where']['g.title'] = array('LIKE','%'.$_REQUEST['title'].'%');
        }
        if($_REQUEST['goods_type_id']){
            $param['where']['g.goods_type_id'] = $_REQUEST['goods_type_id'];
        }

        $param['order']             = 'create_time DESC';   //排序
        $param['where']['g.status']   = array('lt',9);        //状态
        $param['page_size']         = C('LIST_ROWS');        //页码
        $param['parameter']         = $request;             //拼接参数
        $result = D('Goods')->getList($param);
        return $result;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicError('参数错误！'); return false;
        }
        $param['where']['status'] = array('lt',9);
        $row = D('Goods')->findRow($param);
        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        $row['sec_g_t_list'] = M('GoodsType')->where(array('status'=>array('neq',9),'parent_id'=>$row['goods_type_id']))->select();
        $row['thr_g_t_list'] = M('GoodsType')->where(array('status'=>array('neq',9),'parent_id'=>$row['sec_goods_type_id']))->select();
        return $row;
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
        $data['content'] = $_POST['content'];

        //处理数据
        $data = $this->processData($data);
        //判断增加还是修改
        if(empty($data['id'])) {
            //新增数据
            $result = D($model)->data($data)->add();
            if(!$result) {
                $this->setLogicError('新增时出错！'); return false;
            }
            //行为日志
            api('Manager/ActionLog/actionLog', array('add',$model,$result,AID));
        } else {

            //创建修改参数
            $where['id'] = $request['id'];
            $result = D($model)->where($where)->data($data)->save();
            if(!$result) {
                $this->setLogicError('您未修改任何值！'); return false;
            }
            //行为日志
            api('Manager/ActionLog/actionLog', array('edit',$model,$data['id'],AID));
        }
        //执行后操作
        if(!$this->afterUpdate($result,$request)) { return false; }

        $this->setLogicSuccess($data['id'] ? '更新成功！' : '新增成功！'); return true;
    }

    public function processData($data = array())
    {
        $data['coupon_start_time'] = strtotime($_POST['coupon_start_time']);
        $data['coupon_end_time'] = strtotime($_POST['coupon_end_time']);
        return $data;
    }
}