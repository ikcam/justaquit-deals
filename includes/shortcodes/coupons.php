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
			if( $coupon != NULL ):
				// Pass: Verify is coupon is valid.
				if( $coupon->status == 0 ):
					// Pass: Valid coupon
					// Show coupon information
					
					// Set coupon as used.
					Coupon::set_active($coupon->ID);
				else:
					// Error: Coupon already used.
					// Show coupon information

				endif;
			else:
				// Error: Coupon code not valid.
				echo 'Error: Invalid coupon code.';
			endif;
		elseif( isset($_GET['coupon']) ):
			// Pass: Form Page
			$coupon = get_coupon_by_code($_GET['coupon']);

			if( $coupon != NULL ):
				// Pass: Valid coupon
				$provider = get_provider_by_coupon($coupon->code);
				$product = get_product_by_coupon($coupon->code);
?>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
		<table class="form-table">
		<tbody>
			<tr>
				<td><strong>Provider:</strong></td>
				<td><span><?php echo $provider->name ?></span></td>
			</tr>
			<tr>
				<td><strong>Product:</strong></td>
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
		</tbody>
		</table>
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