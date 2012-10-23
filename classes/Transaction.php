<?php
Class Transaction{
	global $wpdb;

	private $order_id;
	private $amount;
	private $txn_id;
	private $status;
	private static $table = $wpdb->prefix.'transactions';

	public function __construct($order_id, $amount, $xtxn_id, $status){
		$this->order_id = $order_id;
		$this->amount   = $amount;
		$this->txn_id   = $txn_id;
		$this->status   = $status;
	}

	public function add_transaction(){
		global $wpdb;

		if($this->exists()){
			$data = array(
				'order_id' => $this->order_id,
				'amount' => $this->amount,
				'txn_id' => $this->txn_id,
				'status' => $this->status
			);
			$format = array('%d', '%d', '%d', '%d');
			$wpdb->insert($this->table, $data, $format);
			return $wpdb->insert_id;
		} else {
			return FALSE;
		}
	}

	public static function delete_function($ID){
		global $wpdb;

		if($this->exists($ID)){
			$query = "DELETE FROM $this->table WHERE ID = %d;";
			$wpdb->query($wpdb->prepare($query, $ID));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function exists($ID=NULL){
		global $wpdb;

		if($ID==NULL){
			$query = "SELECT COUNT(*) FROM $this->table WHERE order_id = %d;";
			$count = $wpdb->get_var($wpdb->prepare($query, $this->order_id));
		} else {
			$query = "SELECT COUNT(*) FROM $this->table WHERE ID = %d;";
			$count = $wpdb->get_var($wpdb->prepare($query, $ID));
		}

		if($count>0)
			return TRUE;
		else
			return FALSE;
	}
}
?>