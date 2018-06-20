<?php

namespace Manager\Controller;

class PostController extends BaseController {

    /**
     * 添加
     */
    function add() {
//        $this->checkRule(self::$rule);
        if(!IS_POST) {
            $this->getAddRelation();
            $this->display('update');
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
     * 修改
     */
    function update() {
//        $this->checkRule(self::$rule);
        if(!IS_POST) {
            if ($_GET['id']) {
                $Object = D(CONTROLLER_NAME,'Logic');
                $row = $Object->findRow(I('get.'));
                if ($row) {
                    $this->getUpdateRelation();
                    $this->assign('row', $row);
                } else {
                    $this->error($Object->getLogicError());
                }
            }
            $post_type = M('PostType')->where(['status'=>1])->select();
            $this->assign('post_type', $post_type);
            $this->display('update');
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




    function setStatus(){
        $Object = D(CONTROLLER_NAME,'Logic');
        $result = $Object->setStatus(I('get.'));
        if($result) {
            $this->success($Object->getLogicSuccess(), Cookie('__forward__'));
        } else {
            $this->error($Object->getLogicError());
        }
    }

}