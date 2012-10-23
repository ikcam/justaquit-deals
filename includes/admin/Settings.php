<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Settings extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');
?>
<div class="wrap">
	<h2>Settings</h2>
</div>
<?php
	}

	public function add(){
		add_submenu_page('deals', 'Settings', 'Settings', 'administrator', 'deals_settings', array($this, 'page'));
	}
}
?>