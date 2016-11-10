<?
function connect_db() {
	$link = mysql_connect('localhost', 'veloprokat', 'ghjrfndtkjd');
	mysql_select_db('veloprokat');
	mysql_query('SET NAMES utf8 COLLATE utf8_general_ci');	
}

function auth($login, $password) {
	connect_db();
	$result=mysql_query("select count(*) as count from users where login='".$login."' and password='".$password."'");
	if (mysql_fetch_assoc($result)['count']=='1') {
		return true;
	} else {
		return false;
	}
}
function render_table ($table) {
	connect_db();
	$result=mysql_query("SHOW FULL COLUMNS FROM ".$table);
	$return=$return."<table class='table table-bordered'><tr>";
	$fields=array();
	while ($row = mysql_fetch_assoc($result)) {
		$return=$return."<th>".$row[Comment]."</th>";
		$fields[$row[Field]]=$row[Comment];
    }
	$return=$return."<th>Редактировать</th><th>Удалить</th></tr>";
	$query="select * from ".$table;
	$res=mysql_query($query);
	$result=array();
	while ($row=mysql_fetch_assoc($res)) {
		$return=$return."<tr>";
		$parametrs="";
		foreach ($row as $key => $therow) {
			$return=$return."<td>".$therow."</td>";
			$parametrs=$parametrs.$key."='".$therow."' ";
		}
		$return=$return."<td><button class='btn btn-primary ".$table."' ".$parametrs." act='edit-".$table."' data-toggle='modal' data-target='#".$table."'>Редактировать</button></td><td><a class='btn btn-primary edit_bicycle' href='?action=settings&action_s=del-".$table."&id=".$row[id]."'>Удалить</a></tr>";
	}
	$return=$return."
	</table>
	<button act='add-".$table."' class='btn btn-primary ".$table."' data-toggle='modal' data-target='#".$table."'>Добавить</button>
	<script>
	$('.".$table."').click( function() {
		console.log($(this).text());
		if ($(this).text()=='Редактировать') {
			console.log($(this).attr('act'));
			$('input[name=action_s]').val($(this).attr('act'));\n";
			foreach ($fields as $key => $value) {
				$return=$return."$('input[name=".$key."]').val($(this).attr('".$key."'));\n";
			}
		$return=$return."
		} else {
			console.log($(this).attr('act'));
			$('input[name=action_s]').val($(this).attr('act'));\n";
			foreach ($fields as $key => $value) {
				$return=$return."$('input[name=".$key."]').val($(this).attr(''));\n";
			}
		$return=$return."
				$('input[name=id]').prop('disabled', true);
		}
	});
	</script>
	
	<div class='modal fade' id='".$table."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel1' aria-hidden='true'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<h4 class='modal-title' id='myModalLabel1'>".$table."</h4>
			</div>
			
			<div class='modal-body'>
				<form role='form' >
					<input name='action' type='hidden' class='form-control' value='settings'>
					<input name='id' type='hidden' class='form-control'>
					<input name='action_s' type='hidden' class='form-control'>";
					foreach ($fields as $key => $value) {
						$return=$return."
						<div class='form-group'>
							<label for='".$key."'>".$value."</label>
							<input name='".$key."' type='text' class='form-control'>
						</div>
						";
						
					}
					$return=$return."
					<button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button>
					<input type='submit' class='btn btn-primary' value='Сохранить'>
				</form>
			</div>
		</div>
	</div>
</div>
";
	
	return $return;
}	


function get_locations($location_id=NULL) {
	connect_db();
	if (!empty($location_id)) {
		$query="select * from locations where id=".$location_id;
	} else {
		$query="select * from locations";
	}
	$res=mysql_query($query);
	$result=array();
	while ($row=mysql_fetch_assoc($res)) {
		if (!empty($field)) {
			$result[]=$row[$field];
		} else {
			$result[]=$row;
		}
	}
	return $result;
}
function get_bicycles($bycicle_id=NULL,$field=NULL) {
	connect_db();
	if (!empty($bycicle_id)) {
		$query="select * from bicycles where id IN ($bycicle_id)";
	} else {
		if (empty($_COOKIE['locationid']) or $_GET['action']=='settings') {
			$query="select * from bicycles";
		} else {
			$query="select * from bicycles where location=".$_COOKIE[locationid];
		}
	}
	$res=mysql_query($query);
	$result=array();
	while ($row=mysql_fetch_assoc($res)) {
		if (!empty($field)) {
			$result[]=$row[$field];
		} else {
			$result[]=$row;
		}
	}
		return $result;
}

function get_policies() {
	connect_db();
	if (!empty($policy_id)) {
		$query="select * from bicycles where id IN ($bycicle_id)";
	} else {
		$query="select * from payment_policy";
	}
	$res=mysql_query($query);
	$result=array();
	while ($row=mysql_fetch_assoc($res)) {
		if (!empty($field)) {
			$result[]=$row[$field];
		} else {
			$result[]=$row;
		}
	}
		return $result;
}


function get_orders($order=NULL,$field=NULL ) {
	connect_db();
	if (!empty($order)) {
		$query="select * from orders where id='$order'";
	} else {
		$query="select * from orders where deleted=0 and closed=0 order by id DESC";
	}
	$res=mysql_query($query);
	$result=array();
			// $bicycle=get_bicycles(explode(",", $row['bicycle_id'])[0]);
		// if ($row['opened']==1)
			// $result[]=$row;
		// elseif ($bicycle[0][location]==$_COOKIE['locationid'] and $_GET[status]!='close') {
			// $result[]=$row;
		// } else {
	
	while ($row=mysql_fetch_assoc($res)) {
			$result[]=$row;
	}
	if (!empty($field)) {
		return $result[0][$field];
	} else {
		return $result;
	}
}

function get_bg_class($order_id) {
	connect_db();
	$query="select * from orders where id='$order_id'";
	$res=mysql_query($query);
	$row=mysql_fetch_assoc($res);
	if ($row['opened']==0) {
		$result[1]="bg-primary";
		$result[2]="Заказ создан";
	} else if ($row['closed']==1) {
		$result[1]="bg-info";
		$result[2]="Заказ закрыт";
	} else if ($row['opened']==1) {
		$result[1]="bg-success";
		$result[2]="Заказ открыт";
	}
	return $result;
}

function get_order_payment($order_id) {
	connect_db();
	$date1=date_create(get_orders($order_id, "time_finished"));
	$date2=date_create(get_orders($order_id, "time_started"));
	$diff=date_diff($date1,$date2);
	$day=$diff->format("%d");
	$hour=$diff->format("%h");
	$min=$diff->format("%i");
	$hourmin=$hour+$min/60;
	//$day=gmdate('Y', strtotime(get_orders($order_id, "time_finished"))-strtotime(get_orders($order_id, "time_started")));
	//$hour=gmdate('H', strtotime(get_orders($order_id, "time_finished"))-strtotime(get_orders($order_id, "time_started")));
	//$min=gmdate('i', strtotime(get_orders($order_id, "time_finished"))-strtotime(get_orders($order_id, "time_started")))/60;
	//$time=$hour+$min;
	//$result=get_orders($order_id);
	$bicycles=explode(",", get_orders($order_id)[0]['bicycle_id']);
	$result=$bicycles;
	$payment=0;
//echo "qwe".$hour;
	foreach ($bicycles as $bicycle) {
		$res=mysql_fetch_assoc(mysql_query("select * from payment_policy where id in (select payment_policy_id from bicycles where id='".$bicycle."')"));
		if ($day>0) {
			$payment=$payment + $day*$res['day'];
			//echo "+1+";
		}
		if ($hourmin>$res['max_hour']) {
			$payment=$payment + $day*$res['day'];
			//echo "+2+";
		}
		if ($hourmin<=$res['max_hour'] and $hourmin>1) {
			$payment=$payment + $res['first_hour'] + ($hourmin-1)*$res['next_hour'];
			//echo "+3+";
		}
		if ($hourmin<=1) {
			$payment=$payment + $hourmin*$res['first_hour'];
			//echo "+4+";
		}
	}
	
	$payment=round($payment, -1);
	$result['interval']=$day." дней и ".$hour." часов и ".$min." минут";
	$result['payment']=$payment;
	$result['payed']=mysql_fetch_assoc(mysql_query("select sum from payments where order_id='$order_id'"))['sum'];
	return $result;
}

function get_clients($phone) {
	connect_db();
	$query="select * from clients where phone='$phone'";
	if (!empty($phone)) {
		$res=mysql_query($query);
		while ($row=mysql_fetch_assoc($res)) {
			$result=$row;
		}
	} else {
		$result="error";
	}
	return $result;
}


function send_sms($number, $text) {
	$login="t89613593774";
	$pass="134331";
	$text1=$text;
	$text=urlencode($text);
	$result=file_get_contents("http://$login:$pass@gate.prostor-sms.ru/send/?phone=$number&text=$text&sender=veloprokat");
	if (strstr($result, "accept")) {
		sql_query("insert into sms_log(text, number, status) values ('".$text1."', '".$number."', '1')");
	} else {
		sql_query("insert into sms_log(text, number, status) values ('".$text1."', '".$number."', '0')");
	}
}
function sql_query($query) {
	connect_db();
	mysql_query($query);
}

function revenue() {
	connect_db();
	$revenue=mysql_fetch_assoc(mysql_query("select sum(sum) as sum from payments where time>=CURDATE() order by id DESC"));
	return $revenue['sum'];
	
}

function revenue_history($param=NULL) {
	connect_db();
	if ($param=="cur_month") {
		$curdate=date("Y-m");
		$revenue=mysql_fetch_assoc(mysql_query("SELECT sum(sum) as sum FROM payments WHERE time like '%".$curdate."%'"));
	} else if ($param=="cur_year") {
		$curdate=date("Y");
		$revenue=mysql_fetch_assoc(mysql_query("SELECT sum(sum) as sum FROM payments WHERE time like '%".$curdate."%'"));
	} else if ($param=="last_year_month") {
		$curdate=date("m");
		$revenue=mysql_fetch_assoc(mysql_query("SELECT sum(sum) as sum FROM payments WHERE time like '%".$curdate."%' and YEAR(time) = 2015"));
	} else if ($param=="last_year") {
		//$curdate=date("m");
		$revenue=mysql_fetch_assoc(mysql_query("SELECT sum(sum) as sum FROM payments WHERE YEAR(time) = 2015"));
	} else {
		$curdate=date("m-d");
		$revenue=mysql_fetch_assoc(mysql_query("SELECT sum(sum) as sum FROM payments WHERE time like '%".$curdate."%' and YEAR(time) = 2015"));
	}
	return $revenue['sum'];
}

?>