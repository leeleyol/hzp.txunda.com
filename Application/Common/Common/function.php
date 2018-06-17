<?php
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */
/**
 * @param int $flag 0数字字符混合 1字符 2数字
 * @param int $num 验证标识的个数
 * @return string
 */
function get_vc($num = 0, $flag = 0) {
    /**获取验证标识**/
    $arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',1,2,3,4,5,6,7,8,9,0);
    $vc  = '';
    switch($flag) {
        case 0 : $s = 0;  $e = 61; break;
        case 1 : $s = 0;  $e = 51; break;
        case 2 : $s = 52; $e = 61; break;
    }

    for($i = 0; $i < $num; $i++) {
        $index = rand($s, $e);
        $vc   .= $arr[$index];
    }
    return $vc;
}

/**
 * 检查是否注册
 * @param $account
 * @return bool
 */
function checkIsRegister($account){
    $where['account'] = $account;
    $where['status']  = array('neq',9);
    $count = M('Member')->where($where)->count();
    if($count>0){
        return true;
    }else{
        return false;
    }
}

/**
 * 验证手机号是否正确
 * @param $mobile
 * @return bool
 */
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match(C('MOBILE'), $mobile) ? true : false;
}

/**
 * 检查参数是否完整
 * @param $param  check_type:is_null（判空）,in_array(是否在待选值内)，regex(正则验证)
 */
function check_param($param){
    $error_msg = '';
    foreach($param as $k => $v){
        //判定是否为空
        if($v['check_type'] == 'is_null'){
            if(empty($v['parameter'])){
                $error_msg = $v['error_msg'];break;
            }
        }
        //判定是否在所需的条件内
        if($v['check_type'] == 'in_array'){
            if(!in_array($v['parameter'],$v['condition'])){
                $error_msg = $v['error_msg'];break;
            }
        }
        //正则判定
        if($v['check_type'] == 'regex'){
            if(!preg_match($v['condition'], $v['parameter'])){
                $error_msg = $v['error_msg'];break;
            }
        }
    }
    //统一返回结果
    if($error_msg){
        apiResponse('0',$error_msg);
    }
}

/**
 * 生成订单编号
 * User: Vernon
 * @param:
 * Date: 2017-11-20
 * @return string
 */
function order_sn(){
    $order_sn = time().rand(10000,99999);
    return $order_sn;
}
/**
 * 将xml转为array
 * @param $xml
 * @return mixed
 */
function xmlToArray($xml){
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function str2arr($str, $glue = ',') {
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr2str($arr, $glue = ',') {
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param int $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param boolean $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function think_decrypt($data, $key = '') {
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param string $sort_by 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list, $field, $sort_by = 'asc') {
   if(is_array($list)) {
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sort_by) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int $root
 * @return array
 * 把返回的数据集转换成Tree
 */
function list_to_tree($list, $root = 0, $pk = 'id', $pid = 'parent_id', $child = '_child') {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            //以主键为键值的数组
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            //当前分类的父级分类是否等于父根节点
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    //当前分类的伤及分类 引用
                    $parent =& $refer[$parentId];
                    //存入上级分类的子分类中
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, &$list = array(), $child = '_child', $order = 'id'){
    if(is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $refer = $value;
            //是否有子分类
            if(isset($refer[$child])) {
                unset($refer[$child]);
                //递归
                tree_to_list($value[$child], $list, $child, $order);
            }
            $list[] = $refer;
        }
        //排序
        $list = list_sort_by($list, $order, $sort_by = 'asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 */
function set_redirect_url($url) {
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 */
function get_redirect_url() {
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook,$params=array()) {
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param string $name 插件名
 * @return mixed
 */
function get_plugin_class($name) {
    $class = "Plugins\\{$name}\\{$name}Plugin";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 * @return mixed
 */
function get_plugin_config($name) {
    $class = get_plugin_class($name);
    if(class_exists($class)) {
        $plugin = new $class();
        return $plugin->getConfig();
    } else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @return mixed
 */
function plugins_url($url, $param = array()) {
    $url        = parse_url($url);
    $case       = C('URL_CASE_INSENSITIVE');
    $plugins    = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if(isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        'plugins'    => $plugins,
        'controller' => $controller,
        'action'     => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Plugins/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function time_format($time = NULL,$format='Y-m-d H:i') {
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * @param $files
 * 基于数组创建目录和文件
 */
function create_dir_or_files($files) {
    foreach ($files as $key => $value) {
        if(substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

/**
 * 返回input数组中键值为$columnKey的列
 */
if(!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 * @return mixed
 */
function api($name,$vars = array()) {
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $class_name = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$class_name.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1) {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * @param array $by_array  按照该数组排序
 * @param array $list        要排序的列表
 * @param string $key_name  键值名称
 * @return array
 */
function sort_by_array($by_array, $list, $key_name = 'id') {
    if(empty($by_array))
        return $list;
    if(empty($list))
        return array();
    foreach ($list as $key => $data) {
        if(empty($data[$key_name]))
            return array();
        $refer[$data[$key_name]] =& $list[$key];
    }
    foreach($by_array as $val) {
        if(!empty($refer[$val])) {
            $sort_list[] = $refer[$val];
        }
    }
    return $sort_list;
}

/**
 * @param $str
 * @return string
 * 过滤掉html标签
 */
function filter_html($str) {
    return stripslashes(preg_replace("/(\<[^\<]*\>|\r|\n|\s|\&nbsp;|\[.+?\])/is", '', $str));
}

/**
 * @param $arr
 * @param $str
 * 去除数组中指定元素
 */
function remove_arr_str($arr,$str){
    foreach($arr as $key=>$value){
        if($value == $str){
            unset($arr[$key]);
        }
    }
    return $arr;
}

/**
 * 获取当前页面的url
 * @return string
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * curl 执行post的请求
 * @param $url
 * @param string $post_data
 * @param int $timeout
 * @return mixed
 */
function post($url, $post_data = '', $timeout = 5){
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_POST, 1);
    if($post_data != ''){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

/**
 *  API返回信息格式函数 ；0失败，1成功，-1需要登录
 * @param string $code
 * @param string $message
 * @param array $data
 */
function apiResponse($code = '0', $message = '',$data = array()){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type:application/json; charset=utf-8');
    $result = array(
        'code'=>$code,
        'message'=>$message,
        'data'=>$data,
    );
    die(json_encode($result,JSON_UNESCAPED_UNICODE));
}

function returnImage($image){
    $imageArr = explode(',',$image);
    if(count($imageArr)>1){
        foreach($imageArr as $k=>$v){
            $path = M('file')->where(['id'=>$v])->getField('path');
            $imageUrl[] = $path?C('API_URL').$path:C('API_URL').'/Uploads/Member/default.png';
        }
    }else{
        $path = M('file')->where(['id'=>$image])->getField('path');
        $imageUrl = $path?C('API_URL').$path:C('API_URL').'/Uploads/Member/default.png';
    }
  return $imageUrl?$imageUrl:"";

}


function returnPostImage($image){
    $imageArr = explode(',',$image);
    if(count($imageArr)>1){
        foreach($imageArr as $k=>$v){
            $path = M('file')->where(['id'=>$v])->getField('path');
            $imageUrl[] = $path?C('API_URL').$path:C('API_URL').'/Uploads/Member/default.png';
        }
    }else{
        $path = M('file')->where(['id'=>$image])->getField('path');
        $imageUrl[] = $path?C('API_URL').$path:C('API_URL').'/Uploads/Member/default.png';
    }
    return $imageUrl;

}


function returnSupplyImage($image){
    $imageArr = explode(',',$image);
    if(count($imageArr)>1){
        foreach($imageArr as $k=>$v){
            $path = M('file')->where(['id'=>$v])->getField('path');
            $imageUrl[$k]['picture_path'] = $path?C('API_URL').$path:C('API_URL').'/Uploads/Member/default.png';
            $imageUrl[$k]['picture_id'] = $v;
        }
    }elseif($image){
        $path = M('file')->where(['id'=>$image])->getField('path');
        $imageUrl[0]['picture_path'] = $path?C('API_URL').$path:C('API_URL').'/Uploads/Member/default.png';
        $imageUrl[0]['picture_id'] = $image;
    }
    return $imageUrl?$imageUrl:[];
}

function getMemberGoodsNum($m_id){
    $goods_num = M('Goods')->where(['m_id'=>$m_id,'status'=>1])->count('id');
    return $goods_num?$goods_num:"0";
}

function getMemberSupplyNum($m_id){
    $goods_num = M('Supply')->where(['m_id'=>$m_id,'status'=>1])->count('id');
    return $goods_num?$goods_num:"0";
}

