$(document).ready(function() { 
	$(".client_edit").click(function (){
		$('input[name=phone]').val($(this).val());
		$.ajax({
			type: 'POST',
			url: 'index.php?action=testtest',
			data: 'phone='+$(this).val(),
			dataType: 'json',
			success: function(data){
				$('input[name=client_id]').val(data.id);
				$('input[name=name]').val(data.name);
				$('input[name=surname]').val(data.surname);
				$('input[name=passport]').val(data.passport_number);
			}
		});
	});

	$('#add').on('hidden.bs.modal', function (e) {
		$('input[type=text]').val('');
	});
	
	$("input[name=time]").keyup(function () {
		var date = new Date($('#datetimepicker6').data("DateTimePicker").date())
		date.setTime(date.getTime()+3600000*$('#time').val());
		$('#datetimepicker7').data("DateTimePicker").date(date);
	});
	
	$("#phone").mask("8-999-999-99-99");
});



function simple_timer(sec, block, direction) {
    var time    = sec;
    direction   = direction || false;
             
    var hour    = parseInt(time / 3600);
    if ( hour < 1 ) hour = 0;
    time = parseInt(time - hour * 3600);
    if ( hour < 10 ) hour = '0'+hour;
 
    var minutes = parseInt(time / 60);
    if ( minutes < 1 ) minutes = 0;
    time = parseInt(time - minutes * 60);
    if ( minutes < 10 ) minutes = '0'+minutes;
 
    var seconds = time;
    if ( seconds < 10 ) seconds = '0'+seconds;
 
    block.innerHTML = hour+':'+minutes+':'+seconds;
 
    if ( direction ) {
        sec++;
 
        setTimeout(function(){ simple_timer(sec, block, direction); }, 1000);
    } else {
        sec--;
 
        if ( sec > 0 ) {
            setTimeout(function(){ simple_timer(sec, block, direction); }, 1000);
        } else {
            alert('Время вышло!');
        }
    }
}