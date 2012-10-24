<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Transactions extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

	public function page(){
		$settings = get_option('justaquit_deals');
?>
<div class="wrap">
	<h2>Transactions</h2>

	<table class="wp-list-table widefat fixed transactions" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
				<th scope="col" id="order" class="manage-column column-order"><span>Order ID</span></th>
				<th scope="col" id="amount" class="manage-column column-amount"><span>Amount</span></th>
				<th scope="col" id="txnid" class="manage-column column-txnid"><span>PayPal Txn ID</span></th>
				<th scope="col" id="date" class="manage-column column-date"><span>Date</span></th>
				<th scope="col" id="Status" class="manage-column column-status"><span>Status</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
				<th scope="col" id="order" class="manage-column column-order"><span>Order ID</span></th>
				<th scope="col" id="amount" class="manage-column column-amount"><span>Amount</span></th>
				<th scope="col" id="txnid" class="manage-column column-txnid"><span>PayPal Txn ID</span></th>
				<th scope="col" id="date" class="manage-column column-date"><span>Date</span></th>
				<th scope="col" id="Status" class="manage-column column-status"><span>Status</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	$transactions = get_transactions();
	foreach( $transactions as $transaction ):
		$order = get_order( $transaction->order_id );
?>
			<tr>
				<td class="id column-id"><?php echo $transaction->ID ?></td>
				<td class="order column-order"><?php echo $transaction->order_id ?></td>
				<td class="amount column-amount">$<?php echo $transaction->amount ?></td>
				<td class="txnid column-txnid"><?php echo $transaction->txn_id ?></td>
				<td class="date column-date"><?php echo date('H:i m/d/Y', $order->date_time) ?></td>
				<td class="status column-status"><?php echo get_transaction_status() ?></td>
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
		add_submenu_page('deals', 'Transactions', 'Transactions', 'administrator', 'deals_transactions', array($this, 'page'));
	}
}
?>