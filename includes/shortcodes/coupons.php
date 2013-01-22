<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class shortcode_coupons{
	public function __construct(){
		add_shortcode('coupons', array($this, 'shortcode'));
	}

	public function shortcode(){
		if( isset($_POST['submit']) ):
			// Pass: Usage coupon page
			$coupon = get_coupon_by_code($_POST['code']);
			$provider = get_provider_by_coupon($coupon->code);

			if( $provider->password == $_POST['password'] ):
				// Pass: Valid password.
				// Verify if is a valid coupon.
				if( $coupon->status == 0 )
					// Show coupon information.
?>
	<h3 id="coupon">Coupon Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Code:</th>
			<td><?php echo $coupon->code ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Status:</th>
			<td><?php echo get_coupon_status($coupon->ID) ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Usage Date:</th>
			<td>
<?php
	if( $coupon->status == 0 )
		echo 'None yet';
	else
		echo date( 'H:i m/d/y', $coupon->usage_date );
?>
			</td>
		</tr>
	</tbody>
	</table>	
<?php
					$order = get_order( $coupon->order_id );
?>
	<h3 id="client">Client Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">First Name:</th>
			<td><?php echo $order->first_name ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Last Name:</th>
			<td><?php echo $order->last_name ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Email:</th>
			<td><?php echo $order->email ?></td>
		</tr>
	</tbody>
	</table>
<?php
					$product = get_post( $order->post_id );
?>
	<h3 id="product">Product Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Product Name:</th>
			<td><?php echo $product->post_title ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Product URL:</th>
			<td><a href="<?php echo get_permalink($product->ID) ?>" target="_blank"><?php echo get_permalink($product->ID) ?></a></td>
		</tr>
		<tr valign="top">
			<th scope="row">Product Price:</th>
			<td>$<?php echo $order->amount ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Buy Date:</th>
			<td><?php echo date('H:i m-d-Y', $order->date_time) ?></td>
		</tr>
	</tbody>
	</table>
<?php
					$transaction = get_transaction_by_order($order->ID);
?>
	<h3 id="transaction">Transaction Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Amount:</th>
			<td><?php echo $transaction->amount ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Transaction ID:</th>
			<td><?php echo $transaction->txn_id ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Date/Time</th>
			<td><?php echo date('H:i m/d/Y', $order->date_time) ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Status</th>
			<td><?php echo get_transaction_status($transaction->ID) ?></td>
		</tr>
	</tbody>
	</table>
<?php
					// Set coupon as active.
					Coupon::set_active($coupon->ID);
				else:
					// Error: Invalid coupon.
					echo 'This coupon was already used.';
				endif;
			else:
				// Error: Wrong password
				echo 'Error: Wrong password';
			endif;
		elseif( isset($_GET['coupon']) ):
			// Pass: Form Page
			$coupon = get_coupon_by_code($_GET['coupon']);

			if( $coupon != NULL ):
				// Pass: Valid coupon
				$provider = get_provider_by_coupon($coupon->code);
				$product = get_product_by_coupon($coupon->code);
?>
	<form action="" method="post">
		<table class="form-table">
		<tbody>
			<tr>
				<td><strong>Provider Name:</strong></td>
				<td><span><?php echo $provider->name ?></span></td>
			</tr>
			<tr>
				<td><strong>Product Name:</strong></td>
				<td><span><?php echo $product->post_title ?></span></td>
			</tr>
			<tr>
				<td><label for="code">Code:</label></td>
				<td><input type="text" name="code" id="code" readonly="readonly" value="<?php echo $coupon->code ?>" /></td>
			</tr>
			<tr>
				<td><label for="password">Password:</label></td>
				<td><input type="password" name="password" id="password" required /></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="submit" id="submit" value="Use Coupon" />
				</td>
			</tr>
		</tbody>
		</table>
	</form>
<?php
			else:
				// Error: Coupon doesn't exists or is invalid
				echo 'Error: Coupon doesn\'t exists or is invalid';
			endif;
		else:
			// Error: No coupon code setup
?>
	<form action="" method="get">
		<table class="form-table">
		<tbody>
			<tr>
				<td><label for="code"´>Coupon Code: </label></td>
				<td><input type="text" name="coupon" id="coupon" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Go" /></td>
			</tr>
		</tbody>
		</table>
	</form>
<?php
		endif;
	}
}
$init = new shortcode_coupons();
?>