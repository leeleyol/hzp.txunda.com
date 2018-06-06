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
     * 初始化方法
     */
    public $member_obj = '';
    public $sms_obj    = '';
    //public $msg_obj    = '';
    public $file_obj   = '';
    public function _initialize()
    {
        parent::_initialize();
        $this->member_obj = D('Member');
        $this->sms_obj    = D('Sms');
        //$this->msg_obj    = D('Msg');
        $this->file_obj   = D('File');
    }


    /**
     * 兴趣圈首页
     */
    public function interestIndex(){
        $this->display('interestIndex');
    }



    /**
     *
     * 添加帖子
     *
     */
    public function addInterest(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['title'],'condition'=>'','error_msg'=>'标题参数错误'),
            array('check_type'=>'is_null','parameter' => $request['content'],'condition'=>'','error_msg'=>'内容参数错误'),
            array('check_type'=>'is_null','parameter' => $request['ins_type'],'condition'=>'','error_msg'=>'分类参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $data = [
            'm_id'=>$m_id,
            'title'=>$request['title'],
            'content'=>$request['content'],
            'pic'=>$request['pic'] ? $request['pic'] : '',
            'create_time'=>time(),
            'ins_type'=>$request['ins_type'],
        ];
        $res = M('Interest')->data($data)->add();
        if($res){
            apiResponse('1','发布成功');
        }else{
            apiResponse('0','发布失败');
        }
    }


    /**
     * @param array $request
     * 分类列表
     * 传递参数的方式：post
     * 需要传递的参数：
     * m_id 用户id
     */
    public function typeList(){
        $list = M('InterestType')->where(['status'=>1])->order('create_time desc')->field('id as ins_type,pic,type_name')->select();
        foreach($list as $k=>$v){
            $list[$k]['pic'] = returnImage($v['pic']);
        }
        apiResponse('1','请求成功',$list?$list:[]);
    }


}