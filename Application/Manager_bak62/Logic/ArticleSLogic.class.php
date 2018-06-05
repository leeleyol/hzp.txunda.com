<?php
namespace Manager\Logic;

/**
 * Class ArticleLogic
 * @package Manager\Logic
 */
class ArticleSLogic extends BaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取文章列表
     */
    function getList($request = array()) {
        $param['where']['status'] = 1;
        //排序
        $param['order'] = 'create_time DESC';
        $param['page_size'] = 15;
        //返回数据
        $list = D('ArticleS')->getList($param);
        return $list;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     */
    protected function processData($data = array(), $request = array()) {
        $data['content'] = $_POST['content'];
        $data['intro'] = $_POST['intro'];
        return $data;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicError('参数错误！'); return false;
        }
        $row = D('ArticleS')->findRow($param);

        if(!$row) {
            $this->setLogicError('未查到此记录！'); return false;
        }
        //返回数据
        return $row;
    }

}