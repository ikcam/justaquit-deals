<?php
/*
Plugin Name: JustAquit Products
Plugin URI: http://justaquit.com
Description: This plugins allows you to use your WordPress installation as a offers system.
Version: 1.0
Author: Irving Kcam
Author URI: http://ikcam.com
License: GPL2
*/
?>
<?php
class product_functions {
	/*
	@@ Función: get_categories @@
	Args:
		- (Int) count: Número de elementos requeridos
	*/
	public function get_categories( $count=10 ){
		// Globalize $wpdb for SQL queries
		global $wpdb;
		// Vars
		$time_current = strtotime( current_time('mysql') );
		// Query
		$query = "SELECT * FROM $wpdb->terms WHERE term_id IN ( SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id IN ( SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ) ) AND term_id IN ( SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' AND parent = 0 ) ORDER BY term_id DESC LIMIT 0,%d";
		$categories = $wpdb->get_results( $wpdb->prepare( $query, $count ) );
		// Result
		if( !$categories )
			return false;
		else
			return $categories;
	}

	/*
	@@ Función: get_category_posts @@
	Argumentos:
		- (Int) cat_id: ID de la categoría.
		- (Int) count: Número de elementos requeridos.
	Devuelve posts activos de una categoría específica.
	*/
	public function get_category_posts( $cat_id, $count ){
		// Globalize $wpdb for SQL queries
		global $wpdb;
		// Vars
		$time_current = strtotime( current_time('mysql') );
		// Query
		$query = "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND ID IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d ) ORDER BY post_date DESC LIMIT 0, %d";
		$posts = $wpdb->get_results( $wpdb->prepare( $query, $cat_id, $count ) );
		// Result
		if( !$posts )
			return false;
		else
			return $posts;
	}

	/*
	@@ Función: views_count @@
	Contador de la cantidad de vistas de un producto
	*/
	function views_count(){
		if( is_single() && !is_admin() ){
			$ID   = get_the_ID();
			$post = get_post($ID);

			$count = get_post_meta( $post->ID, '_product_views', TRUE );

			if( !$count )
				$count = 1;
			else {
				$count = intval( $count );
				$count++;				
			}
			add_post_meta($post->ID, '_product_views', $count, true) or update_post_meta( $post->ID, '_product_views', $count );
		}
	}

	/*
	@@ Función: buys_count @@
	Contador de la canridad de compras de un producto
	*/
	function buys_count($ID){
		$ID    = intval( $ID );
		$count = get_post_meta( $ID, '_product_buys', TRUE );

		if( !$count )
			$count = 1;
		else {
			$count = intval( $count );
			$count++;
		}

		add_post_meta($post->ID, '_product_buys', $count, true) or update_post_meta( $post->ID, '_product_buys', $count );
	}

	function details_script(){
		$ID = get_the_ID();
		if( product_functions::product_active( $ID ) && is_single() ) :
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-timer', plugins_url('js/timer.jquery.js',__FILE__) );
			wp_enqueue_script( 'jquery-details', plugins_url('js/details.jquery.js',__FILE__) );
		endif;
	}

	function get_transaction_by_order( $ID ){
		$ID = intval($ID);

		global $wpdb;
		$table       = $wpdb->prefix.'transactions';
		$query       = "SELECT * FROM $table WHERE order_id = %d";
		$transaction = $wpdb->get_row( $wpdb->prepare( $query, $ID ) );

		if( $transaction)
			return $transaction;
		else
			return false;
	}

	function get_coupon_by_order( $ID ){
		$ID = intval($ID);

		global $wpdb;
		$table  = $wpdb->prefix.'coupons';
		$query  = "SELECT * FROM $table WHERE order_id = %d";
		$coupon = $wpdb->get_row( $wpdb->prepare( $query, $ID ) );

		if( $coupon)
			return $coupon;
		else
			return false;
	}

	function get_coupon_by_code( $code ){
		global $wpdb;
		$table = $wpdb->prefix.'coupons';
		$query = "SELECT * FROM $table WHERE code = %s";
		$coupon = $wpdb->get_row( $wpdb->prepare( $query, $code ) );

		if( $coupon )
			return $coupon;
		else
			return false;
	}

	function get_order_status( $ID ){
		$ID = intval( $ID );

		global $wpdb;
		$table = $wpdb->prefix.'transactions';
		$query = "SELECT * FROM $table WHERE order_id = %d";
		$transaction = $wpdb->get_row( $wpdb->prepare($query, $ID) );
		$status = $transaction->status;

		if( $status == 3 )
			return true;
		else
			return false;
	}

	/*
	@@ Función: shortcode_details
	*/
	function shortcode_details(){
		// Get post information
		$ID = get_the_ID();
		$post = get_post($ID);
		$views = get_post_meta( $post->ID, '_product_views', TRUE );

		// If post is active execute
		$output  = "\n".'<form class="product_details" method="post" action="'.get_bloginfo('url').'/store/checkout" >';
		$output .= product_functions::show_inputs( $post->ID );
	  $output .= "\n\t".'<input type="hidden" name="price" id="price" value="'.product_functions::price_current().'" />';
	  $output .= "\n\t".'<input type="hidden" name="the_id" id="the_id" value="'.get_the_ID().'" />';
	  $output .= "\n\t".'<input type="hidden" name="url" id="url" value="'.get_permalink().'" />';
	  $output .= "\n\t".'<div class="top">';
	  $output .= "\n\t\t".'<div class="product_time">';
	  $output .= "\n\t\t\t".'<span id="time">'.product_functions::time_current().'</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="discount">';
	  $output .= "\n\t\t\t".'<span id="discount">'.product_functions::discount_current().'</span>% savings';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="price">';
	  $output .= "\n\t\t\t".'<span id="price">$'.product_functions::price_current().'</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<input type="submit" value="Buy Now!" />';
	  $output .= "\n\t".'</div>';
	  $output .= "\n\t".'<div class="bottom">';
	  $output .= "\n\t\t".'<div class="views">';
	  $output .= "\n\t\t\t".'<span id="views">'.$views.' Views</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="gift">';
	  $output .= kk_star_ratings( $post->ID );
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="share">';
	  $output .= "\n\t\t\t".'<div class="fb-like" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false" data-action="like" data-font="arial"></div>';
		$output .= "\n".'<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-hashtags="mydealisideal">Tweet</a>';
		$output .= "\n".'<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		$output .= "\n".'<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
<script type="IN/Share" data-counter="right"></script>';
		$output .= "\n".'<div class="g-plus" data-action="share" data-annotation="bubble"></div>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t".'</div>';
	  $output .= "\n".'</form>';
	  $output .= "\n".'<div class="clearfix"></div>';

		// Result
		return $output;
	}
} // End of class: product_functions
add_action( 'wp_head', array('product_functions', 'views_count') );
add_action( 'wp_enqueue_scripts', array('product_functions', 'details_script') );
add_shortcode( 'details', array('product_functions', 'shortcode_details')  );

class product_slider {
	/*
	@@ Función: slider_enqueue @@
	Scripts necesarios para la ejecución del slider
	*/
	function slider_enqueue(){
		if( is_front_page() ){
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('jquery-timer', plugins_url('js/timer.jquery.js', __FILE__) );
			wp_enqueue_script('jquery-slider', plugins_url('js/slider.jquery.js', __FILE__) );
		}
	}

	function get_all_products( $count ){
		$args = array(
			'numberposts'     => $count,
			'offset'          => 0,
			'orderby'         => 'rand',
			'meta_key'        => '_product_expire',
			'post_type'       => 'post',
			'post_status'     => 'publish'
		);
		$posts = get_posts($args);

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_new_products( $count ){
		$args = array(
			'numberposts'     => $count,
			'offset'          => 0,
			'orderby'         => 'post_date',
			'meta_key'        => '_product_expire',
			'post_type'       => 'post',
			'post_status'     => 'publish'
		);
		$posts = get_posts($args);

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_ending_products( $count ){
		global $wpdb;
		$time_current = strtotime( current_time('mysql') );

		$query = "SELECT * FROM $wpdb->posts AS a	INNER JOIN $wpdb->postmeta AS b ON a.ID = b.post_id WHERE a.post_type = 'post' AND a.post_status = 'publish' AND b.meta_key = '_product_expire' AND b.meta_value > %d ORDER BY b.meta_value ASC LIMIT 0,%d";
		$posts = $wpdb->get_results( $wpdb->prepare( $query, $time_current, $count ) );

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_gone_products( $count ){
		global $wpdb;
		$time_current = strtotime( current_time('mysql') );

		$query = "SELECT * FROM $wpdb->posts AS a	INNER JOIN $wpdb->postmeta AS b ON a.ID = b.post_id WHERE a.post_type = 'post' AND a.post_status = 'publish' AND b.meta_key = '_product_expire' AND b.meta_value < %d ORDER BY b.meta_value ASC LIMIT 0,%d";
		$posts = $wpdb->get_results( $wpdb->prepare( $query, $time_current, $count ) );

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_views_products( $count ){
		$args = array(
			'numberposts'     => $count,
			'offset'          => 0,
			'orderby'         => 'meta_value_num',
			'order'           => 'DESC',
			'meta_key'        => '_product_views',
			'post_type'       => 'post',
			'post_status'     => 'publish'
		);
		$posts = get_posts($args);

		if( $posts )
			return $posts;
		else
			return false;
	}
} // End of class: product_slide

// Call WordPress actions for product_slider
add_action( 'wp_enqueue_scripts', array('product_slider', 'slider_enqueue')  );
// End of Call WordPress actions for product_slider


class product_transaction{
	function scripts(){
		if( is_page( 'Checkout' ) ){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-timer', plugins_url( 'js/timer.jquery.js', __FILE__ ) );
			wp_enqueue_script( 'jquery-checkout', plugins_url( 'js/checkout.jquery.js', __FILE__ ) );
      wp_register_style( 'style-checkout', plugins_url('css/checkout.css', __FILE__) );
			wp_enqueue_style( 'style-checkout' );
		}
	}

	/*
	@@ Función: shortcode_checkout @@
	LLamar las funciones necesarias.
	*/
	function shortcode_checkout(){
		$product_id  = $_POST['the_id'];
		$product_time_buy = $_POST['time_current'];
		$server_time_buy = strtotime( current_time('mysql') );
		$server_time_buy = $server_time_buy;
		$time_remain = 120 - ( $server_time_buy - $product_time_buy );

		if( (($time_remain <= 0 ) || ( !$product_id )) && product_functions::product_active( $product_id ) ) :
			echo '<div class="countdown">Sorry, your time has expire, you have to place again your order.</div>';
		else :
			$post = get_post($product_id);
			$total = 0;
?>
	<form action="<?php bloginfo('url') ?>/store/transaction" method="post">
		<input type="hidden" name="time_buy" id="time_buy" value="<?php echo $product_time_buy ?>" />
		<input type="hidden" name="time_server" id="time_server" value="<?php echo $server_time_buy ?>" />
		<table class="table-checkout">
		<tbody>
			<tr>
				<td colspan="6"><h4>PRODUCTS</h4></td>
			</tr>
			<tr class="product-item" id="product-<?php echo $post->ID ?>">
				<input type="hidden" name="the_id" value="<?php echo $post->ID ?>" />
				<td class="thumb">
					<?php echo get_the_post_thumbnail( $post->ID, array(100,100,TRUE) ); ?>
				</td>
				<td colspan="4">
					<strong><?php echo $post->post_title ?></strong>
					<br />
					<small>
						<a id="delete" href="#">Delete</a> | 
						<a id="view" href="<?php echo get_permalink($post->ID) ?>" target="_blank">View</a>
					</small>
				</td>
				<td class="price">
					$
					<span id="price"><?php 
						$total = $total + product_functions::price_post($post->ID);
						$total = floor($total*100)/100;
						echo floor(product_functions::price_post($post->ID)*100)/100;
					?></span>
				</td>
			</tr>
			<tr class="total">
				<td colspan="5"><strong>Total</strong></td>
				<td class="price"><strong>$<span id="total"><?php echo $total ?></span></strong></td>
			</tr>
			<tr>
				<td colspan="6">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="6"><h4>BILLING/CONTACT DETAILS</h4></td>
			</tr>
			<tr><!-- Email -->
				<td colspan="2"><label for="contact_email">Email *</td>
				<td><input type="email" name="contact_email" id="contact_email" required /></td>
			</tr>
			<tr><!-- First Name -->
				<td colspan="2"><label for="contact_first_name">First Name *</td>
				<td><input type="text" name="contact_first_name" id="contact_first_name" required /></td>
			</tr>
			<tr><!-- Last Name -->
				<td colspan="2"><label for="contact_last_name">Last Name *</td>
				<td><input type="text" name="contact_last_name" id="contact_last_name" required /></td>
			</tr>
			<tr><!-- Pay with PayPal -->
				<td colspan="5">
				</td>
				<td class="price">
					<a id="cancel" href="#">Cancel</a> 
					or
					<input type="submit" value="Buy Now">
				</td>
			</tr>
		</tbody>
		</table>
	</form>
<?php if( product_functions::product_active( $product_id ) ) : ?>
	<div class="countdown">
		Hurry up! You have <span id="countdown"><?php echo $time_remain ?></span> seconds left.
	</div>
<?php endif; ?>
<?php
		endif;
	}

	function shortcode_transaction(){
		$product_id         = $_POST['the_id'];
		$product_id         = floor( $product_id );
		$time_buy           = $_POST['time_buy'];
		$time_buy           = floor( $time_buy );
		$time_current       = strtotime( current_time('mysql') );
		$time_diff          = $time_current - $time_buy;
		$contact_email      = $_POST['contact_email'];
		$contact_first_name = $_POST['contact_first_name'];
		$contact_last_name  = $_POST['contact_last_name'];

		if( !$product_id ) :
			echo 'You haven\'t order a product yet, please choose a product.'."\n";
		else :
			if( $time_diff > 130 && product_functions::product_active( $product_id ) ) :
				echo 'Sorry, your time has expire, you have to place again your order.'."\n";
			else :
				product_functions::buys_count($product_id);
				$price = product_functions::price_by_time( $product_id, $time_buy );

				$order = 	array(
						'email'      => $contact_email,
						'first_name' => $contact_first_name,
						'last_name'  => $contact_last_name,
						'post_id'    => $product_id,
						'amount'     => $price,
						'date_time'  => $time_buy
					);
				$order_id = product_transaction::order_insert($order);
				if ( $order_id ) :
					$post = get_post( $order['post_id'] );

					$url  = 'https://www.paypal.com/cgi-bin/webscr?business=mydealisideal@gmail.com&cmd=_xclick&currency_code=USD';
					$url .= '&amount='.$order['amount'];
					$url .= '&item_name='.$post->post_title;
					$url .= '&item_number='.$order_id;
					$url .= '&shipping=0';
					$url .= '&notify_url='.home_url('/store/transaction/ipn/').'';
					$url .= '&return='.home_url('/sucessfull/');

					echo 'Please wait, you will be redirected to PayPal in a few seconds...';
					echo '
					<script type="text/javascript">
						window.location = "'.$url.'"
					</script>
					';
				else:
					
				endif;
			endif;
		endif;
	}

	function redirect($url) {
		header('Location: '.$url);
		exit();
	}

	function order_insert( $args ){
		global $wpdb;

		$table = $wpdb->prefix.'orders';
		$format = array( '%s', '%s', '%s', '%d', '%s', '%d' );
		$wpdb->insert( $table, $args, $format );
		$id = $wpdb->insert_id;

		if( $id )
			return $id;			
		else
			return false;
	}

	function transaction_insert( $args ){
		global $wpdb;

		$table = $wpdb->prefix.'transactions';
		$format = array( '%d', '%s', '%s', '%d' );
		$wpdb->insert( $table, $args, $format );
		$id = $wpdb->insert_id;

		if( $id )
			return true;
		else
			return false;
	}

	function shortcode_transaction_ipn(){
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
				global $wpdb;
				$table = $wpdb->prefix.'transactions';
				$query = "SELECT * FROM $table WHERE txn_id = %s";
				$check = $wpdb->get_results( $wpdb->prepare( $query, $txn_id ) );
				if( !$check ) :
					// check that receiver_email is your Primary PayPal email
					if( $receiver_email == 'mydealisideal@gmail.com' ) :
						// check that payment_amount/payment_currency are correct
						$order = product_functions::get_order( $item_number );
						if( $order->amount == $payment_amount ) :
							// Add transaction
							$transaction = array(
									'order_id' => $item_number,
									'amount'   => $payment_amount,
									'txn_id'   => $txn_id,
									'status'   => 3
								);
							$transaction_id = product_transaction::transaction_insert( $transaction );

							$coupon = array(
									'order_id'    => $item_number,
									'coupon_date' => strtotime( current_time('mysql') ),
									'code'        => product_coupon::coupon_code(6),
									'status'      => 0,
									'usage_date'  => NULL
								);
							$coupon_id = product_coupon::coupon_create( $coupon );
							$product = get_post( $order->post_id );

							// Get order information
							$mail_to      = $order->email;
							$mail_subject = 'Your coupon from '.get_bloginfo();
							$mail_message = 'Your Order ID: '.$coupon['order_id']."\n";
							$mail_message .= 'Product Name: '.$product->post_title."\n";
							$mail_message .= 'Product URL: '.get_permalink($product->ID)."\n";
							$mail_message .= 'Coupon code: '.$coupon['code']."\n\n\n";
							$mail_message .= 'Thank you for purchase a promotion at '.get_bloginfo()."\n";
							$mail_message .= 'Problems? '.get_bloginfo('admin_email')."\n";
							$headers[]    = 'From: '.get_bloginfo().' <'.get_bloginfo('admin_email').'>';
							$headers[]    = 'Cc: '.get_bloginfo('admin_email');
							wp_mail( $mail_to, $mail_subject, $mail_message, $headers );

						endif;
					endif;
				endif;
			endif;
		} else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			echo 'nada que ver';
		}
	}
}
add_shortcode( 'checkout', array('product_transaction', 'shortcode_checkout') );
add_shortcode( 'transaction', array('product_transaction', 'shortcode_transaction') );
add_shortcode( 'transaction_ipn', array('product_transaction', 'shortcode_transaction_ipn') );
add_action( 'wp_enqueue_scripts', array('product_transaction', 'scripts') );

class product_coupon{
	function coupon_create( $args ){
		global $wpdb;

		$table = $wpdb->prefix.'coupons';
		$format = array( '%d', '%d', '%s', '%d', '%d' );
		$wpdb->insert( $table, $args, $format );
		$id = $wddb->insert_id;

		if( $id )
			return $id;
		else
			return false;
	}

	function coupon_gen_code( $length=6 ){
		$key = '';
		list($usec, $sec) = explode(' ', microtime());
		mt_srand((float) $sec + ((float) $usec * 100000));
		$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

		for($i=0; $i<$length; $i++) {
			$key .= $inputs{mt_rand(0,61)};
		}

		return $key;
	}

	function coupon_code( $length=6 ){
		global $wpdb;

		do {
			$code = product_transaction::coupon_gen_code($length);
			$table = $wpdb->prefix.'coupons';
			$query = "SELECT * FROM $table WHERE code = %s";
			$check = $wpdb->get_results( $wpdb->prepare( $query, $code ) );
		} while ( $check );

		return $code;
	}

	function set_active( $ID ){
		$ID = intval( $ID );
		$ID = array( 'ID' => $ID );

		global $wpdb;

		$args   = array(
				'status'     => 1,
				'usage_date' => strtotime( current_time('mysql') )
			);
		$format = array( '%d', '%d' );
		$table  = $wpdb->prefix.'coupons';
		$wpdb->update( $table, $args, $ID, $format, array( '%d' ) );
	}

	function shortcode_coupons(){
		$action = $_GET['action'];

		if( $action == 'use_coupon' ) :
			$code   = $_GET['code'];
			$coupon = product_functions::get_coupon_by_code( $code );
			// Verify if coupon exists
			if( $coupon ):
				$order = product_functions::get_order($coupon->order_id);
				$product  = get_post( $order->post_id );
				switch( $coupon->status ){
					case 0:
						$status = 'Valid Coupon';
						break;
					case 1:
						$status = 'Already used coupon';
						break;
					default:
						$status = 'Error, contact support@mydealisideal.com';
				}
?>
<h3>Coupon Information</h3>
<h5>Status</h5>
	<?php echo $status ?>
<h5>Client Information</h5>
	First Name: <?php echo $order->first_name ?><br />
	Last Name: <?php echo $order->last_name ?><br />
	Email: <?php echo $order->email ?><br />
<h5>Product Information</h5>
	Product Name: <?php echo $product->post_title ?><br/>
	Product URL: <a href="<?php echo get_permalink($product->ID) ?>" target="_blank"><?php echo get_permalink($product->ID) ?></a><br />
	Product Price: $<?php echo $order->amount ?><br />
	Buy Date: <?php echo date('H:i m-d-Y', $order->date_time) ?><br />
<h5>Status</h5>
	<?php echo $status ?>
<?php
				product_coupon::set_active( $coupon->ID );
			else :
?>
<h4>Error</h4>
You are trying to use an invalid coupon.
<?php
			endif;
		else :
?>
	<form method="get" action="<?php $_SERVER['PHP_SELF'] ?>">
		<label for="code">Coupon Code: </label>
		<input type="text" name="code" required />
		<input type="hidden" name="action" value="use_coupon" />
		<input type="submit" value="Use Coupon" />
	</form>
<?php
		endif;
	}
}
add_shortcode( 'coupons', array('product_coupon', 'shortcode_coupons') );

class product_admin {
	function page_main(){
?>
	<h3>Page Main</h3>
<?php
	}

	function orders(){
		$order = product_functions::get_order( $_GET['view'] );
		if( $order ) :
?>
<div class="wrap">
	<h2>View Order <?php echo $order->ID ?></h2>
	<h3>Client Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">First Name:</th>
			<td><input type="text" disabled value="<?php echo $order->first_name ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Last Name:</th>
			<td><input type="text" disabled value="<?php echo $order->last_name ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Email:</th>
			<td><input type="text" disabled value="<?php echo $order->email ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Status:</th>
			<td>
<?php
	$status = product_functions::get_order_status( $order->ID );
	if( $status )
		echo '<input type="text" disabled value="Paid" />';
	else
		echo '<input type="text" disabled value="Unpaid" />';
?>
			</td>
		</tr>
	</tbody>
	</table>

	<h3>Product Information</h3>
<?php
	$product = get_post( $order->post_id );
?>	
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Product ID:</th>
			<td><input type="text" disabled value="<?php echo $product->ID ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Name:</th>
			<td><input type="text" disabled value="<?php echo $product->post_title ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">URL:</th>
			<td><a href="<?php echo get_permalink($product->ID) ?>" target="_blank"><?php echo get_permalink($product->ID) ?></a></td>
		</tr>
		<tr valign="top">
			<th scope="row">Price:</th>
			<td><input type="text" disabled value="<?php echo $order->amount ?>" /></td>
		</tr>
	</tbody>
	</table>
<?php
	if( $status ) :
		$transaction = product_functions::get_transaction_by_order( $order->ID );
?>
	<h3>Transaction Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Transaction ID:</th>
			<td><input type="text" disabled value="<?php echo $transaction->ID ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">PayPal Txn ID:</th>
			<td><input type="text" disabled value="<?php echo $transaction->txn_id ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Date:</th>
			<td><input type="text" disabled value="<?php echo date('H:i m/d/Y', $order->date_time) ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Amount:</th>
			<td><input type="text" disabled value="<?php echo $transaction->amount ?>" /></td>
		</tr>
	</tbody>
	</table>

	<h3>Coupon Information</h3>
<?php
	$coupon = product_functions::get_coupon_by_order( $order->ID );
?>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Coupon ID:</th>
			<td><input type="text" disabled value="<?php echo $coupon->ID ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Coupon Code:</th>
			<td><input type="text" disabled value="<?php echo $coupon->code ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Status:</th>
<?php
	if( $coupon->status == 1 ) :
?>
			<td><input type="text" disabled value="Already used." /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Usage Date:</th>
			<td><input type="text" disabled value="<?php echo date( 'H:i m/d/Y', $coupon->usage_date ) ?>" /></td>
<?php
	else :
?>
			<td><input type="text" disabled value="Already used." /></td>
<?php
	endif;
?>
		</tr>
	</tbody>
	</table>
<?php
	endif;
?>
</div>
<?php
		else:
?>
<?php
		endif;
	}

}
?>
