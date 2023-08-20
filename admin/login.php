<?
/**
 * 登录
 * 开源仓库地址：https://github.com/codehub666/94list.git
 * 作者官网:https://api.94speed.com/
 * 作者邮箱:a94author@outlook.com
 * 声明:本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用，禁止商用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。
 */
require_once("../function.php");
if (!file_exists(__DIR__ . "/../config.php")) {
    header('Location: /install/');
} else {
    if (if_login()) {
        header('Location: ./');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
     <?php require_once(__DIR__ . "/../header.php"); ?>
    <title><?php echo $get_config['title'] ?> 登录面板</title>
</head>
<body>
<div id="app">
<el-card class="box-card">
<h2><?php echo $get_config['title'] ?> 后台登录面板</h2>
<el-input v-model="user" placeholder="请输入用户名"></el-input><br><br>
<el-input v-model="pass" placeholder="请输入密码" show-password></el-input><br><br>
 <el-button type="primary" @click="login">登录后台</el-button>
</el-card>
</div>
<script src="login.js"></script>
      <style>
.input-container {
  margin-bottom: 10px; /* 调整这里的数值来改变上下间距 */
}
</style>
</body>
</html>



