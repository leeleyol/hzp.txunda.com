<?php

namespace Manager\Controller;

/**
 * 搜索关键词
 * Class SearchWordsController
 * @package Manager\Controller
 */
class SearchWordsController extends BaseController {

    public function getAddRelation()
    {
        $this->assign('select',M('search_words')->where(array('status'=>array('neq',9)))->select());
    }

    public function getUpdateRelation()
    {
        $this->assign('select',M('search_words')->where(array('status'=>array('neq',9)))->select());
    }
}
