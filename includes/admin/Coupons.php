<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Coupons extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');
?>
<div class="wrap">
	<h2>Coupons</h2>

<table class="wp-list-table widefat fixed coupons" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
				<th scope="col" id="order" class="manage-column column-order"><span>Order ID</span></th>
				<th scope="col" id="code" class="manage-column column-code"><span>Code</span></th>
				<th scope="col" id="date" class="manage-column column-date"><span>Date</span></th>
				<th scope="col" id="status" class="manage-column column-status"><span>Status</span></th>
				<th scope="col" id="usage" class="manage-column column-usage"><span>Usage Date</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
				<th scope="col" id="order" class="manage-column column-order"><span>Order ID</span></th>
				<th scope="col" id="code" class="manage-column column-code"><span>Code</span></th>
				<th scope="col" id="date" class="manage-column column-date"><span>Date</span></th>
				<th scope="col" id="status" class="manage-column column-status"><span>Status</span></th>
				<th scope="col" id="usage" class="manage-column column-usage"><span>Usage Date</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	$coupons = get_coupons();
	foreach( $coupons as $coupon ):
		$order = get_order($coupon->order_id);
?>
			<tr>
				<td class="id column-id"><?php echo $coupon->ID ?></td>
				<td class="order column-order"><?php echo $coupon->order_id ?></td>
				<td class="code column-code"><?php echo $coupon->code ?></td>
				<td class="date column-date"><?php echo date('H:i m/d/Y', $coupon->coupon_date) ?></td>
				<td class="status column-status"><?php echo get_coupon_status() ?></td>
				<td class="usage column-usage">
<?php
if( $coupon->status == 0 )
	echo 'None yet';
else
	echo date( 'H:i m/d/y', $coupon->usage_date );
?>
				</td>
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
		add_submenu_page('deals', 'Coupons', 'Coupons', 'administrator', 'deals_coupons', array($this, 'page'));
	}
}
?>