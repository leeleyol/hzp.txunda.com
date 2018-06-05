<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     */
    public function index(){
        $this->display('index');
    }

    /**
     * 搜索
     */
    public function search(){
        $this->display('search');
    }
}