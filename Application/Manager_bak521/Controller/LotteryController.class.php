<?php

namespace Manager\Controller;

/**
 * Class LotteryController
 * @package Manager\Controller
 */
class LotteryController extends BaseController {

    function update()
    {
        $this->checkRule(self::$rule);
        if (!IS_POST) {
            if ($_GET['id']) {
                $Object = D(CONTROLLER_NAME, 'Logic');
                $row = $Object->findRow(I('get.'));
                if ($row) {
                    $this->getUpdateRelation();
                    $this->assign('row', $row);
                } else {
                    $this->error($Object->getLogicError());
                }
            }
            // 记录当前列表页的cookie
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->display('update');
        } else {
            $Object = D(CONTROLLER_NAME, 'Logic');
            $result = $Object->update(I('post.'));
            if ($result) {
                $this->success($Object->getLogicSuccess(), Cookie('__forward__'));
            } else {
                $this->error($Object->getLogicError());
            }
        }
    }
}
