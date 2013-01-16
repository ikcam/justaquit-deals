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

		elseif( isset($_GET['coupon']) ):
			// Pass: Form Page
			$coupon = get_coupon_by_code($_GET['coupon']);

			if( $coupon != NULL ):
				// Pass: Valid coupon
				$provider = get_provider_by_coupon($coupon->code);
				$product = get_product_by_coupon($coupon->code);
?>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
		<input type="hidden" name="provider" id="provider" value="<?php echo $provider->ID ?>">
		<p>
			<strong>Provider:</strong><span><?php echo $provider->name ?></span><br />
			<strong>Product:</strong><span><?php echo $product->post_title ?></span><br />
		</p>
		<p>
			<label for="code">Code:</label>
			<input type="text" name="code" id="code" readonly="readonly" value="<?php $coupon->code ?>" />
		</p>
		<p>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" required />
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Use Coupon" />
		</p>
	</form>
<?php
			else:
				// Error: Coupon doesn't exists or is invalid
				echo 'Error: Coupon doesn\'t exists or is invalid';
			endif;
		else:
			// Error: No coupon code setup
			echo 'Error: No coupon code is setup';
		endif;
	}
}
$init = new shortcode_coupons();
?>