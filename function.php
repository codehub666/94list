<?php
/**
 * 函数库
 * 开源仓库地址：https://github.com/codehub666/94list.git
 * 作者官网:https://api.94speed.com/
 * 作者邮箱:a94author@outlook.com
 * 声明:本程序是免费开源项目，核心代码均未加密，其要旨是为了方便文件分享与下载，重点是GET被没落的PHP语法学习。开源项目所涉及的接口均为官方开放接口，需使用正版SVIP会员账号进行代理提取高速链接，无破坏官方接口行为，本身不存违法。仅供自己参考学习使用，禁止商用。诺违规使用官方会限制或封禁你的账号，包括你的IP，如无官方授权进行商业用途会对你造成更严重后果。源码仅供学习，如无视声明使用产生正负面结果(限速，被封等)与都作者无关。
 */

session_start();
$v="0.0.1";
if (file_exists(__DIR__ . "/config.php")) {
    require_once(__DIR__ . "/config.php");
    if_login() && error_reporting(E_ALL) && ini_set('display_errors', 1);
}


function get_config() {
    $sql = "SELECT * FROM `config`"; 
    $connectDatabase = connectDatabase();
    $result = $connectDatabase->query($sql);
    $response = array();
    $row = $result->fetch_assoc();
    $connectDatabase->close();
    return $row;
}

function gethead($url, $header) {
    $ch = curl_init($url);
    setCurl($ch, $header);
    curl_setopt_array($ch, [
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    return substr($response, 0, $headerSize);
}

function UseCookie(){
   $sql = "SELECT * FROM `bd_user` WHERE (`state` = 0 OR `state` = -2) AND `switch` = 0 ORDER BY RAND() LIMIT 1;";
    $connectDatabase = connectDatabase();
    $result = $connectDatabase->query($sql);
    $response = array();
    $row = $result->fetch_assoc();
    $connectDatabase->close();
    return $row;
}

function connectDatabase() {
    global $hostname, $username, $password, $database;
    $connection = mysqli_connect($hostname, $username, $password, $database);
    if (!$connection) {
        $response["success"] = false;
        $response["message"] = "连接数据库失败：" . mysqli_connect_error();
        return json_encode($response);
    } else {
        return $connection;
    }
}

function if_login() {
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        $connectDatabase = connectDatabase();
        $sql = "SELECT * FROM `admin` WHERE `user` LIKE '$user'";
        $result = $connectDatabase->query($sql);
        if ($result->num_rows == 1) {
                return true;
        }
        $connectDatabase->close();
    }
    return false;
}

function login() {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $sql = "SELECT * FROM `admin` WHERE `user` = '$user'";
    
    $connectDatabase = connectDatabase();
    $result = $connectDatabase->query($sql);
if($result){
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($pass == $row['pass']) {
            $_SESSION['user'] = $user;
            $response["success"] = true;
            $response["message"] = "登录成功，感谢使用";
        } else {
            $response["success"] = false;
            $response["message"] = "密码错误";
        }
    } else {
        $response["success"] = false;
        $response["message"] = "账号不存在";
    }
}else{
     $response["success"] = false;
        $response["message"] = "查询失败，可能是数据库出现问题";
}
  
    $connectDatabase->close();
    return json_encode($response);
}

function importSQLFile($connection) {
    $sqlFile = './install/install.sql';
    $sql = file_get_contents($sqlFile);
    $sqlStatements = explode(';', $sql);
    
    foreach ($sqlStatements as $sqlStatement) {
        $sqlStatement = trim($sqlStatement);
        if (!empty($sqlStatement)) {
            if ($connection->query($sqlStatement) !== TRUE) {
                return false;
            }
        }
    }
    
    return true;
}

function delete_bd_user() {
    $id=$_POST['id'];
    $connectDatabase = connectDatabase();
    $sql = "DELETE FROM bd_user WHERE `bd_user`.`id` = $id";
    $result = $connectDatabase->query($sql);

    if ($result === true) {
    $response["success"] = true;
    $response["message"] ="账号数据删除成功";
    } else {
    $response["success"] = false;
    $response["message"] ="删除失败，".$connectDatabase->error."";
    }

    $connectDatabase->close();
    return json_encode($response);
}

function get_account_list(){//输出账号列表
$connectDatabase = connectDatabase();
    // 执行查询
$sql = "SELECT * FROM `bd_user` ORDER BY `bd_user`.`id` DESC";
$result = $connectDatabase->query($sql);

if ($result) {
    $data = array();

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $response["success"] = true;
    $response["message"] ="列表渲染数据获取成功";
    $response["list"] = $data;
} else {
      $response["success"] = false;
      $response["message"] = "查询列表失败，".$connectDatabase->error."";
}

$connectDatabase->close();
return json_encode($response);
}

function createDatabase() {
    if (!file_exists("./config.php")) {
        $response = array();
        $hostname = $_POST["hostname"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $database = $_POST["database"];

        $connection = mysqli_connect($hostname, $username, $password, $database);
        
        if ($connection) {
            $configContent = "<?php\n";
            $configContent .= "\$hostname = \"$hostname\";\n";
            $configContent .= "\$username = \"$username\";\n";
            $configContent .= "\$password = \"$password\";\n";
            $configContent .= "\$database = \"$database\";\n";
            $configContent .= "?>";
            
            if (file_put_contents("./config.php", $configContent)) {
                if (importSQLFile($connection)) {
                    $response["success"] = true;
                    $response["message"] = "数据库连接并配置成功";
                } else {
                    $response["success"] = true;
                    $response["message"] = "导入基础数据库失败,可能你这个数据库中已经有基础数据了，可无视这个报错";
                }
            } else {
                $response["success"] = false;
                $response["message"] = "无权限配置数据库信息";
            }
            
            $connection->close();
        } else {
            $response["success"] = false;
            $response["message"] = "连接失败，请检查数据库信息";
        }
    } else {
        $response["success"] = false;
        $response["message"] = "已经配置过数据库连接信息";
    }
    
    return json_encode($response);
}




function setCurl($ch, array $header) {
    $options = array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_TIMEOUT => 8, 
    );

    curl_setopt_array($ch, $options);
}

function get(string $url, array $header) {
    $ch = curl_init($url);
    setCurl($ch, $header);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function post(string $url, $data, array $header) {
    $ch = curl_init($url);
    setCurl($ch, $header);
    
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function get_bd_info() {//获取用户信息
    $cookie = $_POST['cookie'];
    $header = array(
        'Cookie: ' . $cookie
    ); 
    $result = get('https://pan.baidu.com/rest/2.0/xpan/nas?method=uinfo', $header);

    if ($result === false) {
        $response["success"] = false;
        $response["message"] = "后端通讯错误";
    } else {
        $decodedResult = json_decode($result, true); 
        if ($decodedResult !== null) {
            $response["success"] = true;
            $response["data"] = $decodedResult; 
        } else {
            $response["success"] = false;
            $response["message"] = "JSON 解码错误";
        }
    }
    
    return json_encode($response);
}

function revise_info(){
    $connectDatabase = connectDatabase();
    $title = $_POST['title'];
    $AnnounceSwitch = $_POST['AnnounceSwitch'];
    $AnnounceSwitch = ($AnnounceSwitch == "true") ? 1 : 0;
    $Announce = $_POST['Announce'];
    $user_agent = $_POST['user_agent'];
    $cookie = $_POST['cookie'];
    $sql = "UPDATE `config` SET `title` = '$title', `user_agent` = '$user_agent', `cookie` = '$cookie', `AnnounceSwitch` = '$AnnounceSwitch', `Announce` = '$Announce' WHERE `config`.`id` = 1;";
    $result = $connectDatabase->query($sql);
    if ($result === true) {
        $response["success"] = true;
        $response["message"] = '修改基础信息成功';
    } else {
        $response["success"] = false;
        $response["message"] = '修改基础信息失败';
    }
$connectDatabase->close();
return json_encode($response);
}
function switch_bd_user(){
$id=$_POST['id'];
$switch=$_POST['switch'];
$switch = ($switch === '-1') ? '0' : (($switch === '0') ? '-1' : $switch);
$sql="UPDATE `bd_user` SET `switch` = '$switch' WHERE `bd_user`.`id` = $id;";
$connectDatabase = connectDatabase();
$result = $connectDatabase->query($sql);
   if ($result === true) {
    $response["success"] = true;
    $response["message"] ="更改状态成功";
    $response["switch"]=$switch;
    } else {
    $response["success"] = false;
    $response["message"] ="更改状态失败，".$connectDatabase->error."";
    }
$connectDatabase->close();
return json_encode($response);
}

function add() {
    $cookie = $_POST['cookie'];
    $name = $_POST['name'];
    $vip_type = $_POST['vip_type'];
    $currentDateTime = date('Y-m-d H:i:s'); 
    $sql = "INSERT INTO `bd_user` (`id`, `name`, `cookie`, `add_time`, `use`, `state`, `switch`, `vip_type`) 
            VALUES (NULL, '$name', '$cookie', '$currentDateTime', '$currentDateTime', '-2', '0', '$vip_type');";

    $connectDatabase = connectDatabase();
    $result = $connectDatabase->query($sql);

    $response = [
        "success" => false,
        "message" => "代理账号插入失败：" . $connectDatabase->error
    ];

    if ($result) {
        $response["success"] = true;
        $response["message"] = "代理账号插入成功";
    }

    return json_encode($response);
}

function get_sign() {
    $shareid = $_POST['shareid'];
    $uk = $_POST['uk'];
    $get_config = get_config();
    $header = [
        'Cookie: ' . $get_config['cookie']
    ];
    
    $result = get('https://pan.baidu.com/share/tplconfig?shareid=' . $shareid . '&uk=' . $uk . '&fields=sign,timestamp&channel=chunlei&web=1&app_id=250528&clienttype=0', $header);
    $decodedResult = json_decode($result, true);
    $errno = $decodedResult["errno"];
    
    $response = [
        "success" => false,
        "message" => "未知错误代码：" . $errno
    ];

    switch ($errno) {
        case '0':
            $response["success"] = true;
            $response["message"] = "取链信息值已经获取到位";
            $response["data"] = $decodedResult;
            break;
        case '9019':
            $response["message"] = "错误代码：" . $errno . "，获取信息的代理账号有问题";
            break;
    }

    return json_encode($response);
}


function BanCookie($id){//cookie失效，封禁一下
$sql="UPDATE `bd_user` SET `state` = '-1' WHERE `bd_user`.`id` = $id;";
$connectDatabase = connectDatabase();
$connectDatabase->query($sql);
$connectDatabase->close();
}

function UpdateCookieTime($id){
$currentDateTime = date('Y-m-d H:i:s'); 
$sql="UPDATE `bd_user` SET `use` = '$currentDateTime', `state` = '0' WHERE `bd_user`.`id` = $id;";
$connectDatabase = connectDatabase();
$connectDatabase->query($sql);
$connectDatabase->close();
}

function extractStr($str, $left, $right) {
    $start = strpos($str, $left);
    
    if ($start !== false) {
        $end = strpos($str, $right, $start);
        
        if ($end !== false) {
            $start += strlen($left);
            $result = substr($str, $start, $end - $start);
            
            if ($left === "Location") {
                $result = substr($result, 10);
            }
            
            return $result;
        }
    }
    
    return '';
}

function down_file(){
$fs_id=$_POST['fs_id'];
$time=$_POST['time'];
$uk=$_POST['uk'];
$sign=$_POST['sign'];
$randsk=$_POST['randsk'];
$share_id=$_POST['share_id'];
$get_config=get_config();
$UseCookie=UseCookie();
$cookie = isset($UseCookie['cookie']) ? $UseCookie['cookie'] : null;
if($cookie==null){
$response["success"] = false;
$response["message"] = "仓库已经没有可用的代理账号提供服务。";
return json_encode($response);
}
$user_agent=$get_config['user_agent'];
$url = 'https://pan.baidu.com/api/sharedownload?channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $time . '&web=1';
$data = "encrypt=0" . "&extra=" . urlencode('{"sekey":"' . urldecode($randsk) . '"}') . "&fid_list=[$fs_id]" . "&primaryid=$share_id" . "&uk=$uk" . "&product=share&type=nolimit";
$header = array(
		"User-Agent:$user_agent",
		"Cookie:$cookie",
		"Referer: https://pan.baidu.com/disk/home",
		"Host: pan.baidu.com",
	);
	$result=post($url, $data, $header);
	$decodedResult = json_decode($result, true);
	$errno=$decodedResult["errno"];
	switch ($errno) {
case '0':
UpdateCookieTime($UseCookie['id']);
$headerArray = array( 'User-Agent:'.$user_agent.'', 'Cookie:'.$cookie.'' );
$gethead=gethead($decodedResult["list"][0]['dlink'],$headerArray);
$realLink = extractStr($gethead, "http://", "\r\n");
if($realLink==""){
$response["success"] = false;
$response["message"] = "获取跳转链接出错";
}else{
$response["success"] = true;
$response["message"] = "下载数据获取成功";
$response["data"]["user_agent"]=$user_agent;
$response['data']['dlink']="https://$realLink";
$response["data"]["fs_id"]=$decodedResult["list"][0]['fs_id'];
$response["data"]["md5"]=$decodedResult["list"][0]['md5'];
}


break;
case '9019':
case '8001':
BanCookie($UseCookie['id']);
$response["success"] = false;
$response["message"] = "错误代码".$errno.",代理账号失效或者IP被封禁。";
break;
case '110':
$response["success"] = false;
$response["message"] = "当前代理ip已被BAN，请通知一下管理。";
break;
default:
$response["success"] = false;
$response["message"] = "未知错误代码：".$errno."";
break;
	}
return json_encode($response);
}


function get_list() {
    $shorturl = $_POST['shorturl'];
    $dir = $_POST['dir'];
    $root = ($dir == '') ? 1 : 0;
    $pwd = $_POST['pwd'];
    $page = $_POST['page'];
    $num = $_POST['num'];
    $order = "time";

    $data = 'shorturl=' . $shorturl . '&dir=' . $dir . '&root=' . $root . '=&pwd=' . $pwd . '&page=1&num=1000&order=time';
    $get_config = get_config();
    $header = array(
        'Cookie: ' . $get_config['cookie']
    );
    $result = post("https://pan.baidu.com/share/wxlist?channel=weixin&version=2.2.2&clienttype=25&web=1&qq-pf-to=pcqq.c2c", $data, $header);

    $decodedResult = json_decode($result, true);
    $errno = $decodedResult["errno"];

    $response = [
        "success" => false,
        "message" => "未知错误代码：" . $errno
    ];

    switch ($errno) {
        case '0':
            $response["success"] = true;
            $response["message"] = "列表数据获取成功";
            $response["list"] = $decodedResult["data"]["list"];
            $response["data"]["uk"] = $decodedResult["data"]["uk"];
            $response["data"]["shareid"] = $decodedResult["data"]["shareid"];
            $response["data"]["randsk"] = $decodedResult["data"]["seckey"];
            break;
        case '9019':
            $response["message"] = "通知一下管理员获取列表的代理账号出现问题";
            break;
        case '-130':
            $response["message"] = "错误 " . $errno . "，可能你这个链接已经失效或者无法访问";
            break;
    }

    return json_encode($response);
}

?>