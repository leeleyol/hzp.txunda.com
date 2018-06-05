<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 收藏
 * Class CollectController
 * @package Home\Controller
 */
class CollectController extends BaseController{

    /**
     * 我的关注
     */
    public function myCollect(){
        $this->display('myCollect');
    }
}