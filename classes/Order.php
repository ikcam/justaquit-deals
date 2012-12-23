<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Order{
	private $email;
	private $first_name;
	private $last_name;
	private $post_id;
	private $amount;
	private $date_time;

	public function __construct($email, $first_name, $last_name, $post_id, $amount, $datetime){
		$this->email      = $email;
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
		$this->post_id    = $post_id;
		$this->amount     = $amount;
		$this->datetime   = $datetime;
	}

	public function add_order(){
		global $wpdb;
		$table = $wpdb->prefix.'orders';

		$data = array(
			'email'      => $this->email,
			'first_name' => $this->first_name,
			'last_name'  => $this->last_name,
			'post_id'    => $this->post_id,
			'amount'     => $this->amount,
			'date_time'  => $this->datetime
		);
		$format = array('%s', '%s', '%s', '%d', '%d', '%d');
		$wpdb->insert($table, $data, $format);

		return $wpdb->insert_id;
	}

	public static function delete_order($ID){
		global $wpdb;

		if( $this->exists($ID) ){
			$query = "DELETE FROM $table WHERE ID = %d";
			$wpdb->query($wpdb->prepare($query, $ID));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function update_order($ID){
		global $wpdb;
		$table = $wpdb->prefix.'orders';

		if($this->exists($ID)){
			$data = array(
				'email'      => $this->email,
				'first_name' => $this->first_name,
				'last_name'  => $this->last_name
			);
			$where = array(
				'ID' => $ID	
			);
			$format = array('%s', '%s');
			$wpdb->update($data, $table, $where, $format);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function exists($ID){
		global $wpdb;
		$table = $wpdb->prefix.'orders';

		$query = "SELECT COUNT(*) FROM $table WHERE ID = %d;";
		$count = $wpdb->get_var($wpdb->prepare($query, $ID));

		if($count>0)
			return TRUE;
		else
			return FALSE;
	}
}