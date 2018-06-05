<?php

namespace Manager\Controller;

/**
 * 用户提现
 * Class WithdrawController
 * @package Manager\Controller
 *
 */
class WithdrawController extends BaseController {

    /**
     * 提现成功
     */
    public function doWithdraw(){
        $Object = D(CONTROLLER_NAME,'Logic');
        $result = $Object->doWithdraw(I('request.'));
        if($result) {
            $this->success($Object->getLogicSuccess());
        } else {
            $this->error($Object->getLogicError());
        }
    }
}
