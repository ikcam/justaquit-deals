<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Box{
	public function __construct(){
		add_action('add_meta_boxes', array( $this, 'add'));
		add_action('save_post', array($this, 'save'));
		add_action('admin_enqueue_scripts', array( $this, 'scripts'));
		add_action('admin_enqueue_scripts', array( $this, 'stylesheets'));
	}

	public function box( $post ){
		wp_nonce_field(plugin_basename(__FILE__), 'justaquit_deals');
?>
<?php
	if(!get_post_meta($post->ID, '_product_quantity', TRUE))
		$value = 0;
	else
		$value = get_post_meta($post->ID, '_product_quantity', TRUE);
?>
	<p>
		<label for="quantity">Quantity:</label>
		<input type="text" name="quantity" id="quantity" value="<?php echo $value ?>" />
		<br /><span class="description">0 for unlimited</span>
	</p>
<?php
	if(!get_post_meta($post->ID, '_product_price_real', TRUE))
		$value = $settings['price_real'];
	else
		$value = get_post_meta($post->ID, '_product_price_real', TRUE);
?>
	<p>
		<label for="price_real">Real Price:</label>
		<input type="text" name="price_real" id="price_real" value="<?php echo $value ?>" />
	</p>
<?php 
	if (!get_post_meta($post->ID, '_product_price_max', TRUE))
		$value = $settings['price_max'];
	else
		$value = get_post_meta($post->ID, '_product_price_max', TRUE);
?>
	<p>
		<label for="price_max">Max Price:</label>
		<input type="text" name="price_max" id="price_max" value="<?php echo $value ?>" />
	</p>
<?php
	if(!get_post_meta($post->ID, '_product_price_min', TRUE))
		$value = $settings['price_min'];
	else
		$value = get_post_meta($post->ID, '_product_price_min', TRUE);
?>
	<p>
		<label for="price_min">Min Price:</label>
		<input type="text" name="price_min" id="price_min" value="<?php echo $value ?>" />
	</p>
<?php
	if(!get_post_meta($post->ID, '_product_expire', TRUE))
		$value = date( 'm/d/Y', time() );
	else
		$value = date( 'm/d/Y', get_post_meta($post->ID, '_product_expire', TRUE) );
?>
	<p>
		<label for="expire_date">Expire Date:</label>
		<input type="text" name="expire_date" id="expire_date" value="<?php echo $value ?>" />
	</p>
<?php
	if(!get_post_meta($post->ID, '_product_expire', TRUE))
		$value = date('H:i', time());
	else
		$value = date('H:i', get_post_meta($post->ID, '_product_expire', TRUE));
?>
	<p>
		<label for="expire_time">Expire Time:</label>
		<input type="text" name="expire_time" id="expire_time" value="<?php echo $value ?>" />
	</p>
	<hr />
	<h4>Saved Information:</h4>
<?php 
	$value = strtotime(current_time('mysql'));
	$value = date('H:i m/d/Y', $value);
?>
	<p>
		<label>Server time:</label>
		<input type="text" readonly disabled value="<?php echo $value ?>" />
	</p>
<?php
	if(!get_post_meta($post->ID, '_product_expire', TRUE))
		$value = 'No expiration time/date yet.';
	else
		$value = date('H:i m/d/Y', get_post_meta($post->ID, '_product_expire', TRUE));
?>
	<p>
		<label>Expiration:</label>
		<input type="text" readonly disabled value="<?php echo $value ?>" />
	</p>
<?php
	if(is_active($post->ID))
		$value = 'Active';
	else
		$value = 'Expired';
?>
	<p>
		<label>Status:</label>
		<input type="text" readonly disabled value="<?php echo $value ?>" />
	</p>
<?php
	}

	public function save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

		if(isset($_POST['justaquit_deals'])):
			if(!wp_verify_nonce($_POST['justaquit_deals'], plugin_basename(__FILE__)))
				return;

			if('page' == $_POST['post_type']):
				if(!current_user_can('edit_page', $post_id)):
					return;
				endif;
			else:
				if(!current_user_can('edit_post', $post_id)):
					return;
				endif;
			endif;
		else:
			return;
		endif;
		$post = get_post($post_id);

		$quantity    = $_POST['quantity'];
		$price_real  = $_POST['price_real'];
		$price_max   = $_POST['price_max'];
		$price_min   = $_POST['price_min'];
		$expire_date = $_POST['expire_date'];
		$expire_time = $_POST['expire_time'];

		if($price_min > $price_max):
			$aux       = $price_max;
			$price_max = $price_min;
			$price_min = $aux;
		endif;

		if($price_max > $price_real):
			$aux        = $price_real;
			$price_real = $price_max;
			$price_max  = $aux;
		endif;

		$price_real  = esc_attr($price_real);
		$price_max   = esc_attr($price_max);
		$price_min   = esc_attr($price_min);
		$quantity    = esc_attr($quantity);
		$expire_date = preg_replace('/\//', '.', $expire_date);
		$expire_time = preg_replace('/:/', '.', $expire_time);
		$expire      = $expire_time . ' ' . $expire_date;
		$expire      = parse_from_format( 'HH.ii mm.dd.yyyy', $expire );
		$expire      = mktime($expire['hour'], $expire['minute'], 0, $expire['month'], $expire['day'], $expire['year']);

		if(strtotime($post->post_date) > $expire)
			$expire = strtotime($post->post_date);

		add_post_meta($post_id, '_product_price_real', $price_real, TRUE) or update_post_meta($post_id, '_product_price_real', $price_real);
		add_post_meta($post_id, '_product_price_max', $price_max, TRUE)   or update_post_meta($post_id, '_product_price_max', $price_max);
		add_post_meta($post_id, '_product_price_min', $price_min, TRUE)   or update_post_meta($post_id, '_product_price_min', $price_min);
		add_post_meta($post_id, '_product_quantity', $quantity, TRUE)     or update_post_meta($post_id, '_product_quantity', $quantity);
		add_post_meta($post_id, '_product_expire', $expire, TRUE)         or update_post_meta($post_id, '_product_expire', $expire);
	}

	public function scripts(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-timepicjer', plugin_dir_url(__FILE__).'../javascript/timepicker.jquery.js');
		wp_enqueue_script('ja-deals-admin', plugin_dir_url(__FILE__).'../javascript/admin.jquery.js');
	}

	public function stylesheets(){
		wp_register_style( 'ja-deals-admin', plugin_dir_url(__FILE__).'../stylesheet/admin.jquery.css', false, '1.8.23' );
		wp_enqueue_style( 'ja-deals-admin' );
	}

	public function add(){
		add_meta_box( 'deals_post_box', 'Deal Information', array($this, 'box'), 'post', 'side', 'high' );
	}
}
$init = new Box();
?>