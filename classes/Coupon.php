<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Coupon{
	private $order_id;
	private $coupon_date;
	private $code;
	private $status;
	private $usage_date;

	public function __construct($order_id){
		$this->order_id = $order_id;
		$this->coupon_date = strtotime(current_time('mysql'));
		$this->status = 0;
	}

	public function add_coupon(){
		global $wpdb;
		$table = $wpdb->prefix.'coupons';

		$this->set_code();

		$data = array(
			'order_id'    => $this->order_id,
			'coupon_date' => $this->coupon_date,
			'code'        => $this->code,
			'status'      => $this->status,
			'usage_date'  => 0
		);
		$format = array('%d', '%d', '%s', '%d', '%d');

		$wpdb->insert($table, $data, $format);

		return $wpdb->insert_id;
	}

	public static function delete_coupon($ID){
		global $wpdb;
		$table = $wpdb->prefix.'coupons';

		if($this->exists($ID)){
			$query = "DELETE FROM $table WHERE ID = %d;";
			$wpdb->query($wpdb->prepare($query, $ID));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function set_active($ID){
		global $wpdb;
		$table = $wpdb->prefix.'coupons';

		if(Coupon::exists($ID)){
			$data = array(
				'status'     => 1,
				'usage_date' => strtotime(current_time('mysql'))
			);
			$where = array(
				'ID' => $ID
			);
			$format = array('%d', '%d');
			$wpdb->update($table, $data, $where, $format);
			
			return TRUE;
		} else {
			return FALSE;
		}
	}	

	private function set_code($length=6){
		do {
			$key = '';

			list($usec, $sec) = explode(' ', microtime());
			mt_srand((float) $sec + ((float) $usec * 100000));
			$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

			for($i=0; $i<$length; $i++) {
				$key .= $inputs{mt_rand(0,61)};
			}

			$this->code = $key;
		} while($this->exists());
	}

	public function exists($ID=NULL){
		global $wpdb;
		$table = $wpdb->prefix.'coupons';

		if($ID==NULL){
			$query = "SELECT COUNT(*) FROM $table WHERE code = %s;";
			$count = $wpdb->get_var($wpdb->prepare($query, $this->code));
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