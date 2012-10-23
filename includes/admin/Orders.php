<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Orders extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');
?>
<div class="wrap">
	<h2>Orders</h2>
</div>
<?php
	}

	public function add(){
		add_submenu_page('deals', 'Orders', 'Orders', 'administrator', 'deals_orders', array($this, 'page'));
	}
}
?>