<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 兴趣圈
 * Class InterestController
 * @package Home\Controller
 */
class InterestController extends BaseController{

    /**
     * 兴趣圈首页
     */
    public function interestIndex(){
        $this->display('interestIndex');
    }
}