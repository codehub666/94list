<?php
/**
 * 全局接口
 * 开源仓库地址：https://github.com/codehub666/94list.git
 * 作者官网:https://api.94speed.com/
 * 作者邮箱:a94author@outlook.com
 * 声明:本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用，禁止商用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。
 */

// 导入功能函数
require_once(__DIR__ . "/function.php");

// 尝试解析 POST 数据
try {
    // 从输入流中读取 JSON 数据并解析为关联数组
    $post = json_decode(file_get_contents("php://input"), true);
    // 合并 POST 数据到 $_POST 变量中
    $_POST = is_array($post) ? array_merge($_POST, $post) : $_POST;
} catch (Exception $th) {
    // 异常处理代码
}

// 获取请求类型
$type = isset($_POST['type']) ? $_POST['type'] : null;

// 管理员操作映射表
$adminActions = [
    'get_account_list' => 'get_account_list',
    'delete_bd_user' => 'delete_bd_user',
    'get_bd_info' => 'get_bd_info',
    'add' => 'add',
    'revise_info' => 'revise_info',
    'switch_bd_user' => 'switch_bd_user',
];

// 通用操作映射表
$commonActions = [
    'createDatabase' => 'createDatabase',
    'login' => 'login',
    'get_list' => 'get_list',
    'get_sign' => 'get_sign',
    'down_file' => 'down_file'
];

// 如果存在配置文件并且用户已登录且请求类型是管理员操作
if ($type && file_exists(__DIR__ . "/config.php")) {
    if (if_login() && isset($adminActions[$type])) {
        // 调用相应的管理员操作函数并输出结果
        $data = call_user_func($adminActions[$type]);
        echo $data;
        exit; // 加上退出语句，确保只执行一个操作
    }
}

// 如果请求类型是通用操作
if ($type && isset($commonActions[$type])) {
    // 调用相应的通用操作函数并输出结果
    $data = call_user_func($commonActions[$type]);
    echo $data;
}
?>
