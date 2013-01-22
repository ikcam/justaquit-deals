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
				// Show coupon information.
				
				// Set coupon as active.
				Coupon::set_active($coupon->ID);
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