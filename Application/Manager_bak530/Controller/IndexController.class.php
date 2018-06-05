<?php
namespace Manager\Controller;

/**
 * Class IndexController
 * @package Manager\Controller
 * 首页控制器
 */
class IndexController extends BaseController {


    public function index() {
        if(empty($_POST['start_time']) &&empty($_POST['end_time'])){
            $start_time = strtotime(date('Y-m-d'))-9*86400;
            $end_time = strtotime(date('Y-m-d'));
        }else if($_POST['start_time'] && empty($_POST['end_time'])){
            $start_time = strtotime($_POST['start_time']);
            $end_time =  strtotime(date('Y-m-d'));
        }else if($_POST['end_time'] && empty($_POST['start_time'])){
            $end_time = strtotime($_POST['end_time']);
            $start_time = $end_time-9*86400;
        }else{
            $start_time = strtotime($_POST['start_time']);
            $end_time = strtotime($_POST['end_time']);
        }
        $this -> registerList($start_time,$end_time);

        $this->display('index');
    }
    // 注册
    public function registerList($start_time,$end_time)
    {
        for($i = $start_time;$i<=$end_time;$i = $i+86400){
            $xAxis[] = date('Y-m-d',$i);
            $where['create_time'] = array(array('egt',$i),array('elt',$i+86400-1),'AND');
            $yAxis[0]['data'][] = (int)(M('Member')->where($where)->count());
            unset($where);
        }
        $yAxis[0]['name'] = '日注册用户';
        $this->assign('xAxis',json_encode($xAxis));
        $this->assign('yAxis',json_encode($yAxis));
    }
}
