<?php
function ja_deal_active($ID=NULL){
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
?>