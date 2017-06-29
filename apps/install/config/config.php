<?php
define('CHARSET', 'utf-8');
define('DBCHARSET', 'utf8');

define('METHOD_UNDEFINED', 255);
define('ENV_CHECK_RIGHT', 0);
define('ERROR_CONFIG_VARS', 1);
define('SHORT_OPEN_TAG_INVALID', 2);
define('INSTALL_LOCKED', 3);
define('DATABASE_NONEXISTENCE', 4);
define('PHP_VERSION_TOO_LOW', 5);
define('MYSQL_VERSION_TOO_LOW', 6);
define('UC_URL_INVALID', 7);
define('UC_DNS_ERROR', 8);
define('UC_URL_UNREACHABLE', 9);
define('UC_VERSION_INCORRECT', 10);
define('UC_DBCHARSET_INCORRECT', 11);
define('UC_API_ADD_APP_ERROR', 12);
define('UC_ADMIN_INVALID', 13);
define('UC_DATA_INVALID', 14);
define('DBNAME_INVALID', 15);
define('DATABASE_ERRNO_2003', 16);
define('DATABASE_ERRNO_1044', 17);
define('DATABASE_ERRNO_1045', 18);
define('DATABASE_CONNECT_ERROR', 19);
define('TABLEPRE_INVALID', 20);
define('CONFIG_UNWRITEABLE', 21);
define('ADMIN_USERNAME_INVALID', 22);
define('ADMIN_EMAIL_INVALID', 25);
define('ADMIN_EXIST_PASSWORD_ERROR', 26);
define('ADMININFO_INVALID', 27);
define('LOCKFILE_NO_EXISTS', 28);
define('TABLEPRE_EXISTS', 29);
define('ERROR_UNKNOW_TYPE', 30);
define('ENV_CHECK_ERROR', 31);
define('UNDEFINE_FUNC', 32);
define('MISSING_PARAMETER', 33);
define('LOCK_FILE_NOT_TOUCH', 34);

define('DISCUZ_VERSION', 'X3.2');
define('DISCUZ_RELEASE', '20151208');
define('SOFT_NAME', 'Discuz!');
define('INSTALL_LANG', 'SC_UTF8');
define('CONFIG', './config/config_global.php');
define('CONFIG_UC', './config/config_ucenter.php');
define('UC_VERNAME', '中文版');

$lang = array(
    'SC_GBK' => '简体中文版',
    'TC_BIG5' => '繁体中文版',
    'SC_UTF8' => '简体中文 UTF8 版',
    'TC_UTF8' => '繁体中文 UTF8 版',
    'EN_ISO' => 'ENGLISH ISO8859',
    'EN_UTF8' => 'ENGLIST UTF-8',
    
    'title_install' => SOFT_NAME . ' 安装向导',
    'agreement_yes' => '我同意',
    'agreement_no' => '我不同意',
    'notset' => '不限制',
    
    'message_title' => '提示信息',
    'error_message' => '错误信息',
    'message_return' => '返回',
    'return' => '返回',
    'install_wizard' => '安装向导',
    'config_nonexistence' => '配置文件不存在',
    'nodir' => '目录不存在',
    'redirect' => '浏览器会自动跳转页面，无需人工干预。<br>除非当您的浏览器没有自动跳转时，请点击这里',
    'auto_redirect' => '浏览器会自动跳转页面，无需人工干预',
    'database_errno_2003' => '无法连接数据库，请检查数据库是否启动，数据库服务器地址是否正确',
    'database_errno_1044' => '无法创建新的数据库，请检查数据库名称填写是否正确',
    'database_errno_1045' => '无法连接数据库，请检查数据库用户名或者密码是否正确',
    'database_errno_1064' => 'SQL 语法错误',
    
    'dbpriv_createtable' => '没有CREATE TABLE权限，无法继续安装',
    'dbpriv_insert' => '没有INSERT权限，无法继续安装',
    'dbpriv_select' => '没有SELECT权限，无法继续安装',
    'dbpriv_update' => '没有UPDATE权限，无法继续安装',
    'dbpriv_delete' => '没有DELETE权限，无法继续安装',
    'dbpriv_droptable' => '没有DROP TABLE权限，无法安装',
    
    'db_not_null' => '数据库中已经安装过 UCenter, 继续安装会清空原有数据。',
    'db_drop_table_confirm' => '继续安装会清空全部原有数据，您确定要继续吗?',
    
    'writeable' => '可写',
    'unwriteable' => '不可写',
    'old_step' => '上一步',
    'new_step' => '下一步',
    
    'database_errno_2003' => '无法连接数据库，请检查数据库是否启动，数据库服务器地址是否正确',
    'database_errno_1044' => '无法创建新的数据库，请检查数据库名称填写是否正确',
    'database_errno_1045' => '无法连接数据库，请检查数据库用户名或者密码是否正确',
    'database_connect_error' => '数据库连接错误',
    
    'step_title_1' => '检查安装环境',
    'step_title_2' => '设置运行环境',
    'step_title_3' => '创建数据库',
    'step_title_4' => '安装',
    'step_env_check_title' => '开始安装',
    'step_env_check_desc' => '环境以及文件目录权限检查',
    'step_db_init_title' => '安装数据库',
    'step_db_init_desc' => '正在执行数据库安装',
    
    'step1_file' => '目录文件',
    'step1_need_status' => '所需状态',
    'step1_status' => '当前状态',
    'not_continue' => '请将以上红叉部分修正再试',
    
    'tips_dbinfo' => '填写数据库信息',
    'tips_dbinfo_comment' => '',
    'tips_admininfo' => '填写管理员信息',
    'step_ext_info_title' => '安装成功。',
    'step_ext_info_comment' => '点击进入登录',
    
    'ext_info_succ' => '安装成功。',
    'install_submit' => '提交',
    // 'install_locked' => '安装锁定，已经安装过了，如果您确定要重新安装，请到服务器上删除<br /> ' . str_replace(ROOT_PATH, '', $lockfile),
    'install_locked' => '安装锁定，已经安装过了，如果您确定要重新安装，请到服务器上删除<br /> ',
    'error_quit_msg' => '您必须解决以上问题，安装才可以继续',
    
    'step_app_reg_title' => '设置运行环境',
    'step_app_reg_desc' => '检测服务器环境以及设置 UCenter',
    'tips_ucenter' => '请填写 UCenter 相关信息',
    'tips_ucenter_comment' => 'UCenter 是 Comsenz 公司产品的核心服务程序，Discuz! Board 的安装和运行依赖此程序。如果您已经安装了 UCenter，请填写以下信息。否则，请到 <a href="http://www.discuz.com/" target="blank">Comsenz 产品中心</a> 下载并且安装，然后再继续。',
    
    'advice_mysql_connect' => '请检查 mysql 模块是否正确加载',
    'advice_gethostbyname' => '是否 PHP 配置中禁止了 gethostbyname 函数。请联系空间商，确定开启了此项功能',
    'advice_file_get_contents' => '该函数需要 php.ini 中 allow_url_fopen 选项开启。请联系空间商，确定开启了此项功能',
    'advice_xml_parser_create' => '该函数需要 PHP 支持 XML。请联系空间商，确定开启了此项功能',
    'advice_fsockopen' => '该函数需要 php.ini 中 allow_url_fopen 选项开启。请联系空间商，确定开启了此项功能',
    'advice_pfsockopen' => '该函数需要 php.ini 中 allow_url_fopen 选项开启。请联系空间商，确定开启了此项功能',
    'advice_stream_socket_client' => '是否 PHP 配置中禁止了 stream_socket_client 函数',
    'advice_curl_init' => '是否 PHP 配置中禁止了 curl_init 函数',
    
    'ucurl' => 'UCenter 的 URL',
    'ucpw' => 'UCenter 创始人密码',
    'ucip' => 'UCenter 的IP地址',
    'ucenter_ucip_invalid' => '格式错误，请填写正确的 IP 地址',
    'ucip_comment' => '绝大多数情况下您可以不填',
    
    'tips_siteinfo' => '请填写站点信息',
    'sitename' => '站点名称',
    'siteurl' => '站点 URL',
    
    'forceinstall' => '强制安装',
    'dbinfo_forceinstall_invalid' => '当前数据库当中已经含有同样表前缀的数据表，您可以修改“表名前缀”来避免删除旧的数据，或者选择强制安装。强制安装会删除旧数据，且无法恢复',
    
    'click_to_back' => '点击返回上一步',
    'adminemail' => '系统信箱 Email',
    'adminemail_comment' => '用于发送程序错误报告',
    'dbhost_comment' => '数据库服务器地址, 一般为 localhost',
    'tablepre_comment' => '同一数据库运行多个论坛时，请修改前缀',
    'forceinstall_check_label' => '我要删除数据，强制安装 !!!',
    
    'uc_url_empty' => '您没有填写 UCenter 的 URL，请返回填写',
    'uc_url_invalid' => 'URL 格式错误',
    'uc_url_unreachable' => 'UCenter 的 URL 地址可能填写错误，请检查',
    'uc_ip_invalid' => '无法解析该域名，请填写站点的 IP',
    'uc_admin_invalid' => 'UCenter 创始人密码错误，请重新填写',
    'uc_data_invalid' => '通信失败，请检查 UCenter 的URL 地址是否正确 ',
    'uc_dbcharset_incorrect' => 'UCenter 数据库字符集与当前应用字符集不一致',
    'uc_api_add_app_error' => '向 UCenter 添加应用错误',
    'uc_dns_error' => 'UCenter DNS解析错误，请返回填写一下 UCenter 的 IP地址',
    
    'ucenter_ucurl_invalid' => 'UCenter 的URL为空，或者格式错误，请检查',
    'ucenter_ucpw_invalid' => 'UCenter 的创始人密码为空，或者格式错误，请检查',
    'siteinfo_siteurl_invalid' => '站点URL为空，或者格式错误，请检查',
    'siteinfo_sitename_invalid' => '站点名称为空，或者格式错误，请检查',
    'dbinfo_dbhost_invalid' => '数据库服务器为空，或者格式错误，请检查',
    'dbinfo_dbname_invalid' => '数据库名为空，或者格式错误，请检查',
    'dbinfo_dbuser_invalid' => '数据库用户名为空，或者格式错误，请检查',
    'dbinfo_dbpw_invalid' => '数据库密码为空，或者格式错误，请检查',
    'dbinfo_adminemail_invalid' => '系统邮箱为空，或者格式错误，请检查',
    'dbinfo_tablepre_invalid' => '数据表前缀为空，或者格式错误，请检查',
    'admininfo_username_invalid' => '管理员用户名为空，或者格式错误，请检查',
    'admininfo_email_invalid' => '管理员Email为空，或者格式错误，请检查',
    'admininfo_password_invalid' => '管理员密码为空，请填写',
    'admininfo_password2_invalid' => '两次密码不一致，请检查',
    
    'install_dzfull' => '全新安装 Discuz! X (含 UCenter Server)',
    'install_dzonly' => '仅安装 Discuz! X (手工指定已经安装的 UCenter Server)',
    
    'username' => '管理员账号',
    'email' => '管理员 Email',
    'password' => '管理员密码',
    'password_comment' => '管理员密码不能为空',
    'password2' => '重复密码',
    
    'admininfo_invalid' => '管理员信息不完整，请检查管理员账号，密码，邮箱',
    'dbname_invalid' => '数据库名为空，请填写数据库名称',
    'tablepre_invalid' => '数据表前缀为空，或者格式错误，请检查',
    'admin_username_invalid' => '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字',
    'admin_password_invalid' => '密码和上面不一致，请重新输入',
    'admin_email_invalid' => 'Email 地址错误，此邮件地址已经被使用或者格式无效，请更换为其他地址',
    'admin_invalid' => '您的信息管理员信息没有填写完整，请仔细填写每个项目',
    'admin_exist_password_error' => '该用户已经存在，如果您要设置此用户为论坛的管理员，请正确输入该用户的密码，或者请更换论坛管理员的名字',
    
    'tagtemplates_subject' => '标题',
    'tagtemplates_uid' => '用户 ID',
    'tagtemplates_username' => '发帖者',
    'tagtemplates_dateline' => '日期',
    'tagtemplates_url' => '主题地址',
    
    'uc_version_incorrect' => '您的 UCenter 服务端版本过低，请升级 UCenter 服务端到最新版本，并且升级，下载地址：http://www.comsenz.com/ 。',
    'config_unwriteable' => '安装向导无法写入配置文件, 请设置 config.inc.php 程序属性为可写状态(777)',
    
    'install_in_processed' => '正在安装...',
    'install_succeed' => '安装成功，点击进入',
    'install_cloud' => '安装成功，欢迎开通Discuz!云平台<br>Discuz!云平台致力于帮助站长提高网站流量，增强网站运营能力，增加网站收入。<br>Discuz!云平台目前免费提供了QQ互联、腾讯分析、纵横搜索、漫游应用、SOSO表情服务。Discuz!云平台将陆续提供更多优质服务项目。<br>开通Discuz!平台之前，请确保您的网站（Discuz!、UCHome或SupeSite）已经升级到Discuz! X3。',
    'to_install_cloud' => '到后台开通',
    'to_index' => '暂不开通',
    'uc_installed' => '您已经安装过 UCenter，如果需要重新安装，请删除 data/install.lock 文件',
    'i_agree' => '我已仔细阅读，并同意上述条款中的所有内容',
    'supportted' => '支持',
    'unsupportted' => '不支持',
    'max_size' => '支持/最大尺寸',
    'project' => '项目',
    'ucenter_required' => 'Discuz! 所需配置',
    'ucenter_best' => 'Discuz! 最佳',
    'curr_server' => '当前服务器',
    'env_check' => '环境检查',
    'os' => '操作系统',
    'php' => 'PHP 版本',
    'attachmentupload' => '附件上传',
    'unlimit' => '不限制',
    'version' => '版本',
    'gdversion' => 'GD 库',
    'allow' => '允许 ',
    'unix' => '类Unix',
    'diskspace' => '磁盘空间',
    'priv_check' => '目录、文件权限检查',
    'func_depend' => '函数依赖性检查',
    'func_name' => '函数名称',
    'check_result' => '检查结果',
    'suggestion' => '建议',
    'advice_mysql' => '请检查 mysql 模块是否正确加载',
    'advice_fopen' => '该函数需要 php.ini 中 allow_url_fopen 选项开启。请联系空间商，确定开启了此项功能',
    'advice_file_get_contents' => '该函数需要 php.ini 中 allow_url_fopen 选项开启。请联系空间商，确定开启了此项功能',
    'advice_xml' => '该函数需要 PHP 支持 XML。请联系空间商，确定开启了此项功能',
    'none' => '无',
    
    'dbhost' => '数据库服务器',
    'dbuser' => '数据库用户名',
    'dbpw' => '数据库密码',
    'dbname' => '数据库名',
    'tablepre' => '数据表前缀',
    
    'ucfounderpw' => '创始人密码',
    'ucfounderpw2' => '重复创始人密码',
    
    'init_log' => '初始化记录',
    'clear_dir' => '清空目录',
    'select_db' => '选择数据库',
    'create_table' => '建立数据表',
    'succeed' => '成功 ',
    
    'install_data' => '正在安装数据',
    'install_test_data' => '正在安装附加数据',
    
    'method_undefined' => '未定义方法',
    'database_nonexistence' => '数据库操作对象不存在',
    'skip_current' => '跳过本步',
    'topic' => '专题',
    'install_finish' => '您的论坛已完成安装，点此访问'
);

return array(
    'lang' => $lang
);

