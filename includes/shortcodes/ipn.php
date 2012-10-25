<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class shortcode_ipn{
	public function __construct(){
		add_shortcode('ipn', array($this, 'shortcode'));
	}

	public function shortcode(){
		$settings = get_option('justaquit_deals');

		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();

		foreach ($raw_post_array as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}

		$req = 'cmd=_notify-validate';

		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exits = true;
		}

		foreach ($myPost as $key => $value) {        
			if($get_magic_quotes_exits == true && get_magic_quotes_gpc() == 1) { 
				$value = urlencode(stripslashes($value)); 
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.paypal.com'));

		$res = curl_exec($ch);
		curl_close($ch);
 
		// assign posted variables to local variables
		$item_name        = $_POST['item_name'];
		$item_number      = $_POST['item_number'];
		$payment_status   = $_POST['payment_status'];
		$payment_amount   = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id           = $_POST['txn_id'];
		$receiver_email   = $_POST['receiver_email'];
		$payer_email      = $_POST['payer_email'];

		
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			if( $payment_status == 'Completed' ) :
				// check that txn_id has not been previously processed
				if( !Transaction::verify_txnid($txn_id) ) :
					// check that receiver_email is your Primary PayPal email
					if( $receiver_email == $settings['paypal_account'] ) :
						// check that payment_amount/payment_currency are correct
						$order = get_order( $item_number );
						if( $order->amount == $payment_amount ) :
							// Add transaction
							$transaction    = new Transaction($item_number, $payment_amount, $txn_id, 3);
							$transaction_id = $transaction->add_transaction();
							$coupon         = new Coupon($item_number);
							$coupon_id      = $coupon->add_coupon();

							$transaction = get_transaction($transaction_id);
							$coupon      = get_coupon($coupon_id);
							$product     = get_post($order->post_id);

							// Get order information
							$mail_to      = $order->email;
							$mail_subject = 'Your coupon from '.get_bloginfo();
							$mail_message = 'Your Order ID: '.$coupon->order_id."\n";
							$mail_message .= 'Product Name: '.$product->post_title."\n";
							$mail_message .= 'Product URL: '.get_permalink($product->ID)."\n";
							$mail_message .= 'Coupon code: '.$coupon->code."\n\n\n";
							$mail_message .= 'Thank you for purchase a promotion at '.get_bloginfo()."\n";
							$mail_message .= 'Problems? '.get_bloginfo('admin_email')."\n";
							$headers[]    = 'From: '.get_bloginfo().' <'.get_bloginfo('admin_email').'>';

							wp_mail( $mail_to, $mail_subject, $mail_message, $headers );
							wp_mail( get_bloginfo('admin_email'), $mail_subject, $mail_message, $headers );
						endif;
					endif;
				endif;
			endif;
		} else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			$transaction = new Transaction($item_number, 0, NULL, 1);
			$transaction->add_transaction();
		}
	}
}
$init = new shortcode_ipn();
?>