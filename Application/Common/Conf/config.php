<?php
defined('THINK_PATH') or exit();
return  array(
  'LANG_SWITCH_ON'        => true,   // 开启语言包功能
  'LANG_AUTO_DETECT'      => true, // 自动侦测语言 开启多语言功能后有效
  'DEFAULT_LANG'          => 'zh-cn', // 默认语言
  'LANG_LIST'             => 'zh-cn', // 允许切换的语言列表 用逗号分隔
  'VAR_LANGUAGE'          => 'lang', // 默认语言切换变量

  'DEFAULT_MODULE'        =>  'Home',  // 默认模块
  'DEFAULT_CONTROLLER'    =>  'Article', // 默认控制器名称
  'DEFAULT_ACTION'        =>  'list', // 默认操作名称

  'ACTION_SUFFIX'         =>  'Action', // 操作方法后缀
  //'COOKIE_EXPIRE'         =>  1800,       // Cookie有效期
  'COOKIE_PREFIX'         =>  'ZYALLER_COOKIE_',      // Cookie前缀 避免冲突

  'TMPL_ACTION_ERROR'     =>  APP_PATH.'Common/View/Pintuer/Tmpl/jump.html', // 默认错误跳转对应的模板文件
  'TMPL_ACTION_SUCCESS'   =>  APP_PATH.'Common/View/Pintuer/Tmpl/jump.html', // 默认成功跳转对应的模板文件
  'TMPL_EXCEPTION_FILE'   =>  APP_PATH.'Common/View/Pintuer/Tmpl/exception.html',// 异常页面的模板文件

  'TMPL_DETECT_THEME'     =>  true, // 自动侦测模板主题
  'DEFAULT_THEME'         =>  'Pintuer', // 默认模板主题名称，可以通过在URL地址中传入t参数来切换如http://localhost/?t=think，如果切换到一个不存在的模板主题，就会使用默认主题。
  'THEME_LIST'            =>  'Pintuer', // 支持的模板主题项
  'DEFAULT_FILTER'        =>  '', // 默认参数过滤方法 用于I函数...

  //'SESSION_OPTIONS'       =>  array( 'expire' => 1800 ), // session 配置数组 支持type name id path expire domain 等参数
  'SESSION_PREFIX'        =>  'ZYALLER_SESSION_', // session 前缀

  'TMPL_STRIP_SPACE'      =>  false,       // 是否去除模板文件里面的html空格与换行

  'DB_TYPE'               =>  'mysql',     // 数据库类型
  'DB_HOST'               =>  'zyb389db.mysql.rds.aliyuncs.com', // 服务器地址
  'DB_NAME'               =>  'life',          // 数据库名
  'DB_USER'               =>  'admin',      // 用户名
  'DB_PWD'                =>  'admin',          // 密码
  'DB_PORT'               =>  '3306',      // 端口
  'DB_PREFIX'             =>  'life_',    // 数据库表前缀

  'DB_DEBUG'              =>  false, // 数据库调试模式 开启后可以记录SQL日志

  'URL_MODEL'             =>  2, // URL模式
  'URL_PARAMS_BIND'       =>  false, // URL变量绑定到Action方法参数

  'URL_ROUTER_ON'         =>  true,   // 是否开启URL路由
  'URL_ROUTE_RULES'       =>  array(  // 默认路由规则 针对模块
  	'/^([\x{4e00}-\x{9fa5}a-z0-9_]+)$/ui'     =>  'Home/Article/list?alias=:1'
  ),
  'URL_MAP_RULES'         =>  array(), // URL映射定义规则

  'TMPL_PARSE_STRING'     =>  array(
    '__PUBLIC__'          =>     '/Public',
    '__JS__'              =>     '/Public/js',
    '__IMG__'             =>     '/Public/image',
    '__CSS__'             =>     '/Public/css',
    '__UPLOAD__'          =>     '/Upload',
  )

);