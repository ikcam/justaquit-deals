<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Transactions extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');
?>
<div class="wrap">
	<h2>Transactions</h2>
</div>
<?php
	}

	public function add(){
		add_submenu_page('deals', 'Transactions', 'Transactions', 'administrator', 'deals_transactions', array($this, 'page'));
	}
}
?>