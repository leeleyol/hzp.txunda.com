<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 消息
 * Class MsgController
 * @package Home\Controller
 */
class MsgController extends BaseController{

    /**
     * 消息首页
     */
    public function msgIndex(){
        $this->display('msgIndex');
    }
}