<?
/**
 * 全局接口
 * 开源仓库地址：https://github.com/codehub666/94list.git
 * 作者官网:https://api.94speed.com/
 * 作者邮箱:a94author@outlook.com
 * 声明:本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用，禁止商用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。
 */
require_once(__DIR__ . "/function.php");
$type = $_POST['type'];
$adminActions = [
    'get_account_list' => 'get_account_list',
    'delete_bd_user' => 'delete_bd_user',
    'get_bd_info' => 'get_bd_info',
    'add' => 'add',
    'revise_info' => 'revise_info',
    'switch_bd_user' => 'switch_bd_user',
];
$commonActions = [
    'createDatabase' => 'createDatabase',
    'login' => 'login',
    'get_list' => 'get_list',
    'get_sign' => 'get_sign',
    'down_file' => 'down_file'
];
if (file_exists(__DIR__ . "/config.php")) {
    if (if_login() && isset($adminActions[$type])) {
        $data = call_user_func($adminActions[$type]);
        echo $data;
    }
}
if (isset($commonActions[$type])) {
    $data = call_user_func($commonActions[$type]);
    echo $data;
}

