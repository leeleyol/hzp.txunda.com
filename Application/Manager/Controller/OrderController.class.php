<?php

namespace Manager\Controller;

/**
 * 订单管理
 * Class OrderController
 * @package Manager\Controller
 */
class OrderController extends BaseController {
    public function getIndexRelation()
    {
        $this->assign('company_list',M('DeliveryCompany')->where(1)->select());
    }

    /**
     * 配送
     */
    public function delivery(){
        $order_id           = $_POST['order_id'];
        $delivery_sn        = $_POST['delivery_sn'];
        $delivery_compay_id = $_POST['delivery_compay_id'];
        $company_info = M('DeliveryCompany')->where(array('id'=>$delivery_compay_id))->find();
        $where['id'] = $order_id;
        $data['delivery_compay_name']  = $company_info['company_name'];
        $data['delivery_company_code'] = $company_info['delivery_code'];
        $data['delivery_sn']           = $delivery_sn;
        $data['update_time']           = time();
        $data['status']                = 2;
        $res = M('Order')->where($where)->data($data)->save();
        if($res){
            $this->ajaxReturn(array('success'=>'操作成功'));
        }else{
            $this->ajaxReturn(array('error'=>'操作失败'));
        }

    }

    /**
     * 打印訂單
     */
    public function printOrder(){
        if ($_GET['id']) {
            $Object = D(CONTROLLER_NAME, 'Logic');
            $row = $Object->findRow(I('get.'));
            if ($row) {
                $this->assign('row', $row);
            } else {
                $this->error($Object->getLogicError());
            }
        }
        $this->display('print');
    }


    /*
     * 拒绝取消订单
     * */
    public function refuseOrder(){
        if ($_GET['order_id']) {
            $Object = D(CONTROLLER_NAME, 'Logic');
            $row = $Object->refuse(I('get.'));
            if ($row) {
                $this->success($Object->getLogicSuccess());
            } else {
                $this->error($Object->getLogicError());
            }
        }
    }
}
