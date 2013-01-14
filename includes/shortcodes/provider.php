<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class shortcode_provider{
	public function __construct(){
		add_shortcode('provider', array($this, 'shortcode'));
	}

	public function shortcode(){
		$provider = get_provider( get_post_meta(get_the_ID(), '_product_provider', TRUE ) );

		$output = '<strong>'.$provider->name.'</strong><br />';
		$output .= $provider->address."<br />";
		$output .= $provider->phone."<br />";
		$output .= "<br />";
		if( $provider->url_site != null ){
			$output .= '<a href="'.$provider->url_site.'">'.$provider->url_site.'</a><br />';
		}
		if( $provider->url_fb != null ) {
			$output .= '<a href="'.$provider->url_fb.'">'.$provider->url_fb.'</a><br />';
			$output .= "<br />";
		}
		$output .= '[gmaps lat="'.$provider->location_lat.'" lng="'.$provider->location_long.'"][gmarker lat="'.$provider->location_lat.'" lng="'.$provider->location_long.'" title="'.$provider->name.'"]<strong>'.$provider->name.'</strong><br />'.$provider->address.'<br />'.$provider->phone.'[/gmarker][/gmaps]';

		return do_shortcode($output);
	}
}
$init = new shortcode_provider();
?>