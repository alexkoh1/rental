<?
include("header.php");
?>
<a href="index.php?action=settings" class="btn btn-primary" >Настройки</a>	
<a href="index.php?action=logout" class="btn btn-danger" >Выйти</a>	
<h5>Локации:</h5>
	<?php foreach(get_locations() as $item ): ?>
		<a href="?locationid=<?=$item['id']?>" class="btn <?if ($item['id']==$_COOKIE['locationid']) : echo("btn-success"); else: echo("btn-primary"); endif?>"><?=$item['name']?></a>
	<?php endforeach ?>	
	<br/><br/>
	<? if(empty($_COOKIE['locationid'])) { ?>
		<h1>Выберите локацию!</h1>
		
	<?exit;	}?>

	<button class="btn btn-primary order_add" data-toggle="modal" data-target="#add">Добавить бронь</button>	
	<p>Сегодня <?=date("d-m-Y"); ?>.
	<table class="table table-bordered">
		<tr><th>Год</th><th>Выручка за сегодня, руб</th><th>Выручка за месяц, руб</th><th>Выручка за год, руб</th></tr>
		<tr><td>2016</td><td><?=revenue()?></td><td><?=revenue_history("cur_month");?></td><td><?=revenue_history("cur_year");?></td>
		<tr><td>2015</td><td><?=revenue_history()?></td><td><?=revenue_history("last_year_month");?></td><td><?=revenue_history("last_year");?></td>
	</table>
	<?php foreach(get_orders() as $order ): ?>
		<div class="order <?=get_bg_class($order['id'])['1']?>">
			<h3><?=get_bg_class($order['id'])['2']?></h3>
			<table class="table table-bordered">
				<tr>
					<th>Номер заказа</th>
					<th>Имя</th>
					<th>Телефон</th>
					<th>Дата начала</th>
					<th>Дата окончания</th>
					<th>Велосипед</th>
				</tr>
				<tr>
					<td><?=$order['id']?></td>
					<td><?=get_clients($order['phone'])['surname'] ?> <?=get_clients($order['phone'])['name'] ?></td>
					<td><?=$order['phone']?></td>
					<td><?=$order['time_started']?></td>
					<td><?=$order['time_finished']?></td>
					<td><?php foreach(get_bicycles($order['bicycle_id']) as $bicycle ): ?> <?=$bicycle['name']?></br> <? endforeach?></td>
				</tr>
			</table>
			<button class="btn btn-primary order_edit_<?=$order['id']?>" data-target="#add" data-toggle="modal" value="<?=$order['phone']?>">Редактировать заказ</button>
			<script>
				$('.order_edit_<?=$order['id']?>').click(function (){
					var text = $('.order_edit_<?=$order['id']?>').val();
					$('input[name=phone]').val(text);
					$('input[name=order_id]').val("<?=$order['id']?>");
					$('.selectpicker').selectpicker('val', [<?=implode(",", get_bicycles($order['bicycle_id'], "id")) ?>]);
					$('.selectpicker').selectpicker('render');
					$('#datetimepicker6').data("DateTimePicker").defaultDate("<?=$order['time_started']?>");
					$('#datetimepicker7').data("DateTimePicker").defaultDate("<?=$order['time_finished']?>");
					
				});
			</script>
			
			<?if (get_clients($order['phone'])) {?>
				<button class="btn btn-primary client_edit" data-target="#client_edit" data-toggle="modal" value="<?=$order['phone']?>">Редактировать паспортные данные</button>
			<? } else { ?>
				<button class="btn btn-primary client_edit" data-target="#client_edit" data-toggle="modal" value="<?=$order['phone']?>">Заполнить паспортные данные</button>
			<? } ?>
			<a href="?action=print_contract&id=<?=$order['id']?>" class="btn btn-primary" target="_blank" onclick="window.reload()">Напечатать договор</a> 
			<? if ($order['opened']==0) { ?>
				<!--<a href="?action=open_order&id=<?=$order['id']?>" class="btn btn-primary">Открыть заказ</a>-->
				<button class="btn btn-primary" id="open_order_<?=$order['id']?>" data-target="#open_order" data-toggle="modal" value="<?=$order['phone']?>">Открыть заказ</button>
				<script>
					$('#open_order_<?=$order['id']?>').click( function() {
						$('input[name=order_id]').val("<?=$order['id']?>");
						$.ajax({
							type: 'POST',
							url: 'index.php?action=get_order_info',
							data: 'id=<?=$order['id']?>',
							dataType: 'json',
							success: function(data){
								$('input[name=payment]').val(data.payment);
								$('.open_order').html(data.interval);
							}
						});
					});
				</script>
			<? } else if ($order['closed']==0) {?>
				<button class="btn btn-primary" id="close_order_<?=$order['id']?>" data-target="#close_order" data-toggle="modal">Закрыть заказ</button>
				<script>
					$(document).ready(function() { 
						var block = document.getElementById('sample_timer_<?=$order['id']?>');
						simple_timer(<?=(strtotime("now")-strtotime($order['time_started']))?>, block, true);
					});
				</script>
				<p class="bg-success" id="sample_timer_<?=$order['id']?>">00:00:00</p>
				
				
				<script>
				$('#close_order_<?=$order['id']?>').click( function() {
					$('input[name=order_id]').val("<?=$order['id']?>");
					$.ajax({
						type: 'POST',
						url: 'index.php?action=get_order_info&status=close',
						data: 'id=<?=$order['id']?>',
						dataType: 'json',
						success: function(data){
							//$('input[name=payment]').val(data.payment-data.payed);
							$('.open_order').html(data.interval);
							$('.qwe').html(data.payment);
							$('.asd').html(data.payed);
						}
					});
				});
				</script>
			<? } ?>
			<a class="btn btn-warning" href="?action=delete_order&order_id=<?=$order['id']?>">Удалить заказ</a>
		</div>
	<?php endforeach ?>
</div>
</div>
<div class="modal fade" id="client_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel2">Редактирование клиента</h4>
			</div>
			<div class="modal-body">
				<form role="form">
					<input type="hidden" name="client_id">
					<div class="form-group">
						<label for="phone">Телефон</label>
						<input name="phone" type="text" class="form-control">
					</div>
					<div class="form-group">
						<label for="surname">Фамилия</label>
						<input name="surname" type="text" class="form-control" id="surname" >
					</div>
					<div class="form-group">
						<label for="name">Имя</label>
						<input name="name" type="text" class="form-control" id="name" >
					</div>
					<div class="form-group">
							<label for="passport">Паспортные данные</label>
							<input name="passport" type="text" class="form-control" id="passport" >
					</div>
					<button type="button" class="btn btn-default close_modal" data-dismiss="modal">Закрыть</button>
					<input type="submit" class="btn btn-primary" value="Сохранить">
				</form>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="open_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel1">Открытие заказа</h4>
			</div>
			<div class="modal-body">
				<span class="open_order"></span>
				<form role="form">
					<input name="order_id" type="hidden" class="form-control">
					<input name="action" type="hidden" class="form-control" value="open_order">
					<div class="form-group">
						<label for="cash">Наличные, руб</label>
						<input name="cash" type="text" class="form-control order_edit" id="cash">
					</div>
					<div class="form-group">
						<label for="payment">Сумма к оплате</label>
						<input name="payment" type="text" class="form-control order_edit" id="payment">
					</div>
					<div class="form-group">
						<label for="change">Сдача</label>
						<input name="change" type="text" class="form-control order_edit" id="change">
					</div>
					<script>
						$("input[name=cash]").keyup(function () {
							$("input[name=change]").val($("input[name=cash]").val()-$("input[name=payment]").val());
						});
					</script>

					<button type="button" class="btn btn-default close" data-dismiss="modal">Закрыть</button>
					<input type="submit" class="btn btn-primary" value="Сохранить">
				</form>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="close_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel1">Закрытие заказа</h4>
			</div>
			
			<div class="modal-body">
				<span class="open_order"></span>
				<span class="qwe"></span>
				<span class="asd"></span>
				<form role="form">
					<input name="order_id" type="hidden" class="form-control">
					<input name="action" type="hidden" class="form-control" value="close_order">
					<div class="form-group">
						<label for="cash">Наличные, руб</label>
						<input name="cash" type="text" class="form-control order_edit" id="cash">
					</div>
					<div class="form-group">
						<label for="payment">Сумма к оплате</label>
						<input name="payment" type="text" class="form-control order_edit" id="payment">
					</div>
					<div class="form-group">
						<label for="change">Сдача</label>
						<input name="change" type="text" class="form-control order_edit" id="change">
					</div>
					<script>
						$("input[name=cash]").keyup(function () {
							$("input[name=change]").val($("input[name=cash]").val()-$("input[name=payment]").val());
						});
					</script>

					<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
					<input type="submit" class="btn btn-primary" value="Сохранить">
				</form>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Добавить бронь</h4>
			</div>
			<div class="modal-body">
				<form role="form">
					<input name="order_id" type="hidden" class="form-control">
					<div class="form-group">
						<label for="phone">Телефон</label>
						<input name="phone" type="text" class="form-control order_edit" id="phone">
					</div>
					<div class="form-group">
						<label for="name">Велосипед</label>
						<select multiple="multiple" name="bicycle_id[]" class="form-control selectpicker">
							<?php foreach(get_bicycles() as $item ): ?>
								<option value="<?=$item['id']?>"><?=$item['name']?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div class="form-group">
						<label for="datetimepicker6">Время начала</label>
						<div class='input-group date' id='datetimepicker6'>
							<input name="time_started" type='text' class="form-control" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label for="time">Время аренды</label>
						
						<input name="time" type="text" class="form-control order_edit" id="time">
						<select name="time_sh" class="form-control">
							<option value="hours">часов</option>
							<option value="days">дней</option>
						</select>
					</div>
					<div class="form-group">
						<label for="datetimepicker7">Время окончания</label>
						<div class='input-group date' id='datetimepicker7'>
							<input name="time_finished" type='text' class="form-control" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				
					<script type="text/javascript">
						$(function () {
							$('#datetimepicker6').datetimepicker({
								format: "YYYY-MM-DD HH:mm:ss"
							});
							$('#datetimepicker7').datetimepicker({
								format: "YYYY-MM-DD HH:mm:ss"
							});
							$("#datetimepicker6").on("dp.change", function (e) {
								$('#datetimepicker7').data("DateTimePicker").minDate(e.date);
							});
							$("#datetimepicker7").on("dp.change", function (e) {
								$('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
							});
						});
					</script>
					<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
					<input type="submit" class="btn btn-primary" value="Сохранить">
				</form>
			</div>
		</div>
	</div>
</div>