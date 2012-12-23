<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Provider{
	private $ID;
	private $name;
	private $shortname;
	private $password;
	private $email;
	private $address;
	private $phone;
	private $url_site;
	private $url_fb;
	private $location_lat;
	private $location_long;

	public function __construct($name, $shortname, $password, $email, $address, $phone, $url_site=null, $url_fb=null, $lat, $long){
		$this->name          = $name;
		$this->shortname     = $shortname;
		$this->password      = $password;
		$this->email         = $email;
		$this->address       = $address;
		$this->phone         = $phone;
		$this->url_site      = $url_site;
		$this->url_fb        = $url_fb;
		$this->location_lat  = $lat;
		$this->location_long = $long;
	}

	public function add_provider(){
		global $wpdb;
		$table = $wpdb->prefix.'providers';

		$data = array(
			'name'          => $this->name,
			'shortname'     => $this->shortname,
			'password'      => $this->password,
			'email'         => $this->email,
			'address'       => $this->address,
			'phone'         => $this->phone,
			'url_site'      => $this->url_site,
			'url_fb'        => $this->url_fb,
			'location_lat'  => $this->location_lat,
			'location_long' => $this->location_long 
		);
		$format = array( '%s', '%s', '$s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
		$wpdb->insert($table, $data, $format);

		$this->ID = $wpdb->insert_id;

		return $this->ID;
	}

	public function delete_provider($ID){
		global $wpdb;
		$table = $wpdb->prefix.'providers';

		if( $this->exists($ID) ){
			$query = "DELETE FROM $table WHERE ID = %s";
			$wpdb->query( $wpdb->prepare($query, $ID) );
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function update_provider($ID){
		global $wpdb;
		$table = $wpdb->prefix.'providers';

		if( $this->exists($ID) ){
			$data = array(
				'name'          => $this->name,
				'shortname'     => $this->shortname,
				'password'      => $this->password,
				'email'         => $this->email,
				'address'       => $this->address,
				'phone'         => $this->phone,
				'url_site'      => $this->url_site,
				'url_fb'        => $this->url_fb,
				'location_lat'  => $this->location_lat,
				'location_long' => $this->location_long 
			);
			$where = array(
				'ID' => $ID
			);
			$format = array( '%s', '%s', '$s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
			$wpdb->update($data, $table, $where, $format);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function exists($ID){
		global $wpdb;
		$table = $wpdb->prefix.'providers';

		$query = "SELECT COUNT(*) FROM $table WHERE ID = %d";
		$count = $wpdb->get_var($wpdb->prepare($query, $ID));

		if( $count > 0 )
			return TRUE;
		else
			return FALSE;
	}
}
?>