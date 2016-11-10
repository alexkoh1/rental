<?php
include("header.php");
?>

  <a href="index.php" class="btn btn-primary" >Вернуться к бронированию</a>	
  <!-- Навигация -->
  <ul class="nav nav-tabs" role="tablist">
    <li <?if ($_GET['tab']=="bicycles") print "class='active'";?>><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Велосипеды</a></li>
    <li <?if ($_GET['tab']=="payment_policy") print "class='active'";?>><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Политики оплаты</a></li>
    <li <?if ($_GET['tab']=="locations") print "class='active'";?>><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Локации</a></li>
    <li <?if ($_GET['tab']=="clients") print "class='active'";?>><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Клиенты</a></li>
  </ul>
  <!-- Содержимое вкладок -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane <?if ($_GET['tab']=="bicycles") print "active in";?>" id="home">
		<table class="table table-bordered">
			<tr><th>№</th><th>Название</th><th>Стоимость велосипеда</th><th>Цвет</th><th>Политика списания</th><th>Локация</th><th>Редактировать</th><th>Удалить</th>
			<?php foreach(get_bicycles() as $bicycle ): ?>
				
				<tr><td><?=$bicycle['id']?></td><td><?=$bicycle['name']?></td><td><?=$bicycle['cost']?></td><td><?=$bicycle['colour']?></td><td><?=$bicycle['payment_policy_id']?></td><td><?=get_locations($bicycle['location'])[0]['name']?></td><td><button act="edit_bike" bike_name="<?=$bicycle['name']?>" id="<?=$bicycle['id']?>" bike_cost="<?=$bicycle['cost']?>" bike_colour="<?=$bicycle['colour']?>"  bike_payment_policy_id="<?=$bicycle['payment_policy_id']?>"  bike_locationid="<?=$bicycle['location']?>" class="btn btn-primary edit_bicycle" data-toggle="modal" data-target="#bicycle" >Редактировать</button></td><td><a href="?action=settings&action_s=del_bike&id=<?=$bicycle['id']?>"class="btn btn-primary">Удалить</a></td></tr>
			<?php 
			endforeach ?>
		</table>
		<button act="add_bike" class="btn btn-primary edit_bicycle" data-toggle="modal" data-target="#bicycle">Добавить велосипед</button>
	</div>
    <div role="tabpanel" class="fade tab-pane <?if ($_GET['tab']=="payment_policy") print "active in";?>" id="profile">
		<? print_r(render_table("payment_policy"))?>
	</div>
    <div role="tabpanel" class="fade tab-pane <?if ($_GET['tab']=="locations") print "active in";?>" id="messages">
		<? print_r(render_table("locations"))?>
	</div>
    <div role="tabpanel" class="fade tab-pane <?if ($_GET['tab']=="clients") print "active in";?>" id="settings">
		<? print_r(render_table("clients"))?>
	</div>
  </div>
</div>

<script>
$('.edit_bicycle').click( function() {
	console.log($(this).text());
	if ($(this).text()=='Редактировать') {
		console.log($(this).attr("act"));
		$('input[name=action_s]').val($(this).attr("act"));
		$('input[name=id]').val($(this).attr("id"));
		$('input[name=name]').val($(this).attr("bike_name"));
		$('input[name=bike_cost]').val($(this).attr("bike_cost"));
		$('input[name=bike_colour]').val($(this).attr("bike_colour"));
		$('select[name=payment_pocily_id]').val($(this).attr("bike_payment_policy_id")).change()
		$('select[name=locationid]').val($(this).attr("bike_locationid")).change()
	} else {
		console.log("asdasdasasdasd");
		$('input[name=action_s]').val($(this).attr("act"));
		$('input[name=name]').val("");
		$('input[name=bike_cost]').val("");
		$('input[name=bike_colour]').val("");
		$('select[name=payment_pocily_id]').val($(this).attr("bike_payment_policy_id")).change();
	}
});
</script>

<div class="modal fade" id="bicycle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel1">Добавить велосипед</h4>
			</div>
			
			<div class="modal-body">
				<form role="form" >
					<input name="action" type="hidden" class="form-control" value="settings">
					<input name="id" type="hidden" class="form-control">
					<input name="action_s" type="hidden" class="form-control">
					<div class="form-group">
						<label for="name">Название</label>
						<input name="name" type="text" class="form-control" id="name">
					</div>
					<div class="form-group">
						<label for="bike_cost">Стоимость велосипеда</label>
						<input name="bike_cost" type="text" class="form-control" id="bike_cost">
					</div>
					<div class="form-group">
						<label for="bike_colour">Цвет</label>
						<input name="bike_colour" type="text" class="form-control" id="bike_colour">
					</div>
					<div class="form-group">
						<label for="change">Политика списания</label>
						<select name="payment_pocily_id" class="form-control selectpicker">
							<?php foreach(get_policies() as $item ): ?>
								<option value="<?=$item['id']?>"><?=$item['policy_name']?> первый час:<?=$item['first_hour']?>, следующий час: <?=$item['next_hour']?>, максимальное время<?=$item['max_hour']?>, сутки<?=$item['day']?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div class="form-group">
						<label for="change">Локация</label>
						<select name="locationid" class="form-control selectpicker">
							<?php foreach(get_locations() as $item ): ?>
								<option value="<?=$item['id']?>"><?=$item['name']?> <?=$item['address']?></option>
							<?php endforeach ?>
						</select>
					</div>


					<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
					<input type="submit" class="btn btn-primary" value="Сохранить">
				</form>
			</div>
		</div>
	</div>
</div>