<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Settings extends Deals{
	public function __construct(){
		add_action('admin_init', array($this, 'register'));
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');
?>
<div class="wrap">
	<h2>Settings</h2>
	<form method="post" action="options.php">
	<?php settings_fields('deals'); ?>
	<h3>Main Settings</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="price_real">Default Real Price</label></th>
			<td><input type="text" id="price_real" name="deals[price_real]" value="<?php echo $settings['price_real'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="price_max">Default Max Price</label></th>
			<td><input type="text" id="price_max" name="deals[price_max]" value="<?php echo $settings['price_max'] ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="price_min">Default Min Price</label></th>
			<td><input type="text" id="price_min" name="deals[price_min]" value="<?php echo $settings['price_min'] ?>" /></td>
		</tr>
	</tbody>
	</table>
	<h3>PayPal Settings</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="paypal_account">PayPal Email</label></th>
			<td><input type="text" id="paypal_account" name="deals[paypal_account]" value="<?php echo $settings['paypal_account'] ?>" required /></td>
		</tr>
	</tbody>
	</table>
	</form>
</div>
<?php
	}

	public function save($input){
		if( empty($input['price_real']) )
			$input['price_real'] = 0;
		if( empty($input['price_max']) )
			$input['price_max'] = 0;
		if( empty($input['price_min']) )
			$input['price_min'] = 0;
		if( empty($input['paypal_account']) )
			$input['paypal_account'] = 'me@ikcam.com';

		if( $input['price_min'] > $input['price_max'] ):
			if( $input['price_min'] > $input['price_real'] ):
				// Fix Price Real
				$aux = $input['price_real'];
				$input['price_real'] = $input['price_min'];
				$input['price_min'] = $aux;
			endif;

			// Fix Price Max
			$aux = $input['price_max'];
			$input['price_max'] = $input['price_min'];
			$input['price_min'] = $aux;
		endif;

		return $input;
	}

	public function register(){
		register_setting('deals', 'justaquit_deals', array($this, 'save'));
	}

	public function add(){
		add_submenu_page('deals', 'Settings', 'Settings', 'administrator', 'deals_settings', array($this, 'page'));
	}

}
?>