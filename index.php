<?php
require_once(__DIR__ . "/function.php");
!file_exists("./config.php")? header('Location: ./install/'):

$get_config=get_config();?>
<!DOCTYPE html>
<html>
<head>
<?php require_once(__DIR__ . "/header.php"); ?>
<title><?php echo $get_config['title'] ?> 用户操作演示面板</title>
</head>
<body>

<div id="app">
 <el-dialog
    v-model="AnnounceSwitch"
    title="公告"
    width="90%"
  >
<span>{{Announce}}</span>
  </el-dialog>
  <el-dialog v-model="DownDialog" title="解析任务列表" width="80%" >
      <el-space wrap> 当前的UA : <el-link type="danger" @click="copy(user_agent,'已复制UA')">{{ user_agent }}</el-link></el-space><br><br>
    <el-table :data="rw_list" show-overflow-tooltip>
      <el-table-column property="name" label="文件名" width="180"  fixed></el-table-column>
      <el-table-column  label="下载链接" width="480" >
    <template #default="scope">
    {{ scope.row.dlink }}
  </template>
      </el-table-column>
      <el-table-column label="操作" width="280">
      <template #default="scope"  >
          
          <template v-if="scope.row.DownState=='0'">
         <el-button @click="copy(scope.row.dlink,'已将链接复制到粘贴板内')" type="text" size="small">复制链接</el-button>
        <el-button type="text" size="small" @click="senddown(scope.row.dlink,scope.row.name,'6800')">发送Aria2</el-button>
        <el-button type="text" size="small" @click="senddown(scope.row.dlink,scope.row.name,'16800')">发送Motrix</el-button>
        </template>
         <template v-if="scope.row.DownState=='1'">
         
        </template>
      </template>
    </el-table-column>
    </el-table>
  </el-dialog>
    
<el-card class="card">
<el-input
  type="textarea"
  :rows="2"
  placeholder="需要处理的链接"
  v-model="taskurl">
</el-input><br><br>
<el-input v-model="pass" placeholder="链接对应的密码"></el-input><br><br>
<el-row :gutter="5">
<el-button  type="primary" @click="analyze()" :loading="taskstate">解析链接</el-button>
<el-button  type="primary" @click="pl_down()" :loading="taskstate">批量下载</el-button>
</el-row>
</el-card><br><br>
<el-card>
<el-table
    :data="list"
    stripe
    style="width: 100%"
    @row-click="clickfile"
    v-loading="taskstate"
     @selection-change="SelectedRows"
    >
        <el-table-column
      type="selection"
      width="55">
    </el-table-column>
    <el-table-column
      label="文件名"
      width="280">
   <template #default="scope">
    <el-space wrap>
  <img
    :src="scope.row.isdir == '1' ? '/assets/images/file.png' : '/assets/images/unknownfile.png'"
    style="width: 20px; height: 20px;"
  />
  {{ scope.row.server_filename }}
</el-space>
    </template>
    </el-table-column>
    <el-table-column
      label="修改时间"
      width="180">
  <template #default="scope">
    {{ formatTimestamp(scope.row.server_mtime) }}
  </template>
    </el-table-column>
    <el-table-column
      label="大小">
      <template #default="scope">
    {{ formatBytes(scope.row.size) }}
  </template>
    </el-table-column>
  </el-table>
  </el-card>
</div>
<script>
  const user_agent = "<?php echo $get_config['user_agent']; ?>";
  const AnnounceSwitch = <?php echo $get_config['AnnounceSwitch'] ? 'true' : 'false'; ?>;
  const Announce = "<?php echo $get_config['Announce']; ?>";
</script>
<script src="/assets/js/index.js"></script>