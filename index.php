<?
include ("model/functions.php");
if (!empty($_GET[login])) {
	if (auth($_GET[login], $_GET[password])) {
		setcookie("login", $_GET['password']);
		header("Location: ?");
	} 	
}
if (empty($_COOKIE['login'])) {
	require_once("view/login.php");
	exit;
}
if (!empty($_GET['locationid'])) {
	setcookie("locationid", $_GET['locationid']);
	header("Location: ?");
}
setlocale(LC_TIME, "ru_RU");
setlocale(LC_ALL, 'ru_RU','rus_RUS','Russian');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//print_r( DateTimeZone::listIdentifiers( ) );
date_default_timezone_set("Asia/Yekaterinburg");

if (!empty($_GET['number'])) {
	require_once("model/log_call.php");
} else if (!empty($_GET['bicycle_id'])) {
	if (!empty($_GET['order_id'])) {
		sql_query("update orders set phone='$_GET[phone]', time_started='$_GET[time_started]', time_finished='$_GET[time_finished]', bicycle_id='".implode(",", $_GET['bicycle_id'])."' where id='$_GET[order_id]'");
	} else {
		sql_query("insert into orders (phone, time_started, time_finished, bicycle_id, hours) values ('$_GET[phone]', '$_GET[time_started]', '$_GET[time_finished]', '".implode(",", $_GET['bicycle_id'])."', '".$_GET['time']."')");
	}
	//print_r($_GET);
	//echo (strtotime($_GET['date_start']));
	header("Location:index.php");
} else if (strstr($_GET['action'], "print_contract")) {
	require_once("view/print_contract.php");
} else if (strstr($_GET['action'], "settings")) {
	if (strstr($_GET['action_s'], "add_bike")) {
		sql_query("insert into bicycles (name, cost, colour, payment_policy_id) values('$_GET[name]', '$_GET[bike_cost]', '$_GET[bike_colour]', '$_GET[payment_pocily_id]')");
		Header("Location: ?action=settings&tab=bicycles");
	} else if (strstr($_GET['action_s'], "del_bike")) {
		sql_query("delete from bicycles where id=".$_GET[id]);
		Header("Location: ?action=settings&tab=bicycles");
	} else if (strstr($_GET['action_s'], "edit_bike")) {
		sql_query("UPDATE `bicycles` SET  cost='".$_GET[bike_cost]."', name='".$_GET[name]."', colour='".$_GET[bike_colour]."', payment_policy_id='".$_GET[payment_pocily_id]."', location='".$_GET[locationid]."' WHERE `bicycles`.`id` = ".$_GET[id]." LIMIT 1");
		Header("Location: ?action=settings&tab=bicycles");
	} else if (!empty($_GET['action_s'])) {
		connect_db();
		$action=explode("-", $_GET['action_s']);
		if ($action[0]=="edit") {
			$result=mysql_query("SHOW FULL COLUMNS FROM ".$action[1]);
			$query="UPDATE ".$action[1]." set ";
			while ($row = mysql_fetch_assoc($result)) {
				$query=$query.$row[Field]."='".$_GET[$row[Field]]."', ";
			}
			$query=substr($query, 0, -2);
			$query=$query." where id=".$_GET[id];
			sql_query($query);
			Header("Location: ?action=settings&tab=".$action[1]);
		} else if ($action[0]=="add") {
			print "qweqweqwe";
			$query="insert into ".$action[1];
			$result=mysql_query("SHOW FULL COLUMNS FROM ".$action[1]);
			while ($row = mysql_fetch_assoc($result)) {
				$vars=$vars.", ".$row[Field];
				$values=$values.", '".$_GET[$row[Field]]."'";
			}
			$vars=substr($vars, 1);
			$values=substr($values, 1);
			$query=$query."(".$vars.") values (".$values.")";
			sql_query($query);
			Header("Location: ?action=settings&tab=".$action[1]);
		} else if ($action[0]=="del") {
			$query="delete from ".$action[1]." where id=".$_GET[id];
			sql_query($query);
			Header("Location: ?action=settings&tab=".$action[1]);
		}
	}
		
	require_once("view/settings.php");
} else if (strstr($_GET['action'], "open_order")) {
	sql_query("update orders set opened='1', time_started='".date("Y-m-d H:i:s")."', time_finished='".date("Y-m-d H:i:s", strtotime("+".get_orders($_GET['order_id'], "hours")." hours 15 minutes"))."' where id=$_GET[order_id]");
	sql_query("insert into payments (sum, order_id) values('$_GET[payment]', '$_GET[order_id]')");

	$phone = str_replace("-", "", get_orders($_GET['order_id'], "phone"));
	$phone=substr_replace($phone, "7", "0", "1");
	$time = date("H:i", strtotime(get_orders($_GET['order_id'], "time_finished")));
	send_sms($phone, "Заказ открыт. Ждём вас в ".$time.". Приятной покатушки! Veloprokatufa.ru 266-44-80");
	header("Location:index.php");
} else if (strstr($_GET['action'], "get_order_info")) {
	//(get_order_payment($_POST['id']));
	//echo(json_encode(get_order_payment($_POST['id']), JSON_UNESCAPED_UNICODE));
	if ($_GET['status']=="close") {
		sql_query("update orders set time_finished='".date("Y-m-d H:i:s")."' where id='$_POST[id]'");
		
	 	$phone = str_replace("-", "", get_orders($_POST['id'], "phone"));
		$phone=str_replace("8", "7", $phone);
		send_sms($phone, "Спасибо за ваш заказ! Ждём вас снова! Veloprokatufa.ru 266-44-80");
		
	}
	
	echo(json_encode(get_order_payment($_POST['id']), JSON_UNESCAPED_UNICODE));
} else if (strstr($_GET['action'], "close_order")) {
	sql_query("update orders set closed='1', time_finished='".date("Y-m-d H:i:s")."' where id='$_GET[order_id]'");
	foreach (explode(",", get_orders($_GET[order_id])[0]['bicycle_id']) as $item) {
		sql_query("update bicycles set location=".$_COOKIE['locationid']." where id=".$item);
	}
		
	sql_query("insert into payments (sum, order_id) values('$_GET[payment]', '$_GET[order_id]')");
	//echo("update bicycles set location=".$_COOKIE['locationid']." where id IN ".$row['bicycle_id']);
	header("Location:index.php");
} else if (strstr($_GET['action'], "delete_order")) {
	sql_query("update orders set deleted='1' where id='$_GET[order_id]'");
	header("Location:index.php");
}else if (strstr($_GET['action'], "testtest")) {
	echo(json_encode(get_clients($_POST['phone']), JSON_UNESCAPED_UNICODE)) ;
} else if (strstr($_GET['action'], "reports")) {
	require_once("view/reports.php");
} else if (strstr($_GET['action'], "login")) {
	require_once("view/login.php");
} else if (strstr($_GET['action'], "logout")) {
	setcookie('login');
	header("Location:index.php");
}
else if (!empty($_GET['passport'])) {
	if (!empty($_GET['client_id'])) {
		sql_query("update clients set phone='$_GET[phone]', name='$_GET[name]', surname='$_GET[surname]', passport_number='$_GET[passport]' where id='$_GET[client_id]'");
	} else {
		sql_query("insert into clients (name, surname, phone, passport_number) values ('$_GET[name]', '$_GET[surname]', '$_GET[phone]', '$_GET[passport]')");
	}
	header("Location:index.php");
} else {
	require_once("view/main_window.php");
}


?>