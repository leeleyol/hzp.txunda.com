<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 关注
 * Class FollowController
 * @package Home\Controller
 */
class FollowController extends BaseController{

    /**
     * 我的关注
     */
    public function myFollow(){
        $this->display('myFollow');
    }
}