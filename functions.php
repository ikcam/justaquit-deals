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
	
	$time_expire  = get_post_meta($ID, '_product_expire', TRUE);
	$time_current = strtotime(current_time('mysql'));

	if($time_expire > $time_current)
		return TRUE;
	else
		return FALSE;
}

function get_price($ID=NULL){
	if($ID==NULL)
		$ID = get_the_ID();

	$post = get_post($ID);

	if(is_active($post->ID)):
		// Variables
		$price_max      = get_post_meta( $ID, '_product_price_max', TRUE );
		$price_min      = get_post_meta( $ID, '_product_price_min', TRUE );
		$time_expire    = get_post_meta( $ID, '_product_expire', TRUE );
		$time_published = strtotime( $post->post_date );
		$time_current   = strtotime( current_time('mysql') );
		// Calculation
		$time_onair     = $time_current - $time_published;
		$time_total     = $time_expire - $time_published;
		$price_diff     = $price_max - $price_min;
		$price          = $price_min + ( $time_onair * $price_diff ) / $time_total;
		$price          = floor( $price * 100 ) / 100;
	else:
		$price          = get_post_meta( $ID, '_product_price_max', TRUE );
	endif;

	return $price;
}

function get_discount($ID=NULL){
	if($ID==NULL)
		$ID = get_the_ID();

	// Variables
	$price_real    = get_post_meta($ID, '_product_price_real', TRUE);
	$price_current = get_price($ID);
	
	// Calculation
	$discount      = 100 - ( ( 100 * $price_current ) / $price_real );
	$discount      = floor( $discount * 100 ) / 100;
			
	// Result
	return $discount;
}

function get_price_by_time( $ID=NULL, $time ){
	if($ID==NULL)
		$ID = get_the_ID();

	// Post information
	$post = get_post($ID);

	// If product is active do calculation
	if( is_active( $ID ) ):
		// Variables
		$price_max      = get_post_meta($ID, '_product_price_max', TRUE);
		$price_min      = get_post_meta($ID, '_product_price_min', TRUE);
		$time_expire    = get_post_meta($ID, '_product_expire', TRUE);
		$time_published = strtotime($post->post_date);
		$time_current   = floor($time);
		
		// Calculation
		$time_onair     = $time_current - $time_published;
		$time_total     = $time_expire - $time_published;
		$price_diff     = $price_max - $price_min;
		$price          = $price_min + ($time_onair * $price_diff) / $time_total;
		$price          = floor( $price * 100 ) / 100;
	else :
		$price          = get_post_meta($ID, '_product_price_max', TRUE);
	endif;

	// Result
	return $price;
}

function get_time($ID=NULL){
	if($ID==NULL)
		$ID = get_the_ID();

	$post = get_post($ID);

	if(is_active($ID)):
		// Variables
		$time_expire  = get_post_meta($ID, '_product_expire', TRUE);
		$time_current = strtotime(current_time('mysql'));
		
		// Calculation
		$time         = $time_expire - $time_current;
		$time_hours   = $time / 3600;
		$time_hours   = floor($time_hours);
		$time_mins    = ($time % 3600) / 60;
		$time_mins    = floor($time_mins);
		$time_secs    = $time - (($time_hours*3600)+($time_mins * 60));
		$output       = $time_hours.':'.$time_mins.':'.$time_secs;
	else :
		$output       = 'Active';
	endif;

	// Result
	return $output;
}

function get_inputs($ID=NULL){
	if($ID==NULL)
		$ID = get_the_ID();

	// Post Information
	$post = get_post($ID);
	
	// Variables
	$price_real     = get_post_meta($ID, '_product_price_real', TRUE);
	$price_max      = get_post_meta($ID, '_product_price_max', TRUE);
	$price_min      = get_post_meta($ID, '_product_price_min', TRUE);
	$time_expire    = get_post_meta($ID, '_product_expire', TRUE);
	$time_published = strtotime($post->post_date);
	$time_current   = strtotime(current_time('mysql'));

	// Output
	$output         = "\n".'<input type="hidden" name="price_real" id="price_real" value="'.$price_real.'" />';
	$output        .= "\n".'<input type="hidden" name="price_max" id="price_max" value="'.$price_max.'" />';
	$output        .= "\n".'<input type="hidden" name="price_min" id="price_min" value="'.$price_min.'" />';
	$output        .= "\n".'<input type="hidden" name="time_published" id="time_published" value="'.$time_published.'" />';
	$output        .= "\n".'<input type="hidden" name="time_expire" id="time_expire" value="'.$time_expire.'" />';
	$output        .= "\n".'<input type="hidden" name="time_current" id="time_current" value="'.$time_current.'" />';

	// Result
	return $output;
}

function get_transaction_status($ID){
	$transaction = get_transaction($ID);

	switch($transaction->status){
		case 1:
			$output = 'Pending';
			break;
		case 2:
			$output = 'Incomplete';
			break;
		case 3:
			$output = 'Complete';
			break;
	}

	return $output;
}

function get_order_status($ID){
	$transaction = get_transaction($ID);

	switch($transaction->status){
		case 1:
			$output = 'Pending';
			break;
		case 2:
			$output = 'Incomplete';
			break;
		case 3:
			$output = 'Complete';
			break;
	}

	return $output;
}

function get_coupon_status($ID){
	$coupon = get_coupon($ID);

	switch($coupon->status){
		case 0:
			$output = 'Not used yet.';
			break;
		case 1:
			$output = 'Used.';
			break;
		default:
			$output = 'Error.';
	}

	return $output;
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