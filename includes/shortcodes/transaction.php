<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class shortcode_transaction{

	public function __construct(){
		add_shortcode('transaction', array($this, 'shortcode'));
	}

	public function shortcode(){
		if ( !wp_verify_nonce($_POST['deals_checkout'],'checkout') )
			$error = 1;
		else
			$error = 0;
		if(isset($_POST['the_id']))
			$ID = $_POST['the_id'];
		else
			$error = 1;
		if(isset($_POST['time_buy']))
			$time_buy = $_POST['time_buy'];
		else
			$error = 1;
		if(isset($_POST['contact_email']))
			$contact_email = $_POST['contact_email'];
		else
			$error = 1;
		if(isset($_POST['contact_first_name']))	
			$contact_first_name = $_POST['contact_first_name'];
		else
			$error = 1;
		if(isset($_POST['contact_last_name']))
			$contact_last_name = $_POST['contact_last_name'];
		else
			$error = 1;

		if($error==1) :
			echo 'You haven\'t order a product yet, please choose a product.'."\n";
		else :
			$time_current       = strtotime( current_time('mysql') );
			$time_diff          = $time_current - $time_buy;

			if( $time_diff > 130 && is_active($ID) ):
				echo 'Sorry, your time has expire, you have to place again your order.'."\n";
			else:
				//product_functions::buys_count($product_id);
				$price = get_price_by_time($ID, $time_buy);

				$order = new Order($contact_email, $contact_first_name, $contact_last_name, $ID, $price, $time_buy);
				$order_id = $order->add_order();

				if( $order_id ) :
					$order = get_order($order_id);
					$post  = get_post($order->post_id);
					$url  = 'https://www.paypal.com/cgi-bin/webscr?business=mydealisideal@gmail.com&cmd=_xclick&currency_code=USD';
					$url .= '&amount='.$order->amount;
					$url .= '&item_name='.$post->post_title;
					$url .= '&item_number='.$order->ID;
					$url .= '&shipping=0';
					$url .= '&notify_url='.home_url('/store/transaction/ipn/').'';
					$url .= '&return='.home_url('/sucessfull/');

					echo 'Please wait, you will be redirected to PayPal in a few seconds...';
					echo '
						<script type="text/javascript">
							window.location = "'.$url.'"
						</script>
					';
				endif;
			endif;
		endif;
	}
}

?>