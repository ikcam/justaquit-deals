<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

define( 'JUSTAQUIT_DEALS_PATH', plugin_dir_path(__FILE__) );

// Functions
require_once('functions.php');
// Install
require_once('includes/Install.php');
// Classes
require_once('classes/Coupon.php');
require_once('classes/Order.php');
require_once('classes/Provider.php');
require_once('classes/Transaction.php');
// Views
require_once('includes/Init.php');
require_once('includes/Box.php');
// Admin Pages
require_once('includes/admin/Main.php');
require_once('includes/admin/Orders.php');
require_once('includes/admin/Coupons.php');
require_once('includes/admin/Providers.php');
require_once('includes/admin/Transactions.php');
require_once('includes/admin/View.php');
require_once('includes/admin/Settings.php');
// Shortcodes
require_once('includes/shortcodes/details.php');
require_once('includes/shortcodes/checkout.php');
require_once('includes/shortcodes/coupons.php');
require_once('includes/shortcodes/ipn.php');
require_once('includes/shortcodes/provider.php');
require_once('includes/shortcodes/transaction.php');
?>