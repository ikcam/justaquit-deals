<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class deals_init(){
	public function __construct(){
		add_action('wp_enqueue_scripts', array($this, 'scripts'));
	}

	public function scripts(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-timer', plugin_dir_url(__FILE__).'../javascript/timer.jquery.js');
		wp_enqueue_script('jquery-slider', plugin_dir_url(__FILE__).'../javascript/slider.jquery.js');
	}
}
$init = new deals_init();
?>