jQuery(document).ready(function($){
	$.timer(1000, function(){
		$('form.product_details').each(function(){
			// Variables
			var price_real     = $(this).find('#price_real').attr('value');
			var price_max      = $(this).find('#price_max').attr('value');
			var price_min      = $(this).find('#price_min').attr('value');
			var time_active    = $(this).find('#time_active').attr('value');
			var time_expire    = $(this).find('#time_expire').attr('value');
			var time_current   = $(this).find('#time_current').attr('value');

			// Parse Variables
			price_real         = parseInt(price_real);
			price_max          = parseInt(price_max);
			price_min          = parseInt(price_min);
			time_active        = parseInt(time_active);
			time_expire        = parseInt(time_expire);
			time_current       = parseInt(time_current);
			// Calculations
			var time           = time_expire - time_current;
			var time_onair     = time_current - time_active;
			if( time > 0 && time_onair > 0 ) {
				var time_total     = time_expire - time_active;
				var price_diff     = price_max - price_min;
				var price          = price_min + ( time_onair * price_diff ) / time_total;
				var discount       = 100 - ( ( 100 * price ) / price_real );
				var time_hours     = time / 3600;
				time_hours         = parseInt(time_hours);
				var time_mins      = ( time % 3600 ) / 60;
				time_mins          = parseInt(time_mins);	
				var time_secs      = time - ( ( time_hours * 3600 ) + ( time_mins * 60 ) );
				// Final Variables
				price              = '$ '+Math.round( price * 1000 )/1000;
				discount           = Math.round( discount * 100 )/100;
				time               = time_hours+':'+time_mins+':'+time_secs;
				// Replacement
				$(this).find('span#price').html(price);
				$(this).find('span#discount').html(discount);
				$(this).find('span#time').html(time);
			} else {
				var discount       = 100 - ( ( 100 * price_max ) / price_real );
				discount           = Math.round( discount * 100 )/100;
				// Replacement
				$(this).find('span#price').html('$ '+price_max);
				$(this).find('span#discount').html(discount);
				$(this).find('span#time').html('Active');
			}
			time_current++;
			$(this).find('#time_current').attr('value', time_current);
		});
	});
});