<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}
// Functions
require_once('functions.php');

// Classes
require_once('classes/Coupon.php');
require_once('classes/Order.php');
require_once('classes/Transaction.php');

// Views
require_once('includes/Box.php');
require_once('includes/admin/Main.php');
require_once('includes/admin/Orders.php');
require_once('includes/admin/Coupons.php');
require_once('includes/admin/Transactions.php');
require_once('includes/admin/Settings.php');

$deals_box = new Box();
$page = new Deals();
$page = new Orders();
$page = new Transactions();
$page = new Coupons();
$page = new Settings();
?>