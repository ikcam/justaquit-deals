<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}
// Functions
require_once('functions.php');

// Install
require_once('includes/Install.php');

// Classes
require_once('classes/Coupon.php');
require_once('classes/Order.php');
require_once('classes/Transaction.php');

// Views
require_once('includes/Box.php');
$deals_box  = new Box();

// Admin Pages
require_once('includes/admin/Main.php');
require_once('includes/admin/Orders.php');
require_once('includes/admin/Coupons.php');
require_once('includes/admin/Transactions.php');
require_once('includes/admin/Settings.php');
$deals_page = new Deals();
$deals_page = new Orders();
$deals_page = new Transactions();
$deals_page = new Coupons();
$deals_page = new Settings();

// Shortcodes
require_once('includes/shortcodes/transaction.php');
require_once('includes/shortcodes/checkout.php');
require_once('includes/shortcodes/ipn.php');
require_once('includes/shortcodes/details.php');
require_once('includes/shortcodes/coupons.php');
$deals_shortcode = new shortcode_details();
$deals_shortcode = new shortcode_checkout();
$deals_shortcode = new shortcode_transaction();
$deals_shortcode = new shortcode_ipn();
$deals_shortcode = new shortcode_coupons();
// Load
// 
?>

