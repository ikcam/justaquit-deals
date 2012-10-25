<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Orders extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');
?>
<div class="wrap">
	<h2>Orders</h2>
<table class="wp-list-table widefat fixed orders" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>Order ID</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>First Name</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>Last Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="date" class="manage-column column-date"><span>Date</span></th>
				<th scope="col" id="Status" class="manage-column column-status"><span>Status</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>Order ID</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>First Name</span></th>
				<th scope="col" id="author" class="manage-column column-author"><span>Last Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="date" class="manage-column column-date"><span>Date</span></th>
				<th scope="col" id="Status" class="manage-column column-status"><span>Status</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	$orders = get_orders();
	foreach( $orders as $order ):
?>
			<tr>
				<td class="id column-id"><?php echo $order->ID ?></td>
				<td class="author column-author"><?php echo $order->first_name ?></td>
				<td class="author column-author"><?php echo $order->last_name ?></td>
				<td class="email column-email"><?php echo $order->email ?></td>
				<td class="date column-date"><?php echo date('H:i m/d/Y', $order->date_time) ?></td>
				<td class="status column-status"><?php echo get_order_status($order->ID) ?></td>
				<td class="options column-options"><a href="?page=product_orders&amp;view=<?php echo $order->ID ?>">View Details</a></td>
			</tr>
<?php
	endforeach;
?>
		</tbody>
	</table>
</div>
<?php
	}

	public function add(){
		add_submenu_page('deals', 'Orders', 'Orders', 'administrator', 'deals_orders', array($this, 'page'));
	}
}
$init = new Orders();
?>