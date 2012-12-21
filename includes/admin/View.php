<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class View extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');

		if( isset( $_GET['view'] ) ):
			$order = get_order($_GET['view']);
			if( is_object($order) ):
?>
<div class="wrap">
	<h2>View Order ID:<?php echo $order->ID ?></h2>

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
	$product = get_post($order->post_id);
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
	if( is_object($transaction) ):
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
		$coupon = get_coupon_by_order($order->ID);
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
	else:
?>
	<h3 id="transaction">No Transaction Information</h3>
	<h3 id="coupon">No Coupon Information</h3>
<?php
	endif;
?>
</div>
<?php
			else:
?>
Error 1
<?php
			endif;
		else:
?>
Error 2
<?php
		endif;
	}

	public function add(){
		add_submenu_page('deals', 'View Order', 'View Order', 'administrator', 'deals_view', array($this, 'page'));
	}
}
$init = new View();
?>