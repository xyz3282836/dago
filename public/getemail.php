<?php

// 设置区
$CONF_SQLSERVER = "127.0.0.1:3306";
$CONF_SQLUSER   = "root";
$CONF_SQLPASS   = "123456";
$CONF_SQLDB     = "devlinepro";
$CONF_HOSTPASS  = "SharePass2";
$CONF_APIPASS   = "SharePass3";

// 模式切换
switch ($_GET["mode"]) {
    case 'createsql':
        createsql();
        break;

    case 'api':
        api();
        break;

    case 'apiresult':
        apiresult();
        break;

    case 'apisimple':
        apisimple();
        break;

    case 'host':
        host();
        break;

    case 'hostresult':
        hostresult();
        break;

    default:
        echo "Hello.";
}

// 通用 SQL 查询工具函数
function sqlquery($sql, $info_success, $info_error)
{

    //  连接数据库
    $sqlcon = mysql_connect($GLOBALS["CONF_SQLSERVER"], $GLOBALS["CONF_SQLUSER"], $GLOBALS["CONF_SQLPASS"]);
    if (!$sqlcon) {
        die('无法连接数据库<br>' . mysql_error());
    }

    mysql_select_db($GLOBALS["CONF_SQLDB"], $sqlcon);

    $result = mysql_query($sql, $sqlcon);
    if ($result) {
        echo $info_success . "<br>";
    } else {
        echo $info_error . "<br>";
    }

    return $result;

    // 关闭连接
    mysql_close($sqlcon);

}

// 首次创建数据表
function createsql()
{

    //  创建数据表
    $sql = "use devlinepro;CREATE TABLE Querys (
		ReturnValue varchar(5),
		User varchar(30),
		Pass varchar(30),
		Link varchar(2000),
		Error varchar(2000),
		Time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	)";
    sqlquery($sql, "首次创建数据表成功", "首次创建数据表失败");

}

// 提交查询请求
function api()
{

    $user = $_GET["user"];
    $pass = $_GET["pass"];

    $sql = "use devlinepro;INSERT INTO Querys (ReturnValue, User, Pass) VALUES ('query', '" . $user . "', '" . $pass . "')";
    echo $sql;
    sqlquery($sql, "", "");

}

// 获取最新 5 条结果记录
function apiresult()
{

    if ($_GET["apipass"] != $GLOBALS["CONF_APIPASS"]) {
        die('{
			"return": "false",
			"user": null,
			"link": null,
			"time ": null,
			"error": "Api 查询密码错误！"
		}');
    }

    $sql    = "use devlinepro;SELECT * FROM Querys";
    $result = sqlquery($sql, "", '{
			"return": "false",
			"user": null,
			"link": null, 
			"time ": null,
			"error": "数据库查询错误！"
		}');

    $i = 0;
    while ($row = mysql_fetch_array($result)) {
        $array[$i] = '{
			"return": "' . $row['ReturnValue'] . '",
			"user": "' . $row['User'] . '",
			"link": "' . $row['Link'] . '",
			"time": "' . $row['Time'] . '",
			"error": null
		}';
        $i         = $i + 1;
    }
    echo "[" . $array[count($array) - 1] . ", " . $array[count($array) - 2] . ", " . $array[count($array) - 3] . ", " . $array[count($array) - 4] . ", " . $array[count($array) - 5] . "]";

}

// 简易版本查询
function apisimple()
{

    if ($_GET["apipass"] != $GLOBALS["CONF_APIPASS"]) {
        die('Api 查询密码错误！');
    }

    $sql    = "use devlinepro;SELECT * FROM Querys WHERE user='" . $_GET["queryname"] . "'";
    $result = sqlquery($sql, "", '数据库查询错误！');

    $i = 0;
    while ($row = mysql_fetch_array($result)) {
        $array[$i] = $row['Link'];
        $i         = $i + 1;
    }
    echo $array[count($array) - 1];

}

// Host 端后台
function host()
{

    if ($_GET["hostpass"] != $GLOBALS["CONF_HOSTPASS"]) {
        die('Host 端密码错误！');
    }

    $sql    = "use devlinepro;SELECT * FROM Querys";
    $result = sqlquery($sql, "任务列表查询成功", "任务列表查询失败");

    $i = 0;
    while ($row = mysql_fetch_array($result)) {
        $array[$i] = '{
			"return": "' . $row['ReturnValue'] . '",
			"user": "' . $row['User'] . '",
			"pass": "' . $row['Pass'] . '",
			"link": "' . $row['Link'] . '",
			"time": "' . $row['Time'] . '",
			"error": "null"
		}';
        $i         = $i + 1;
    }
    echo '<a id="first" href="#">初始化</a><br/><a id="run" href="#">直接开始运行服务器</a><p id="json">' . $array[count($array) - 1] . '</p>';

}

// Host 端提交
function hostresult()
{

    $user = $_GET["user"];
    $link = str_replace("LINKAND", "&", $_GET["link"]);
    $sql  = "use devlinepro;INSERT INTO Querys (ReturnValue, User, Link) VALUES ('true', '" . $user . "', '" . $link . "')";
    sqlquery($sql, "提交结果成功 页面将在 5s 后关闭", "提交结果失败 页面将在 5s 后关闭");

}

?>

