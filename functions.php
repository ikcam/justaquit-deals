<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

function get_order($ID){
	global $wpdb;
	$table = $wpdb->prefix.'orders';

	$query = "SELECT * FROM $table WHERE ID = %d;";

	return $wpdb->get_row($wpdb->prepare($query, $ID));
}

function get_orders(){
	global $wpdb;
	$table = $wpdb->prefix.'orders';

	$query = "SELECT * FROM $table;";

	return $wpdb->get_results($wpdb->prepare($query));
}

function get_transaction($ID){
	global $wpdb;
	$table = $wpdb->prefix.'transactions';

	$query = "SELECT * FROM $table WHERE ID = %d;";

	return $wpdb->get_row($wpdb->prepare($query, $ID));
}

function get_transactions(){
	global $wpdb;
	$table = $wpdb->prefix.'transactions';

	$query = "SELECT * FROM $table;";

	return $wpdb->get_results($wpdb->prepare($query));
}

function get_coupon($ID){
	global $wpdb;
	$table = $wpdb->prefix.'coupons';

	$query = "SELECT * FROM $table WHERE ID = %d;";

	return $wpdb->get_row($wpdb->prepare($query, $ID));
}

function get_coupons(){
	global $wpdb;
	$table = $wpdb->prefix.'coupons';

	$query = "SELECT * FROM $table;";

	return $wpdb->get_results($wpdb->prepare($query));
}

function is_active($ID=NULL){
	if($ID==NULL)
		$ID = get_the_ID();
	
	$post = get_post($ID);

	$time_expire  = get_post_meta($post->ID, '_product_expire', TRUE);
	$time_current = strtotime(current_time('mysql'));

	if($time_expire > $time_current)
		return TRUE;
	else
		return FALSE;
}

function parse_from_format($format, $date) {
	$dMask = array(
		'H'=>'hour',
		'i'=>'minute',
		's'=>'second',
		'y'=>'year',
		'm'=>'month',
		'd'=>'day'
	);
  
  $format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY);  
  $date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY);  
  
  foreach ($date as $k => $v) {
    if ($dMask[$format[$k]]) $dt[$dMask[$format[$k]]] .= $v;
  }
  
  return $dt;
}
?>