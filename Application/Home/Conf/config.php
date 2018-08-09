<?php
/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'hzp_home_', // 缓存前缀
    'DATA_CACHE_TYPE' => 'File', // 数据缓存类型

    /* 文件上传相关配置 */
    'ATTACHMENT_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'zip,rar,tar,gz,7z,doc,docx,txt,xml,xls', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）
    'ATTACHMENT_UPLOAD_DRIVER' => 'local',
    'EXCEL_UPLOCE' => array(
        'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'xls', //允许上传的文件后缀
        'rootPath' => './Uploads/', //保存根路径
        'savePath' => 'Excel/', //保存路径
        'saveName' => array('uniqid', ''),
        'autoSub' => false, //自动子目录保存文件
    ),
    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 5* 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg,mp4,mov', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）
    'PICTURE_UPLOAD_DRIVER' => 'local',

    //本地上传文件驱动配置
    'UPLOAD_LOCAL_CONFIG' => array(),
    'UPLOAD_BCS_CONFIG' => array(
        'AccessKey' => '',
        'SecretKey' => '',
        'bucket' => '',
        'rename' => false
    ),

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/Static',
        '__PLUGINS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Plugins',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/img',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'hzp_home', //session前缀
    'COOKIE_PREFIX' => 'hzp_home_', // Cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',    //修复uploadify插件无法传递session_id的bug


    /* URL配置 */
    'URL_CASE_INSENSITIVE' => false, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 0, //URL模式 2重写模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    //api路径
    'API_URL' => 'http://hzp.txunda.com/',
);
