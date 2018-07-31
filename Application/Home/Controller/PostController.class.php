<?php
namespace Home\Controller;
/**
 * Class MemberLogLogic
 * @package Api\Logic
 * 社区逻辑层
 */
class PostController extends BaseController{

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
     * 分类列表
     * 传递参数的方式：post
     * 需要传递的参数：
     * p 分页参数
     * m_id 用户id
     */
    public function typeList(){
        $list = M('PostType')->where(['status'=>1])->order('create_time desc')->field('id as type_id,pic,type_name')->select();
        foreach($list as $k=>$v){
            $list[$k]['pic'] = returnImage($v['pic']);
            $num = M('Post')->where(['type_id'=>$v['type_id'],'status'=>1])->count('id');
            $list[$k]['num'] = $num?$num:"0";
        }
        apiResponse('1','请求成功',$list?$list:[]);
    }




    /**
     * 用户发帖
     * 传递参数的方式：post
     * 需要传递的参数：
     * 用户id m_id
     * type_id 分类id
     * title 标题
     * content 内容
     * pic 图片
     */
    public function addPost(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['title'],'condition'=>'','error_msg'=>'标题参数错误'),
            array('check_type'=>'is_null','parameter' => $request['content'],'condition'=>'','error_msg'=>'内容参数错误'),
            array('check_type'=>'is_null','parameter' => $request['type_id'],'condition'=>'','error_msg'=>'分类参数错误')
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数
        $isCanPost = isCanPost($m_id);
        if(!$isCanPost){
            apiResponse('0','已达到今日发布上限');
        }
        $data = [
            'm_id'=>$m_id,
            'title'=>$request['title'],
            'content'=>$request['content'],
            'pic'=>$request['pic'] ? $request['pic'] : '',
            'create_time'=>time(),
            'type_id'=>$request['type_id'],
        ];
        $res = M('Post')->data($data)->add();
        if($res){
            apiResponse('1','发布成功');
        }else{
            apiResponse('0','发布失败');
        }
    }



    /**
     *  社区首页
     * 传递参数的方式：post
     * 需要传递的参数：
     * 用户id m_id
     * p 分页
     */
    public function Index(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
        );
        check_param($param);//检查参数
        $where['p.status'] = 1;
        $order = 'p.create_time desc';
        $result['post_list'] = $this->getData($where,$request['p'],$order);
        if(!$result['post_list']){
            $message = $request['p']==1?'暂无相关帖子':'无更多帖子';
        }else{
            $message = '获取成功';
        }
        $result['msg_num'] = "0";
        apiResponse('1',$message,$result);




    }


    /**
     *  分类下的帖子
     * 传递参数的方式：post
     * 需要传递的参数：
     * 用户id m_id
     * 分类id type_id
     * p 分页
     */
    public function selectTypePost(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
            array('check_type'=>'is_null','parameter' => $request['type_id'],'condition'=>'','error_msg'=>'分类id参数错误'),
        );
        $where['m_id'] = $m_id;
        check_param($param);//检查参数

        $where['p.type_id'] = $request['type_id'];
        $where['p.status'] = 1;
        $order = 'p.create_time desc';

        $result = $this->getData($where,$request['p'],$order);
        if(!$result){
            $message = $request['p']==1?'暂无相关帖子':'无更多帖子';
        }else{
            $message = '获取成功';
        }
        apiResponse('1',$message,$result);


    }


    /*
     * 获取帖子方法转化
     * */
    public function getData($where,$p,$order){
        $page_size = $p?10:3;
        $list = M('Post')
            ->alias('p')
            ->where($where)
            ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = p.m_id')
            ->join('LEFT JOIN '.C('DB_PREFIX').'post_type pt ON pt.id=p.type_id')
            ->order($order)
            ->field('p.*,m.nickname,m.head_pic,pt.type_name')
            ->page($p.','.$page_size)
            ->select();
        foreach($list as $k2=>$v2){
            $list[$k2]['head_pic'] = returnImage($v2['head_pic']);
            $pic = returnPostImage($v2['pic']);
            $list[$k2]['pic_list'] = $pic;
            unset($pic);
        }
        return$list;
    }





    /*
     * 添加评论
     * @param array $request
     * 传递参数的方式：post
     * 需要传递的参数：
     * m_id 用户id
     * post_id 帖子id
     * comment 内容
     * */
    public function addComment(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        empty($_POST['post_id'])&&apiResponse('0','帖子id不能为空');
        empty($_POST['comment'])&&apiResponse('0','内容不能为空');
        $data = [
            'from_mid'=>$m_id,
            'comment'=>$_POST['comment'],
            'post_id'=>$_POST['post_id'],
            'create_time'=>time(),
            'status'=>1,
        ];
        $add = M('PostComment')->data($data)->add();
        if($add){

            /*$memberModel = new \Api\Model\MemberModel();
            $memberModel->addExperience($request['m_id'],1);*/

            M('Post')->where(['id'=>$_POST['post_id']])->setInc('comment_num',1);
            M('Post')->where(['id'=>$_POST['post_id']])->data(['update_time'=>time()])->save();
            apiResponse('1','评论成功');
        }else{
            apiResponse('0','评论失败');
        }

    }

    /*
     * 添加评论
     * @param array $request
     * 传递参数的方式：post
     * 需要传递的参数：
     * 用户id：m_id
     * 内容：reply_content
     * 评论 comment_id
     * */
    public function addReply(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        empty($_POST['m_id'])&&apiResponse('0','用户id不能为空');
        empty($_POST['reply_content'])&&apiResponse('0','内容不能为空');
        empty($_POST['comment_id'])&&apiResponse('0','评论id不能为空');
        $to_mid = M('PostComment')->where(['id'=>$_POST['comment_id']])->getField('from_mid');
        $data = [
            'from_mid'=>$m_id,
            'reply_content'=>$_POST['reply_content'],
            'comment_id'=>$_POST['comment_id'],
            'create_time'=>time(),
            'to_mid'=>$to_mid,
        ];
        $add = M('PostReply')->data($data)->add();
        if($add){
            apiResponse('1','添加回复成功');
        }else{
            apiResponse('0','添加回复失败');
        }

    }










    /*
     * 帖子详情
     * @param array $request
     * 传递参数的方式：post
     * 需要传递的参数：
     * m_id 用户id
     * post_id 帖子id
     * */
    public function postInfo(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        empty($_POST['post_id'])&&apiResponse('0','帖子id不能为空');
        $where['p.id'] = $_POST['post_id'];
        $where['p.status'] = 1;
        $info = $this->getData($where,0,'p.create_time desc');
        $data = $info[0];
        M('Post')->where(['id'=>$_POST['post_id']])->setInc('view',1);
        if($m_id){
            $data['is_collect'] = M('Collect')->where(['object_type'=>2,'object_id'=>$_POST['post_id'],'m_id'=>$m_id])->find()?1:0;
        }else{
            $data['is_collect'] = 0;
        }
        apiResponse('1','成功',$data);

    }



    /*
     * 评论列表
     * @param array $request
     * 传递参数的方式：post
     * 需要传递的参数：
     * post_id 帖子id
     * p 分页
     * m_id
     * */
    public function commentList(){
        $m_id = $this->member_obj->checkToken();
        $this->member_obj->errorTokenMsg($m_id);
        empty($_POST['post_id'])&&apiResponse('0','帖子id不能为空');
        $where['pc.post_id'] = $_POST['post_id'];
        $where['pc.status'] = 1;
        //评论信息
        $list = M('PostComment')
            ->alias('pc')
            ->where($where)
            ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = pc.from_mid')
            ->order('pc.create_time desc')
            ->field('pc.*,m.nickname,m.head_pic')
            ->page($_POST['p'].',10')
            ->select();
        if($list){
            foreach($list as $k2=>$v2){
                $list[$k2]['head_pic'] = returnImage($v2['head_pic']);
                unset($pic);
                //查询子回复
                $list[$k2]['reply_num'] = M('PostReply')->where(['comment_id'=>$v2['id']])->count();
                $reply_list = M('PostReply')
                    ->alias('pr')
                    ->where(['pr.comment_id'=>$v2['id']])
                    ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = pr.from_mid')
                    ->order('pr.create_time desc')
                    ->field('pr.*,m.nickname,m.head_pic')
                    ->limit(2)
                    ->select();
                foreach ($reply_list as $k3=>$v3){
                    $reply_list[$k3]['head_pic'] = returnImage($v3['head_pic']);
                }
                $list[$k2]['reply_list'] = $reply_list?$reply_list:[];
            }
        }
        if(!$list){
            $message = $_POST['p']==1?'暂无评论':'无更多评论';
        }else{
            $message = '获取成功';
        }
        apiResponse('1',$message,$list);
    }

    /*
     * 评论详情
     * @param array $request
     * 传递参数的方式：post
     * 需要传递的参数：
     * post_id 帖子id
     * p 分页
     * m_id
     * */
    public function commentInfo($request = array()){
        empty($request['comment_id'])&&apiResponse('0','评论id不能为空');
        $m_id = $request['m_id'];
        $where['pc.id'] = $request['comment_id'];
        $where['pc.status'] = 1;
        //信息
        $info = M('PostComment')
            ->alias('pc')
            ->where($where)
            ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = pc.from_mid')
            ->join( 'LEFT JOIN '.C('DB_PREFIX').'post p ON p.id = pc.post_id')
            ->order('pc.create_time desc')
            ->field('pc.*,m.nickname,m.head_pic,m.age,m.sex,m.degree,m.city_id,m.province_id,p.title,p.pic')
            ->page($request['p'].',10')
            ->find();
        $reply_list = [];
        if($info){
            $pic = returnElseImage($info['pic']);
            $info['pic'] = $pic?$pic[0]:'';
            $info['head_pic'] = returnImage($info['head_pic']);
            $info['city_name'] = returnRegionName($info['city_id']);
            $info['province_name'] = returnRegionName($info['province_id']);
            $info['reply_num'] = M('PostReply')->where(['comment_id'=>$request['comment_id']])->count();
            unset($pic);
            //查询子回复
            $reply_list = M('PostReply')
                ->alias('pr')
                ->where(['pr.comment_id'=>$request['comment_id']])
                ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = pr.from_mid')
                ->order('pr.create_time desc')
                ->field('pr.*,m.nickname,m.head_pic,m.age,m.sex,m.degree,m.city_id,m.province_id')
                ->page($request['p'].',10')
                ->select();
            if($reply_list){
                foreach($reply_list as $k1=>$v1){
                    $reply_list[$k1]['head_pic'] = returnImage($v1['head_pic']);
                    $reply_list[$k1]['city_name'] = returnRegionName($v1['city_id']);
                    $reply_list[$k1]['province_name'] = returnRegionName($v1['province_id']);
                }
            }
            $info['reply_list'] = $reply_list?$reply_list:[];
        }
        if(!$reply_list){
            $message = $request['p']==1?'暂无回复':'无更多回复';
        }else{
            $message = '获取成功';
        }
        apiResponse('1',$message,$info);
    }


    /**
     *  查询某人的发帖纪录
     * 传递参数的方式：post
     * 需要传递的参数：
     * to_mid 浏览对象id
     */
    public function memberPostList(){
        $where['p.m_id'] = $_POST['to_mid'];
        $where['p.status'] = 1;
        $order = 'p.create_time desc';

        $result = $this->getData($where,$_POST['p'],$order);
        if(!$result){
            $message = $_POST['p']==1?'暂无相关帖子':'无更多帖子';
        }else{
            $message = '获取成功';
        }
        apiResponse('1',$message,$result);

    }





    /**
     * @param array $request
     *  发布视频
     * 传递参数的方式：post
     * 需要传递的参数：
     * video
     */
    public function uploadVideo($request = array()){
        $video = '';
        if($_FILES){
            $res = api('UploadPic/upload',array(array('save_path'=>'Post')));
            foreach($res as $value){
                $video .= $value['id'].',';
            }
            $data['video'] = substr($video,0,-1);

            apiResponse('1','成功',$data);
        }else{
            apiResponse('1','失败');
        }

    }




}