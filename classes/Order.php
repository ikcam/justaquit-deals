<?php
Class Order{
	global $wpdb;

	private $email;
	private $first_name;
	private $last_name;
	private $post_id;
	private $amount;
	private $date_time;
	private static $table = $wpdb->prefix.'orders';

	public function __construct($email, $first_name, $last_name, $post_id, $amount){
		$this->email      = $email;
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
		$this->post_id    = $post_id;
		$this->amount     = $amount;
		$this->datetime   = strtotime(current_time('mysql'));
	}

	public function add_order(){
		global $wpdb;

		$data = array(
			'email'      => $this->email,
			'first_name' => $this->first_name,
			'last_name'  => $this->last_name,
			'post_id'    => $this->post_id,
			'amount'     => $this->amount,
			'datetime'   => $this->datetime
		);
		$format = array('%s', '%s', '%s', '%d', '%d', '%d');
		$wpdb->insert($this->table, $data, $format);

		return $wpdb->insert_id;
	}

	public static function delete_order($ID){
		global $wpdb;

		if( $this->exists($ID) ){
			$query = "DELETE FROM $this->table WHERE ID = %d";
			$wpdb->query($wpdb->prepare($query, $ID));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function update_order($ID){
		global $wpdb;

		if($this->exists($ID)){
			$data = array(
				'email'      => $this->email,
				'first_name' => $this->first_name,
				'last_name'  => $this->last_name,
			);
			$where = array(
				'ID' = $ID	
			);
			$format = array('%s', '%s');
			$wpdb->update($data, $this->table, $where, $format);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function exists($ID){
		global $wpdb;

		$query = "SELECT COUNT(*) FROM $this->table WHERE ID = %d;";
		$count = $wpdb->get_var($wpdb->prepare($query, $ID));

		if($count>0)
			return TRUE;
		else
			return FALSE;
	}
}