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
		if( wp_verify_nonce($_POST['deals'],'deals') ) :
			$code   = $_GET['code'];
			$coupon = get_coupon_by_code($code);
			// Verify if coupon exists
			if( is_object($coupon) ):
				$order = get_order($coupon->order_id);
				$product  = get_post( $order->post_id );
				switch( $coupon->status ){
					case 0:
						$status = 'Valid Coupon';
						break;
					case 1:
						$status = 'Already used coupon';
						break;
					default:
						$status = 'Error, please contact support@mydealisideal.com';
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
	<?php 
		echo $status;
		Coupon::set_active( $coupon->ID );
			else :
	?>
	<h4>Error</h4>
	You are trying to use an invalid coupon.
	<?php
			endif;
		else :
?>
	<form method="get" action="<?php $_SERVER['PHP_SELF'] ?>">
		<?php wp_nonce_field('deals', 'coupon') ?>
		<label for="code">Coupon Code: </label>
		<input type="text" name="code" required />
		<input type="submit" value="Use Coupon" />
	</form>
<?php
		endif;
	}
}
?>