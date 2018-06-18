<?php
/**
 * 菜单配置列表
 * group  父菜单 title标题名称  icon改组图标 class是否选中 默认为空 url链接地址  is_developer 0都可见 1开发者模式可见
 * child 子菜单 同上
 */
    return array(
        'MENUS' => array(
            array(
                'group' => array('title' => '仪表盘', 'icon' => 'fa fa-bar-chart', 'class' => '', 'url' => 'Index/index', 'is_developer' => 0),
                '_child' => array()
            ),
            array(
                'group' => array('title'=>'商家管理','icon'=>'fa fa-user fa-fw','class'=>'','url'=>'Member/index','is_developer'=>0),
                '_child' => array(
                )
            ),
            array(
                'group' => array('title'=>'广告管理','icon'=>'fa fa-bullhorn','class'=>'','url'=>'Advert/index','is_developer'=>0),
                '_child' => array(
                )
            ),
            array(
                'group' => array('title'=>'商品管理','icon'=>'fa fa-navicon','class'=>'','url'=>'javascript:void(0);','is_developer'=>0),
                '_child' => array(
                    array('title'=>'商品分类','url'=>'GoodsType/index','class'=>'','is_developer'=>0),
                    array('title'=>'商品列表','url'=>'Goods/index','class'=>'','is_developer'=>0),
                    array('title'=>'热搜关键词','url'=>'SearchWords/index','class'=>'','is_developer'=>0),
                )
            ),


            array(
                'group' => array('title'=>'财务管理','icon'=>'fa fa-money','class'=>'','url'=>'javascript:void(0);','is_developer'=>0),
                '_child' => array(
                    array('title'=>'用户提现','url'=>'BalanceWithdraw/index','class'=>'','is_developer'=>0),
                    array('title'=>'账单记录','url'=>'PayLog/index','class'=>'','is_developer'=>0),
                )
            ),
            array(
                'group' => array('title'=>'文章管理','icon'=>'fa fa-sliders','class'=>'','url'=>'ArticleS/index','is_developer'=>0),
                '_child' => array(
                )
            ),
            array(
                'group' => array('title'=>'消息管理','icon'=>'fa fa-sliders','class'=>'','url'=>'Msg/index','is_developer'=>0),
                '_child' => array(
                )
            ),
            array(
                'group' => array('title'=>'信息管理','icon'=>'fa fa-bars','class'=>'','url'=>'javascript:void(0);','is_developer'=>0),
                '_child' => array(
                    array('title'=>'短信模板设置','url'=>'SendTemplate/index','class'=>'','is_developer'=>0),
                    array('title'=>'短信发送记录','url'=>'SendLog/index','class'=>'','is_developer'=>0),
                )
            ),

            array(
                'group' => array('title'=>'管理员操作','icon'=>'fa fa-user','class'=>'','url'=>'javascript:void(0);','is_developer'=>0),
                '_child' => array(
                    array('title'=>'管理员信息','url'=>'Administrator/index','class'=>'','is_developer'=>0),
                    array('title'=>'分组权限','url'=>'AuthGroup/index','class'=>'','is_developer'=>0),
                    array('title'=>'行为信息','url'=>'Action/index','class'=>'','is_developer'=>0),
                    array('title'=>'行为日志','url'=>'ActionLog/index','class'=>'','is_developer'=>0),
                )
            ),
            array(
                'group' => array('title'=>'系统设置','icon'=>'fa fa-wrench','class'=>'','url'=>'javascript:void(0);','is_developer'=>0),
                '_child' => array(
                    array('title'=>'网站设置','url'=>'ConfigSet/index?config_group=1','class'=>'','is_developer'=>0),
                    array('title'=>'配置管理','url'=>'Config/index','class'=>'','is_developer'=>0),
                )
            ),
            array(
                'group' => array('title'=>'扩展管理','icon'=>'fa fa-hdd-o','class'=>'','url'=>'javascript:void(0);','is_developer'=>1),
                '_child' => array(
                    array('title'=>'插件管理','url'=>'Plugins/index','class'=>'','is_developer'=>1),
                    array('title'=>'钩子管理','url'=>'Hooks/index','class'=>'','is_developer'=>1)
                )
            ),
        ),
    );