<?
/**
 * 后台
 * 开源仓库地址：https://github.com/codehub666/94list.git
 * 作者官网:https://api.94speed.com/
 * 作者邮箱:a94author@outlook.com
 * 声明:本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用，禁止商用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。
 */
require_once("../function.php");
if (!file_exists(__DIR__ . "/../config.php")) {
    header('Location: ../install/');
} else {
    if (!if_login()) {
        header('Location: ./login.php');
    }
    $get_config = get_config();
}
?>


<!DOCTYPE html>
<html>
<head>
<?php require_once(__DIR__ . "/../header.php"); ?>
<title><?php echo $get_config['title'] ?> 后台控制中心</title>
</head>
<body>
<div id="app">
    
    
    
     <el-dialog
    v-model="add_bd_user_dialog"
    title="添加代理账号"
    width="60%"
    :before-close="handleClose"
  >
   <el-input
    v-model="add_user.cookie"
    :rows="3"
    type="textarea"
    placeholder="请输入代理账号的cookie"
  ></el-input><br><br>
  <el-button type="primary" @click="get_bd_info()" :loading="add_user.loading" round>获取信息</el-button><br><br>
  <el-input v-model="add_user.name" disabled placeholder="代理账号名称" ></el-input><br><br>
  <el-input v-model="add_user.vip_type" disabled placeholder="代理账号等级" ></el-input>
    <template #footer>
      <span class="dialog-footer">
        <el-button @click="add_bd_user_dialog = false">取消</el-button>
        <el-button type="primary" @click="add()">
          添加
        </el-button>
      </span>
    </template>
  </el-dialog>
  
  
  
  
<el-card class="box-card" >
    <h2><?php echo $get_config['title'] ?> 后台控制中心</h2>
  <el-tabs v-model="activeName" @tab-click="handleClick">
    <el-tab-pane label="基础配置" name="config">
        <label>站点名称:</label><br><br><el-input v-model="config.title" placeholder="起个好听的名字吧"></el-input><br><br>
        <label>设置下载 User_Agent:</label><br><br><el-input v-model="config.user_agent" placeholder="下载器得设置这个UA才能进行下载"></el-input><br><br>
        <label>公告设置:</label>  <el-switch v-model="config.AnnounceSwitch" ></el-switch>
        <el-input
    v-model="config.Announce"
    :rows="2"
    type="textarea"
    placeholder="输入公告内容"
  ></el-input><br><br>
   <label>获取列表账号的cookie:</label>
   <el-input
    v-model="config.cookie"
    :rows="2"
    type="textarea"
    placeholder="此账号用于获取列表数据的，请输入代理账号的cookie"
  ></el-input><br><br>
  <el-button type="primary" @click="revise_info()">保存</el-button>
    </el-tab-pane>
    <el-tab-pane label="代理账号管理" name="account_management">
     <el-button @click="add_bd_user()">添加代理账号</el-button><br><br>
     <el-table
     show-overflow-tooltip
    v-loading="loading"
    :data="tableData"
    border
    style="width: 100%">
     <el-table-column
      prop="id"
      label="编号"
      width="50">
    </el-table-column>
    <el-table-column
      prop="name"
      label="账号名称"
      width="100">
    </el-table-column>
    <el-table-column
      label="状态"
      width="80">
<template #default="scope">
     {{ scope.row.state == "0" ? '正常' : (scope.row.state == "-2" ? '待测试' : '寄了') }}
  </template>
    </el-table-column>
    <el-table-column
      prop="add_time"
      label="添加时间"
      width="150">
    </el-table-column>
    <el-table-column
      prop="use"
      label="最后一次有效时间"
      width="150">
    </el-table-column>
    <el-table-column
      prop="cookie"
      label="cookie值">
    </el-table-column>
     <el-table-column
      label="操作"
      width="160">
        <template #default="scope">
   
  <el-button size="mini" @click="switch_bd_user(scope.row.id,scope.row.switch,scope.$index)">
  {{ scope.row.switch == "0" ? '关闭' : (scope.row.switch == "-1" ? '开启' : scope.row.switch) }}
</el-button>
  
  <el-button size="mini" type="danger" @click="delete_bd_user(scope.$index, scope.row)">删除</el-button>
</template>
    </el-table-column>
  </el-table>
    </el-tab-pane>
    <el-tab-pane label="开源说明" name="third">
        <el-card class="illustrate">
<el-text v-html="illustrate.message"></el-text>
</el-card>
        
    </el-tab-pane>
  </el-tabs>

</el-card>
</div>
<script>
  const user_agent = "<?php echo $get_config['user_agent']; ?>";
  const title = "<?php echo $get_config['title']; ?>";
  const AnnounceSwitch = <?php echo $get_config['AnnounceSwitch'] ? 'true' : 'false'; ?>;
  const Announce = "<?php echo $get_config['Announce']; ?>";
 const cookie = <?php echo json_encode($get_config['cookie']); ?>;
</script>
<script src="index.js"></script>
<style>
.input-container {
  margin-bottom: 10px; /* 调整这里的数值来改变上下间距 */
}
</style>
</body>
</html>



