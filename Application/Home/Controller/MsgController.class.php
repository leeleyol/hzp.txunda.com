<?php
namespace Home\Controller;

/**
 * 消息模块
 * Class MsgController
 * @package Api\Controller
 */
class MsgController extends BaseController{
    /**
     * 初始化方法
     */
    public $msg_obj = '';
    public $member_obj ='';
    public function _initialize()
    {
        parent::_initialize();
        $this->msg_obj = D('Msg');
        $this->member_obj = D('Member');
    }

    /**
     * 消息首页
     */
    public function msgIndex(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $result_data = array();
        $sys_msg_info = M('Msg')->where(array('type'=>1,'status'=>array('neq',9)))->order('create_time desc')->find();
        if($sys_msg_info){
            $result_data['sys_msg_title'] = $sys_msg_info['sys_msg_title'];
            $result_data['sys_msg_time'] = date('Y-m-d',$sys_msg_info['create_time']);
        }else{
            $result_data['sys_msg_title'] = '';
            $result_data['sys_msg_time'] = '';
        }
        $sys_msg_info = M('Msg')->where(array('type'=>2,'m_id'=>$m_id))->order('create_time desc')->find();
        if($sys_msg_info){
            $result_data['post_msg_content'] = $sys_msg_info['sys_msg_title'];;
            $result_data['post_msg_time'] = date('Y-m-d',$sys_msg_info['create_time']);
        }else{
            $result_data['post_msg_content'] = '';
            $result_data['post_msg_time'] = '';
        }
        $chat = [];
        /*$chat[0]['from_mid'] =2;
        $chat[0]['from_m_type'] = "0";
        $chat[0]['from_head_pic'] = "";
        $chat[0]['create_time'] = "";
        $chat[0]['nickname'] = "";
        $chat[0]['content'] = "";
        $chat[0]['is_read'] = "0";*/

        $chat = M('Chat')->alias('c')->join('db_member m on m.id=c.from_mid  ','LEFT')->where(['c.m_id'=>$m_id,'is_first'=>1])->field('c.*,m.head_pic,m.nickname,m.type from_m_type')->group('from_mid')->select();

        $chat1 = M('Chat')->alias('c')->join('db_member m on m.id=c.m_id  ','LEFT')->where(['c.from_mid'=>$m_id,'is_first'=>1])->field('c.*,m.head_pic,m.nickname,m.type from_m_type')->group('m_id')->select();

        foreach($chat1 as $k1=>$v1){
            $chat1[$k1]['from_mid'] = $v1['m_id'];
        }
        $chat2 = array_merge($chat,$chat1);
        foreach ($chat2 as $k=>$v){
            $chat2[$k]['from_head_pic'] = returnImage($v['head_pic']);
        }

        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'create_time',       //排序字段
        );
        $arrSort = array();
        foreach($chat2 AS $uniqid => $row){
            foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $chat2);
        }
        $result_data['chat_list'] = $chat2;

        /*$string = "c.m_id = $m_id OR from_mid = $m_id";
        $chat = M('Chat')->alias('c')->join('db_member m on m.id=c.from_mid  ','LEFT')->where(['_string'=>$string,'is_first'=>1])->field('c.*,m.head_pic,m.nickname,m.type from_m_type')->group('from_mid')->select();
        foreach ($chat as $k=>$v){
            $chat[$k]['from_head_pic'] = returnImage($v['head_pic']);
        }*/

       /* $sql = "(SELECT c.*,m.head_pic,m.nickname,m.type from_m_type FROM db_chat c LEFT JOIN db_member m on m.id=c.from_mid  WHERE c.m_id = ".$m_id." AND `is_first` = 1 GROUP BY from_mid ) OR ( SELECT c.*,m.head_pic,m.nickname,m.type from_m_type FROM db_chat c LEFT JOIN db_member m on m.id=c.m_id WHERE c.from_mid =". $m_id." AND `is_first` = 1 GROUP BY m_id)";
        $data = M()->query($sql);
        foreach ($chat2 as $k=>$v){
            $chat2[$k]['from_head_pic'] = returnImage($v['head_pic']);
            if($v['from_mid']==$m_id){
                $data[$k]['from_mid'] = $v['m_id'];
            }
        }
        $result_data['chat_list'] = $data;*/
        apiResponse('1','请求成功',$result_data);
    }

    /**
     * 消息列表
     * 分页参数：p
     */
    public function systemMsgList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' =>$request['p'],'error_msg'=>'参数错误'),
        );
        check_param($param);//检查参数
        $where = [
            'type'=>$request['type'],
            'status'=>1
        ];
        if($request['type']==2){
            $where['m_id'] = $m_id;
        }
        $list = M('Msg')->where($where)->order('create_time desc')
            ->field('id as sys_msg_id,sys_msg_title,sys_msg_content,create_time')
            ->page($request['p'].',15')
            ->select();
        if(empty($list)){
            $message = $request['p']==1?'暂无消息':'无更多消息';
            apiResponse('0',$message);
        }
        foreach($list as $k => $v){
            $list[$k]['sys_msg_content'] = $this->setAbsoluteUrl($v['sys_msg_content']);
        }
        apiResponse('1','请求成功',$list);
    }

    /**
     * 系统消息详情
     * 消息id sys_msg_id
     */
    public function systemMsgInfo(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' =>$request['sys_msg_id'],'error_msg'=>'参数错误'),
        );
        check_param($param);//检查参数
        $info = M('Msg')->where(array('id'=>$request['sys_msg_id']))
            ->field('id as sys_msg_id,sys_msg_title,sys_msg_content,create_time')
            ->find();
        if(empty($info)){
            apiResponse('0','查询失败');
        }
        $info['sys_msg_content'] = $this->setAbsoluteUrl($info['sys_msg_content']);

        $res = M('msg_read_log')->where(array('m_id'=>$m_id,'msg_id'=>$info['sys_msg_id']))->find();
        if(empty($res)){
            M('MsgReadLog')->data(array('m_id'=>$m_id,'msg_id'=>$info['sys_msg_id'],'create_time'))->add();
        }
        apiResponse('1','请求成功',$info);
    }

    /**
     * 我的消息列表
     * 参数：p
     */
    public function myMsgList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' =>$request['p'],'error_msg'=>'参数错误'),
        );
        check_param($param);//检查参数
        $list = M('Msg')->where(array('type'=>2,'m_id'=>$m_id))->order('create_time desc')
            ->field('id as msg_id,order_msg_content,create_time')
            ->page($request['p'].',15')
            ->select();
        if(empty($list)){
            $message = $request['p']==1?'暂无消息':'无更多消息';
            apiResponse('0',$message);
        }
        foreach($list as $k => $v){
            $res = M('msg_read_log')->where(array('m_id'=>$m_id,'msg_id'=>$v['msg_id']))->find();
            if(empty($res)){
                M('MsgReadLog')->data(array('m_id'=>$m_id,'msg_id'=>$v['msg_id'],'create_time'))->add();
            }
        }
        apiResponse('1','请求成功',$list);
    }


    function setAbsoluteUrl($content){
        preg_match_all('/src=\"\/?(.*?)\"/',$content,$match);
        foreach($match[1] as $key => $src){
            if(!strpos($src,'://')){
                $content = str_replace('/'.$src,C('API_URL')."/".$src."\" width=100%",$content);
            }
        }
        return $content;
    }
}