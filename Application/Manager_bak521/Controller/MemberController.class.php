<?php
namespace Manager\Controller;

/**
 * Class MemberController
 * @package Manager\Controller
 * 会员控制器
 */
class MemberController extends BaseController {

    /**
     * 添加
     */
    function add() {
        if(!IS_POST) {
            $this->getAddRelation();
            $this->display('add');
        } else {
            $Object = D(CONTROLLER_NAME,'Logic');
            $result = $Object->update(I('post.'));
            if($result) {
                $this->success($Object->getLogicSuccess(), Cookie('__forward__'));
            } else {
                $this->error($Object->getLogicError());
            }
        }
    }

    /**
     * 修改密码
     */
    function rePass() {
        if(!IS_POST) {
            $this->assign('m_id',$_GET['id']);
            $this->display('rePass');
        } else {
            $Object = D('Member', 'Logic');
            $result = $Object->rePass(I('post.'));
            if ($result) {
                $this->success($Object->getLogicSuccess(), Cookie('__forward__'));
            } else {
                $this->error($Object->getLogicError());
            }
        }
    }
}
