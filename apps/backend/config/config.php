<?php
return new \Phalcon\Config(array(
    'admin' => array(
        // LOGO图 大小图
        'logo_mini' => '<b>A</b>LT',
        'logo' => '<b>Admin</b>LTE',

        // 后台管理系统的名称        
        'name' => '某系统后台管理系统',
        // 是否显示菜单搜索
        'enable_menu_search' => true,
        // 皮肤         
        'skin' => 'skin-blue',
        // 样式
        'layout' => 'sidebar-mini fixed',
        /*
        |--------------------------------------------------------------------------
        | Enable default breadcrumb
        |--------------------------------------------------------------------------
        |
        | Whether enable default breadcrumb for every page content.
        */
        'enable_default_breadcrumb' => true,
    ),
));
