 const App = {
 	data() {
 		return {
 			viptypemap: new Map([
 				[0, '普通用户'],
 				[1, '普通会员'],
 				[2, '超级会员']
 			]),
 			activeName: 'account_management',
 			loading: true,
 			add_bd_user_dialog: false,
 			config: {
 				user_agent: user_agent,
 				title: title,
 				AnnounceSwitch: AnnounceSwitch,
 				Announce: Announce,
 				cookie: cookie, 
 			},
 			illustrate: {
 				message: "说明信息加载中",
 			},
 			add_user: {
 				getinfo: false,
 				loading: false,
 				cookie: null,
 				name: null,
 				vip_type: null,
 			},
 			tableData: []
 		};
 	},
 	methods: {
 		switch_bd_user(id, switchstate, index) {

 			axios.post('/api.php/', 'type=switch_bd_user&id=' + id + '&switch=' + switchstate + '')
 				.then(response => {
 					if (response.data.success == true) {
 						this.tableData[index]['switch'] = response.data.switch;
 						this.$message({
 							message: response.data.message,
 							type: 'success'
 						});
 					} else {
 						this.$message.error(response.data.message);
 					}
 				})
 		},
 		add() {
 			if (this.add_user.getinfo == false) {
 				return this.$message.error('获取信息后在进行添加操作');
 			}
 			axios.post('/api.php', 'type=add&cookie=' + this.add_user.cookie + '&name=' + this.add_user.name + '&vip_type=' + this.add_user.vip_type + '')
 				.then(response => {
 					if (response.data.success == true) {
 						this.add_user.getinfo = false;
 						this.add_bd_user_dialog = false;
 						this.bd_list();
 						this.$message({
 							message: response.data.message,
 							type: 'success'
 						});
 					} else {
 						this.$message.error(response.data.message);
 					}

 				})
 		},
 		get_bd_info() {
 			if (this.add_user.cookie == null) {
 				return this.$message.error('啊啊啊，你提交了个寂寞');
 			}
 			this.add_user.loading = true;
 			axios.post('/api.php', 'type=get_bd_info&cookie=' + this.add_user.cookie)
 				.then(response => {
 					this.add_user.loading = false;
 					if (response.data.success == true) {
 						if (response.data.data.errno == -6) {
 							return this.$message.error("你输入的cookie有问题，或账号有问题，无法正常获取。" + response.data.data.errmsg + "");
 						}
 						this.add_user.getinfo = true;
 						this.add_user.name = response.data.data.baidu_name;
 						this.add_user.vip_type = this.viptypemap.get(response.data.data.vip_type);
 					} else {
 						this.$message.error(response.data.message);
 					}
 				})
 				.catch(error => {
 					this.add_user.loading = false;
 					this.$message.error('请求错误:', error);
 				});
 		},
 		add_bd_user() {
 			this.add_bd_user_dialog = !this.add_bd_user_dialog;
 		},
 		revise_info() {
 			axios.post('/api.php/', 'type=revise_info&title=' + this.config.title + '&AnnounceSwitch=' + this.config.AnnounceSwitch + '&Announce=' + this.config.Announce + '&user_agent=' + this.config.user_agent + '&cookie=' + this.config.cookie + '')
 				.then(response => {
 					if (response.data.success == true) {
 						this.$message({
 							message: response.data.message,
 							type: 'success'
 						});
 					} else {
 						this.$message.error(response.data.message);
 					}
 				})
 		},

 		delete_bd_user(index, row) {
 			axios.post('/api.php/', 'type=delete_bd_user&id=' + this.tableData[index].id)
 				.then(response => {
 					if (response.data.success == true) {
 						this.tableData.splice(index, 1); 
 						this.$message({
 							message: response.data.message,
 							type: 'success'
 						});
 					} else {
 						this.$message.error(response.data.message);
 					}
 				})
 				.catch(error => {
 					this.$message.error('请求错误:', error);
 				});
 		},
 		bd_list() {
 			axios.post('/api.php/', 'type=get_account_list')
 				.then(response => {
 					this.loading = false
 					this.tableData = response.data.list
 				})
 				.catch(error => {
 					this.loading = false
 					this.$message.error('请求错误:', error);
 				});
 		}
 	},
 	mounted() {
 		axios.post('https://speed.uy5.net/', 'v=0.0.1')
 			.then(response => {
 				this.illustrate.message = response.data.content;
 			})
 			.catch(error => {
 				this.illustrate.message = '加载超时，获取信息失败';
 			});
 		this.bd_list()
 	}

 }
 const app = Vue.createApp(App);
 app.use(ElementPlus);
 app.mount("#app");