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
        /*session('openid','');
        $_GET['code'] = 0;*/
        if(!session('openid') && empty($_GET['code'])){
            $url1 = urlencode("http://hzp.txunda.com/index.php/Home/Index/index");
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxcc14df2cb856bd3f&redirect_uri=".$url1."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            Header("Location: $url");
            exit();
        }else{
            if(!session('openid')){
                Vendor('WxpayApi.lib.WxPay#Api');
                Vendor('WxpayApi.WxPay#JsApiPay');
                //①、获取用户openid
                $tools = new \jsApiPay();
                $openId = $tools->GetOpenidFromMp($_GET['code']);
                session('openid',$openId);
                $userInfo = M('Member')->where(['openid'=>$openId])->find();
                if($userInfo){
                    session('m_id',$userInfo['id']);
                }else{
                    $data = [
                        'openid'=>$openId,
                        'type'=>0,
                        'create_time'=>time(),
                        'status'=>1
                    ];
                    $add_res = M('Member')->add($data);
                    session('m_id',$add_res);
                }
            }
        }

        /*if($this->is_weixin())
        {
            $this->getCodes();
        }*/
        $this->display('index');
    }


    /**
     * 获取微信授权信息并注册
     */
    public function getCodes() {
        $openid = session('openid');
        if(!$openid || empty($openid)) {
            Vendor('WxpayApi.lib.WxPay#Api');
            Vendor('WxpayApi.WxPay#JsApiPay');
            $tools = new \JsApiPay();
            $openid = $tools->GetOpenid();
            session('openid', $openid);

            //  $this->ajaxReturn(json_encode(array('openId'=>$openId)));
        }
    }
    function is_weixin(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
    /**
     * 搜索
     */
    public function search(){
        $this->display('search');
    }


    /*
     * 获取首页数据
     * */
    public function getIndexData(){
        #首页轮播
        $advert = M('Advert')->where(['status'=>1])->field('id,ad_position,ad_desc,ad_pic')->order('sort asc')->select();
        foreach ($advert as &$col){
            $col['ad_pic_path'] = returnImage($col['ad_pic']);
        }
        $data['advert'] = $advert ? $advert : [];
        #首页推荐商家
        $merchant_list = M('Member')->where(['status'=>1,'is_recommend'=>1,'type'=>['neq',0]])->field('id m_id,head_pic,attention_num,nickname')->order('create_time asc')->limit('5')->select();
        foreach ($merchant_list as &$value){
            $value['head_pic_path'] = returnImage($value['head_pic']);
        }
        $data['merchant_list'] = $merchant_list ? $merchant_list : [];
        #获取供求
        $where['s.status'] = 1;
        $supply_list = D('Supply')->getList($where,'');
        foreach ($supply_list as $k=>$v){
            $info  = json_decode($v['supply_info'],true);
            $index = [];
            foreach ($info as $k1=>$v1){
                $goods_info = M('Goods')->alias('g')->join('db_goods_type gt on gt.id=g.goods_type_id','left')->where(['g.id'=>$v1['goods_id']])->field('g.goods_name,g.goods_type_id,g.stock,g.stock_unit,g.goods_status,gt.type_name,g.goods_pic')->find();
                $index[$k1]['goods_id'] = $v1['goods_id'];
                $index[$k1]['goods_type_name'] = $goods_info['type_name'];
                $index[$k1]['goods_type_id'] = $goods_info['goods_type_id'];
                $index[$k1]['goods_name'] = $goods_info['goods_name'];
                $index[$k1]['stock'] = $goods_info['stock'];
                $index[$k1]['stock_unit'] = $goods_info['stock_unit'];
                $index[$k1]['goods_pic_path'] = returnImage($goods_info['goods_pic']);
                $index[$k1]['goods_status'] = $goods_info['goods_status'];
                $index[$k1]['goods_price'] = $v1['goods_price'];
            }
            $supply_list[$k]['supply_info'] = $index;
            $supply_list[$k]['head_pic_path'] = returnImage($v['head_pic'],'');
            $supply_list[$k]['pic'] = $v['pic']?returnSupplyImageTwo($v['pic'],''):[];
            unset($index);unset($info);
        }
        $data['supply_list'] = $supply_list;
        #获取帖子
        $where_post['p.status'] = 1;
        $post_list = M('Post')
            ->alias('p')
            ->where($where_post)
            ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = p.m_id')
            ->join('LEFT JOIN '.C('DB_PREFIX').'post_type pt ON pt.id=p.type_id')
            ->order('p.create_time desc')
            ->field('p.*,m.nickname,m.head_pic,pt.type_name')
            ->limit(3)
            ->select();
        foreach($post_list as $k2=>$v2){
            $post_list[$k2]['head_pic'] = returnImage($v2['head_pic']);
            $pic = returnPostImage($v2['pic']);
            $post_list[$k2]['pic_list'] = $pic?$pic:[];
            unset($pic);
        }
        $data['post_list'] = $post_list ? $post_list : [];
        $data['msg_num'] = "0";
        apiResponse('1','获取数据成功',$data);
    }

    /*
     *搜索供求
     * */
    public function searchSupply(){
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
            array('check_type'=>'is_null','parameter' => $request['keyword'],'condition'=>'','error_msg'=>'关键字参数错误'),
        );
        check_param($param);//检查参数
        $where['s.description'] = ['like','%'.$request['keyword'].'%'];
        $where['s.status'] = 1;
        $supply_list = D('Supply')->getList($where,$request['p']);
        foreach ($supply_list as $k=>$v){
            $info  = json_decode($v['supply_info'],true);
            $index = [];
            foreach ($info as $k1=>$v1){
                $goods_info = M('Goods')->alias('g')->join('db_goods_type gt on gt.id=g.goods_type_id','left')->where(['g.id'=>$v1['goods_id']])->field('g.goods_name,g.goods_type_id,g.stock,g.stock_unit,g.goods_status,gt.type_name,g.goods_pic')->find();
                $index[$k1]['goods_id'] = $v1['goods_id'];
                $index[$k1]['goods_type_name'] = $goods_info['type_name'];
                $index[$k1]['goods_type_id'] = $goods_info['goods_type_id'];
                $index[$k1]['goods_name'] = $goods_info['goods_name'];
                $index[$k1]['stock'] = $goods_info['stock'];
                $index[$k1]['stock_unit'] = $goods_info['stock_unit'];
                $index[$k1]['goods_pic_path'] = returnImage($goods_info['goods_pic']);
                $index[$k1]['goods_status'] = $goods_info['goods_status'];
                $index[$k1]['goods_price'] = $v1['goods_price'];
            }
            $supply_list[$k]['supply_info'] = $index;
            $supply_list[$k]['head_pic_path'] = returnImage($v['head_pic'],'');
            $supply_list[$k]['pic'] = $v['pic']?returnSupplyImageTwo($v['pic']):[];
            unset($index);unset($info);
        }
        if(!$supply_list){
            $message = $request['p']==1?'暂无相关供求':'无更多供求';
        }else{
            $message = '获取成功';
        }
        apiResponse('1',$message,$supply_list?$supply_list:[]);
    }


    /*
     *搜索帖子
     * */
    public function searchPost(){
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
            array('check_type'=>'is_null','parameter' => $request['keyword'],'condition'=>'','error_msg'=>'关键字参数错误'),
        );
        check_param($param);//检查参数
        $where_post['p.title'] = ['like','%'.$request['keyword'].'%'];
        $where_post['p.status'] = 1;
        $post_list = M('Post')
            ->alias('p')
            ->where($where_post)
            ->join( 'LEFT JOIN '.C('DB_PREFIX').'member m ON m.id = p.m_id')
            ->join('LEFT JOIN '.C('DB_PREFIX').'post_type pt ON pt.id=p.type_id')
            ->order('p.create_time desc')
            ->field('p.*,m.nickname,m.head_pic,pt.type_name')
            ->limit($request['p'].',10')
            ->select();
        foreach($post_list as $k2=>$v2){
            $post_list[$k2]['head_pic'] = returnImage($v2['head_pic']);
            $pic = returnPostImage($v2['pic']);
            $post_list[$k2]['pic_list'] = $pic;
            unset($pic);
        }

        if(!$post_list){
            $message = $request['p']==1?'暂无相关帖子':'无更多帖子';
        }else{
            $message = '获取成功';
        }
        apiResponse('1',$message,$post_list?$post_list:[]);
    }


    /*
     *搜索商家
     * */
    public function searchMember(){
        $request = I('post.');
        $param = array(
            array('check_type'=>'is_null','parameter' => $request['p'],'condition'=>'','error_msg'=>'分页参数错误'),
            array('check_type'=>'is_null','parameter' => $request['keyword'],'condition'=>'','error_msg'=>'关键字参数错误'),
        );
        check_param($param);//检查参数
        $where['m.nickname'] = ['like','%'.$request['keyword'].'%'];
        $where['m.status'] = 1;
        $where['m.type'] = ['in','1,2'];
        $list = M('Member')->alias('m')
            ->join('db_member_info mi on mi.m_id=m.id','left')
            ->where($where)
            ->field('m.id as member_id,m.nickname,m.head_pic,m.attention_num,m.type,mi.company_address,mi.phone')
            ->order('create_time desc')
            ->page($request['p'].',10')
            ->select();
        if(empty($list)){
            $message =$request['p']==1?'暂无记录':'无更多记录';
            apiResponse('0',$message);
        }
        foreach ($list as $k=>$v){
            $list[$k]['head_pic_path'] = returnImage($v['head_pic']);
            $list[$k]['goods_num'] = getMemberGoodsNum($v['member_id']);
            $list[$k]['need_num'] = getMemberSupplyNum($v['member_id']);
        }
        apiResponse('1','请求成功',$list);

    }




    public function getOpenid() {
        $appid = 'wx637f16d7e2ac1966';
        $secret = '85d92a5517e28824d3091401cede1297';
        $js_code = $_REQUEST['js_code'];
        if(empty($js_code)) {
            apiResponse(0, '缺少code');
        }
        $url = " https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$js_code&grant_type=authorization_code";

        $openid = file_get_contents($url);
        //var_dump($openid);die;
        $openid=json_decode($openid);
        $session_key = $openid->session_key;
        $openid = $openid->openid;

        //$openid = !empty($_REQUEST['openid']) ? $_REQUEST['openid'] : '';

        //var_dump($openid);die;
        if(empty($openid)) {
            apiResponse(0, '缺少openid');
        }
        //apiResponse(1, '成功', $openid);
        // 判断用户是否存在
        $userInfo = M('User')->where(array('openid'=>$openid))->field('is_auth, id AS id')->find();
       // var_dump($userInfo);die;
        if($userInfo) {
            apiResponse(1, '成功', $userInfo['id']);
        }else { // 注册账户
            // 上传头像
            //$_file = array('abs_url'=>$_GET['head_pic'], 'create_time'=>time());
            //$head_pic = M('File')->add($_file);

            $data = array(
                'openid'=>$openid,
                'create_time' => time(),
                'is_auth' => 0,
                'status' => 1
            );
            $res = M('User')->add($data);
            $user_id = M('User')->getLastInsID();
            $userInfo = array(
                'user_id' => $user_id,
                'is_auth' => 0
            );
            if(!$res)  {

            }else {
                apiResponse(1, '成功', $userInfo['user_id']);
            }

        }
       // echo $res;
    }


    public function getSessionMid(){
        if(session('m_id')){
            $m_id = session('m_id');
        }else{
            $m_id = 0;
        }
        apiResponse('200','获取成功',$m_id);
    }


}