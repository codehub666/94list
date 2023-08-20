   const App = {
   	data() {
   		return {
   			user: "",
   			pass: ""
   		}
   	},
   	methods: {
   		login() {
   			axios.post('/api.php', 'type=login&user=' + this.user + '&pass=' + this.pass + '')
   				.then(response => {
   					if (response.data.success == true) { //数据库连接成功
   						this.$message({
   							message: response.data.message,
   							type: 'success'
   						});
   						window.location.href = "./";
   					} else { //数据库连接失败
   						this.$message.error(response.data.message);
   					}
   				})
   		}
   	}

   }
   const app = Vue.createApp(App);
   app.use(ElementPlus);
   app.mount("#app");