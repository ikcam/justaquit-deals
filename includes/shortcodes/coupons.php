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
		if( isset($_POST['submit']) && isset($_GET['coupon']) ):
			

		elseif( isset($_GET['coupon']) ):
			$coupon = get_coupon($_GET['coupon']);

			if( $coupon == NULL ):
				// Error: Coupon doesn't exists
				echo 'Error: Coupon doesn\'t exists';
			else:
				// Pass: Coupon exists and it's valid
				$provider = get_provider_by_coupon($coupon->code);
?>
	<form method="post" action="?coupon=<?php $coupon->code ?>">
		<strong><?php echo $provider->name ?></strong>
		<label for="password">Password:</label> <input type="password" name="password" id="password" placeholder="Venue Password" required />
		<input type="submit" name="submit" id="submit" />
	</form>
<?php
			endif;
		else:
				// Error: Coupon information isn't set
				echo 'Error: Coupon information isn\'t set';
?>
<?php
		endif;
	}
}
$init = new shortcode_coupons();
?>