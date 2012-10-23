jQuery(document).ready(function($){
	$('#delete').click(function(){
		var price = $(this).parents('tr').find('#price').html();
		var total = $('#total').html();
		price     = parseFloat(price);
		total     = parseFloat(total);
		total			= total - price;
		$(this).parents('tr').fadeOut('slow', function(){
			var position = $(this).find('input').attr('disabled', 'disabled');
			console.log(position);
			$('#total').html(total);
		});
	});

	$.timer(1000, function(){
		var time_server = $('#time_server').attr('value');
		time_server     = parseInt(time_server);
		var time_buy    = $('#time_buy').attr('value');
		time_buy        = parseInt(time_buy);
		var time_remain = 120 - (time_server - time_buy);
		if( time_remain <= 0 ){
			var mensaje = 'Sorry, your time has expire, you have to place again your order.';
			$('.countdown').html(mensaje);
		} else {
			$('#countdown').html(time_remain);
		}

		time_server++;
		$('#time_server').attr('value', time_server);
	});
});