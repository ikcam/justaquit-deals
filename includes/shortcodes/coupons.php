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
		if( isset($_POST['submit']) || isset( $_POST['redem'] ) ):
			$coupon = get_coupon_by_code( $_POST['code'] );

			if( $coupon == NULL ):
				echo 'Invalid coupon code.';
?>
	<form action="" method="post">
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="code">Coupon Code:</label></th>
				<td><input type="text" name="code" id="code" required /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="submit" id="submit" value="View coupon" />
			</tr>
		</tbody>
		</table>
	</form>
<?php
			else:
				if( isset( $_POST['redem'] ) ):
					if( $coupon->status == 0 ):
						Coupon::set_active($coupon->ID);
						echo '<h5>Congratulations. You activated this coupon succesfully.</h5>';
						$coupon = get_coupon($coupon->ID);
					else:
						echo '<h5>Error: This coupon was use already.</h5>';
					endif;
				endif;
?>
	<h3>Status: <?php if( $coupon->status == 0 ){ echo 'Not used.'; } else { echo 'Used.'; } ?></h3>
<?php
	if( $coupon->status == 0 ):
?>
	<p class="form-submit">
		<form action="" method="post">
			<input type="hidden" name="code" value="<?php echo $coupon->code ?>" />
			<input type="submit" name="redem" id="redem" value="Redem this coupon" />
		</form>
	</p>
<?php
	endif;
?>
	<h3 id="coupon">Coupon Information</h3>
	<p>
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
	if( $coupon->status == 0 ):
		echo 'None yet';
	else:
		echo date( 'H:i m/d/y', $coupon->usage_date );
	endif;
?>
			</td>
		</tr>
	</tbody>
	</table>	
	</p>
<?php 
	$order = get_order( $coupon->order_id );
?>
	<h3 id="client">Client Information</h3>
	<p>
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
	</p>
<?php
	$product = get_post($order->post_id);
?>
	<h3 id="product">Product Information</h3>
	<p>
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
	</p>
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
<?
				if( $coupon->status == 0 ):
?>
	<p class="form-submit">
		<form action="" method="post">
			<input type="hidden" name="code" value="<?php echo $coupon->code ?>" />
			<input type="submit" name="redem" id="redem" value="Redem this coupon" />
		</form>
	</p>
<?php
				else:
?>
	<p class="form-submit">Used coupon.</p>
<?php
				endif;
			endif;
		else:
?>
	<form action="" method="post">
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="code">Coupon Code:</label></th>
				<td><input type="text" name="code" id="code" required <?php if( isset( $_GET['coupon'] ) ) { echo 'value="'.$_GET['coupon'].'"'; } ?> /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="submit" id="submit" value="View coupon" />
			</tr>
		</tbody>
		</table>
	</form>
<?php
		endif;
?>
<?php
	}
}
$init = new shortcode_coupons();
?>