<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Transaction{
	private $order_id;
	private $amount;
	private $txn_id;
	private $status;

	public function __construct($order_id, $amount, $xtxn_id, $status){
		$this->order_id = $order_id;
		$this->amount   = $amount;
		$this->txn_id   = $txn_id;
		$this->status   = $status;
	}

	public function add_transaction(){
		global $wpdb;
		$table = $wpdb->prefix.'transactions';

		if($this->exists()){
			$data = array(
				'order_id' => $this->order_id,
				'amount' => $this->amount,
				'txn_id' => $this->txn_id,
				'status' => $this->status
			);
			$format = array('%d', '%d', '%d', '%d');
			$wpdb->insert($table, $data, $format);
			return $wpdb->insert_id;
		} else {
			return FALSE;
		}
	}

	public static function delete_function($ID){
		global $wpdb;
		$table = $wpdb->prefix.'transactions';

		if($this->exists($ID)){
			$query = "DELETE FROM $table WHERE ID = %d;";
			$wpdb->query($wpdb->prepare($query, $ID));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function verify_txnid($txnid){
		global $wpdb;
		$table = $wpdb->prefix.'transactions';

		$query = "SELECT COUNT(*) FROM $table WHERE txn_id = %s;";
		$count = $wpdb->get_var($wpdb->prepare($query, $txnid));

		if($count>0)
			return TRUE;
		else
			return FALSE;
	}

	private function exists($ID=NULL){
		global $wpdb;
		$table = $wpdb->prefix.'transactions';

		if($ID==NULL){
			$query = "SELECT COUNT(*) FROM $table WHERE order_id = %d;";
			$count = $wpdb->get_var($wpdb->prepare($query, $this->order_id));
		} else {
			$query = "SELECT COUNT(*) FROM $table WHERE ID = %d;";
			$count = $wpdb->get_var($wpdb->prepare($query, $ID));
		}

		if($count>0)
			return TRUE;
		else
			return FALSE;
	}
}
?>