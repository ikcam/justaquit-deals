<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class shortcode_details {

	public function __construct(){
		add_action('wp_enqueue_scripts', array($this, 'scripts'));
		add_shortcode('details', array($this, 'shortcode'));
	}

	public function shortcode(){
		// Get post information
		$ID = get_the_ID();
		$views = get_post_meta( $ID, '_product_views', TRUE );
		views_count();

		// If post is active execute
		$output  = "\n".'<form class="product_details" method="post" action="'.get_bloginfo('url').'/store/checkout" >';
		$output .= get_inputs();
	  $output .= "\n\t".'<input type="hidden" name="price" id="price" value="'.get_price().'" />';
	  $output .= "\n\t".'<input type="hidden" name="the_id" id="the_id" value="'.get_the_ID().'" />';
	  $output .= "\n\t".'<input type="hidden" name="url" id="url" value="'.get_permalink().'" />';
	  $output .= "\n\t".'<div class="top">';
	  $output .= "\n\t\t".'<div class="product_time">';
	  $output .= "\n\t\t\t".'<span id="time">'.get_time().'</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="discount">';
	  $output .= "\n\t\t\t".'<span id="discount">'.get_discount().'</span>% savings';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="views">';
	  $output .= "\n\t\t\t".'<span id="views">'.$views.' Views</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="price">';
	  $output .= "\n\t\t\t".'<span id="price">$'.get_price().'</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<input type="submit" value="Buy Now!" />';
	  $output .= "\n\t".'</div>';
	  $output .= "\n\t".'<div class="bottom">';
	  $output .= "\n\t\t".'<div class="gift">';
	  if(function_exists('the_ratings')){$output .= the_ratings('div',$ID ,false);}
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="share">';
	  $output .= "\n\t\t\t".'<div class="fb-like" data-send="false" data-layout="button_count" data-width="120" data-show-faces="false" data-action="like" data-font="arial"></div>';
	  $output .= "\n\t\t\t".'<fb:share-button type="button_count"></fb:share-button>';
		$output .= "\n".'<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-hashtags="mydealisideal">Tweet</a>';
		$output .= "\n".'<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		$output .= "\n".'<script src="//platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-counter="right"></script>';
		$output .= "\n".'<div class="google-plus><div class="g-plus" data-action="share" data-annotation="bubble"></div>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t".'</div>';
	  $output .= "\n".'</form>';
	  $output .= "\n".'<div class="clearfix"></div>';

		// Result
		return $output;
	}

	public function scripts(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-timer', plugin_dir_url(__FILE__).'../../javascript/timer.jquery.js');
		wp_enqueue_script( 'jquery-details', plugin_dir_url(__FILE__).'../../javascript/details.jquery.js');
	}
}
$init = new shortcode_details();
?>