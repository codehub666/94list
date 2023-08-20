const App = {
	data() {
		return {
			taskurl: "", 
			pass: "", 
			taskstate: false, 
			shorturl: null,
			user_agent: user_agent,
			list: [], 
			rw_list: [],
			DownDialog: false, 
			get_list_loading: false,
			Currentdirectory: "", 
			pl_Currentdirectory: "",
			settingdialog: false, 
			pathopen: true,
			selectdownlist: [], 
			AnnounceSwitch: AnnounceSwitch,
			Announce: Announce,
		};
	},


	watch: {
		taskurl(newValue) {
			const regex = /pwd=([a-zA-Z0-9]{4})/;
			const match = newValue.match(regex);
			if (match && match[1]) {
				const password = match[1];
				this.$message.success('检测链接存在密码，已为您自动输入')
				this.pass = password;
			}
		},
		DownDialog(newVal) {
			if (!newVal) {
				this.rw_list = [];
			}
		}
	},
	methods: {
		copy(text, message) {
			var textarea = document.createElement("textarea");
			textarea.value = text;
			document.body.appendChild(textarea);
			textarea.select();
			document.execCommand("copy");
			document.body.removeChild(textarea);

			if (message) {
				this.$message({
					message: message,
					type: 'success'
				});
			}
		},
		senddown(url, filename, port) { 
			console.log(url)
			console.log(filename)
			console.log(port)
			const jsonrpc = '2.0';
			const id = 'YOUR_ID';
			const method = 'aria2.addUri';
			const params = [
				[url],
				{
					'out': filename,
					'header': ['User-Agent:' + this.user_agent + '']
				}
			];
			axios.post('http://localhost:' + port + '/jsonrpc', {
					jsonrpc,
					id,
					method,
					params
				})
				.then(response => {
					this.$message.success('已把 ' + filename + ' 任务发送给下载器')
				})
				.catch(error => {
					this.$message.error('发送失败，可能相对应的下载器没有启动')
				});
		},
		get_file(dir) { 
			this.selectedValues = [];
			this.taskstate = true
			axios.post('/api.php/', 'type=get_list&shorturl=' + this.shorturl + '&dir=' + dir + '&root=0&pwd=' + this.pass + '&page=1&num=1000&order=time')
				.then(response => {
					this.taskstate = false
					this.list = response.data.list 
				})
		},
		down_file(fs_id, timestamp, uk, sign, randsk, shareid, server_filename, downpath) { 
			this.DownDialog = true;
			var data = {
				fs_id: fs_id,
				name: server_filename,
				DownState: "1", 
				dlink: "获取中",
				downpath: downpath, 
			};
			var addedIndex = this.rw_list.push(data) - 1;

			axios.post('/api.php/', 'type=down_file&fs_id=' + fs_id + '&time=' + timestamp + '&uk=' + uk + '&sign=' + sign + '&randsk=' + randsk + '&share_id=' + shareid + '')
				.then(response => {
					if (response.data.success) {
						this.rw_list[addedIndex].dlink = response.data.data.dlink;
						this.rw_list[addedIndex].DownState = 0;

					} else {
						if (response.data.message == undefined) {
							response.data.message = "服务器请求失败。";
						}
						this.$message.error("" + server_filename + " 获链失败。原因：" + response.data.message + "");
						this.rw_list[addedIndex].dlink = response.data.message;
					}

				})
		},

		SelectedRows(row) {
			this.selectdownlist = row; 
		},
		pl_down() { 
			this.$message('正在创建批量任务，稍等一下');
			this.selectdownlist.forEach(row => {
				if (row.isdir == "1") {
					this.$message.error('文件夹下载暂时关闭,未获取 "' + row.server_filename + '" 文件夹内容');
				} else {
					this.down_file(row.fs_id, this.timestamp, this.uk, this.sign, this.randsk, this.shareid, row.server_filename, )
				}
			});


		},


		clickfile(scope) { 
			if (scope.isdir == 1) { 
				this.get_file(scope.path);
			} else { 
				this.down_file(scope.fs_id, this.timestamp, this.uk, this.sign, this.randsk, this.shareid, scope.server_filename, )
			}
		},
		formatBytes(bytes, decimals = 2) {
			if (bytes === 0) return '0 Bytes';

			const k = 1024;
			const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

			const i = Math.floor(Math.log(bytes) / Math.log(k));
			return parseFloat((bytes / Math.pow(k, i))
				.toFixed(decimals)) + ' ' + sizes[i];
		},
		formatTimestamp(timestamp) {
			const date = new Date(timestamp * 1000);
			const options = {
				year: 'numeric',
				month: 'long',
				day: 'numeric',
			};
			return date.toLocaleDateString(undefined, options);
		},
		extractParam() { 
			var regex = /s\/([a-zA-Z0-9_-]+)/;
			var match = this.taskurl.match(regex);
			if (match) {
				return match[1];
			} else {
				regex = /surl=([a-zA-Z0-9_-]+)/;
				match = this.taskurl.match(regex);
				if (match) {
					return '1' + match[1] + '';
				} else {
					return false
				}
			}
		},
		analyze() {
			this.selectedValues = [];
			this.pl_Currentdirectory = "";
			this.Currentdirectory = "";
			this.taskstate = true
			this.shorturl = this.extractParam(); 
			if (this.shorturl == false) {
				this.$message.error('链接错误，获取失败');
				this.taskstate = false;
				return
			}
			axios.post('/api.php/', 'type=get_list&shorturl=' + this.shorturl + '&dir=&root=0&pwd=' + this.pass + '&page=1&num=1000&order=time')
				.then(response => {
					if (response.data.success) {
						this.uk = response.data.data.uk 
						this.shareid = response.data.data.shareid 
						this.randsk = response.data.data.randsk
						this.list = response.data.list 
					} else {
						this.$message.error(response.data.message);
						this.taskstate = false 
						return
					}
					axios.post('/api.php', 'type=get_sign&shareid=' + this.shareid + '&uk=' + this.uk + '')
						.then(response => {
							if (response.data.success) {
								this.sign = response.data.data.data.sign
								this.timestamp = response.data.data.data.timestamp
							} else {
								this.$message.error(response.data.message);
								return
							}
							this.taskstate = false
						})
				})
		},

	}

}
const app = Vue.createApp(App);
app.use(ElementPlus);
app.mount("#app");