<?php
/*
Plugin Name: JustAquit Deals
Plugin URI: http://justaquit.com
Description: This plugins allows you to use your WordPress installation as a deals system.
Version: 2.0
Author: Irving Kcam
Author URI: http://ikcam.com
License: GPL2
*/
?>
<?php
define('JUSTAQUIT_DEALS', TRUE);
include('main.php');
register_activation_hook( __FILE__, array( 'deals_install', 'install' ) );
?>