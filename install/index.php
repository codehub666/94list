<?php 
/**
 * 部署
 * 开源仓库地址：https://github.com/codehub666/94list.git
 * 作者官网:https://api.94speed.com/
 * 作者邮箱:a94author@outlook.com
 * 声明:本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用，禁止商用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。
 */
require_once(__DIR__ . "/../header.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>就是加速 网页端部署安装</title>
</head>
<body>
<div id="app">
<el-card class="card">
<?php
require_once(__DIR__ . "/../function.php");
// 初始化变量
$hostname = "";
$username = "";
$password = "";
$database = "";
$message = "";
if(!file_exists(__DIR__ . "/../config.php")) {
	echo '
    <el-steps :active="active" finish-status="success">
  <el-step title="开源说明"></el-step>
  <el-step title="数据库连接"></el-step>
  <el-step title="管理入口"></el-step>
</el-steps>
      <h2>就是加速 网页端部署安装</h2>
     <div  v-show="createDatabaseshow">
      <div class="input-container">
        <label>数据库地址：</label>
        <el-input v-model="mysql.hostname" placeholder="数据库地址"></el-input>
      </div>
      <div class="input-container">
        <label>数据库用户名：</label>
        <el-input v-model="mysql.username" placeholder="数据库用户名"></el-input>
      </div>
      <div class="input-container">
        <label>数据库密码：</label>
        <el-input v-model="mysql.password" placeholder="数据库密码" show-password></el-input>
      </div>
      <div class="input-container">
        <label>数据库名：</label>
        <el-input v-model="mysql.database" placeholder="数据库名"></el-input>
      </div>
      <el-button type="primary" @click="createDatabase">连接数据库</el-button>
      </div>
      <div  v-show="illustrate.show">
      <el-card>
      <el-text v-html="illustrate.content"></el-text>
      </el-card><br><br>
      <el-button type="primary" @click="agillustrate">同意并部署</el-button>
      </div>
     <div  v-show="Entranceshow">
      <el-card>
      <el-text>后台管理 默认账号:admin,密码 :admin</el-text>
      </el-card><br><br>
      <el-button type="primary" @click="openadmin()">前往</el-button>
      </div>
';
} else {
	echo'<h2>你已配置过数据库，如需要重新配置可把根目录config.php文件删除重新刷新本页面。</h2>';
}
?>
    </el-card>
  </div>
    <script>
      const App = {
	data() {
		return {
			active: 0,
			                   mysql: {
				hostname:"localhost",
				                  username:"",
				                  password:"",
				                  database:"",
			}
			,
			                   v:"<?php echo $v ?>",
			                   createDatabaseshow:false,
			                   Entranceshow:false,
			                   illustrate: {
				content:"获取说明中...",
				                       show:true,
			}
			,
		}
	}
	,
	           methods: {
		agillustrate() {
			this.createDatabaseshow=true;
			this.illustrate.show=false;
			if (this.active++ > 2) this.active = 0;
		}
		,
		               openadmin() {
			window.open('/admin', '_blank');
		}
		,
		              createDatabase() {
			axios.post('/api.php','type=createDatabase&hostname='+this.mysql.hostname+'&username='+this.mysql.username+'&password='+this.mysql.password+'&database='+this.mysql.database+'')
			      .then(response => {
				if(response.data.success==true) {
					//数据库连接成功
					if (this.active++ > 2) this.active = 0;
					this.createDatabaseshow=false;
					this.Entranceshow=true;
					this.$message( {
						message: response.data.message,
						          type: 'success'
					}
					);
				} else {
					//数据库连接失败
					this.$message.error(response.data.message);
				}
			}
			)
		}
	}
	,
	            mounted() {
		axios.post('https://speed.uy5.net/','v=0.0.1')
		      .then(response => {
			this.illustrate.content=response.data.content;
		}
		)
		      .catch(error => {
			this.illustrate.content='加载超时，获取信息失败';
		}
		);
	}
}
const app = Vue.createApp(App);
app.use(ElementPlus);
app.mount("#app");
</script>
      <style>
.input-container {
	margin-bottom: 10px;
}
</style>
</body>
</html>