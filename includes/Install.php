<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class deals_install{
	public static function install(){
		global $wpdb;
		// Orders
		$table = $wpdb->prefix.'orders';
		$sql = "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			email varchar(100) NOT NULL,
			first_name varchar(255) NOT NULL,
			last_name varchar(255) NOT NULL,
			post_id mediumint(9) NOT NULL,
			amount decimal(8,2) NOT NULL,
			date_time bigint(20) NOT NULL, 
			UNIQUE KEY ID (ID)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		// Transactions
		$table = $wpdb->prefix.'transactions';
		$sql = "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			order_id mediumint(9) NOT NULL,
			amount decimal(8,2) NOT NULL,
			txn_id varchar(250) NOT NULL,
			status mediumint(9) NOT NULL,
			UNIQUE KEY ID (ID)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		// Providers
		$table = $wpdb->prefix.'providers';
		$sql = "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			shortname varchar(4) NOT NULL,
			password varchar(64) NOT NULL,
			email varchar(100) NOT NULL,
			address text(500) NOT NULL,
			phone varchar(20) NOT NULL,
			url_site varchar(100),
			url_fb varchar(100),
			location_lat varchar(100) NOT NULL,
			location_long varchar(100) NOT NULL,
			UNIQUE KEY ID (ID)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);		
		// Coupons
		$table = $wpdb->prefix.'coupons';
		$sql = "CREATE TABLE $table (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			order_id mediumint(9) NOT NULL,
			coupon_date bigint(20) NOT NULL,
			code varchar(250) NOT NULL,
			status mediumint(9) NOT NULL,
			usage_date bigint(20) NOT NULL,
			UNIQUE KEY ID (ID)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
?>