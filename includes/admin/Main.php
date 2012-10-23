<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
?>
<div class="wrap">
	<h2>Deals</h2>
</div>
<?php
	}

	public function add(){
		add_menu_page( 'Deals', 'Deals', 'administrator', 'deals', array($this, 'page'), '' );
	}
}
?>