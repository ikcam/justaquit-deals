<?php
/*
Plugin Name: JustAquit Products
Plugin URI: http://justaquit.com
Description: This plugins allows you to use your WordPress installation as a offers system.
Version: 1.0
Author: Irving Kcam
Author URI: http://ikcam.com
License: GPL2
*/
?>
<?php
class product_box {
	/*
	@@ Función: box_meta @@
	Lista de opciones que se mostrarán al momento de editar el post.
	*/
	function box_meta( $post ) {
		wp_nonce_field( plugin_basename(__FILE__), 'justaquit_product' );
?>
<?php
	if( !get_post_meta( $post->ID, '_product_quantity', TRUE ) )
		$value = 0;
	else
		$value = get_post_meta( $post->ID, '_product_quantity', TRUE );
?>
	<p>
		<label>Quantity:</label>
		<input type="text" name="quantity" id="quantity" value="<?php echo $value ?>" />
		<br /><span class="description">0 for unlimited</span>
	</p>

<?php
	if( !get_post_meta( $post->ID, '_product_price_real', TRUE ) )
		$value = 0;
	else
		$value = get_post_meta( $post->ID, '_product_price_real', TRUE );
?>
	<p>
		<label>Real Price:</label>
		<input type="text" name="price_real" id="price_real" value="<?php echo $value ?>" />
	</p>
<?php 
	if ( !get_post_meta( $post->ID, '_product_price_max', TRUE ) )
		$value = 100;
	else
		$value = get_post_meta( $post->ID, '_product_price_max', TRUE );
?>
	<p>
		<label>Max Price:</label>
		<input type="text" name="price_max" id="price_max" value="<?php echo $value ?>" />
	</p>

<?php
	if( !get_post_meta( $post->ID, '_product_price_min', TRUE ) )
		$value = 0;
	else
		$value = get_post_meta( $post->ID, '_product_price_min', TRUE );
?>
	<p>
		<label>Min Price:</label>
		<input type="text" name="price_min" id="price_min" value="<?php echo $value ?>" />
	</p>

<?php
	if( !get_post_meta( $post->ID, '_product_expire', TRUE ) )
		$value = date( 'm/d/Y', time() );
	else
		$value = date( 'm/d/Y', get_post_meta( $post->ID, '_product_expire', TRUE ) );
?>
	<p>
		<label>Expire Date:</label>
		<input type="text" name="expire_date" id="expire_date" value="<?php echo $value ?>" />
	</p>

<?php
	if( !get_post_meta( $post->ID, '_product_expire', TRUE ) )
		$value = date( 'H:i', time() );
	else
		$value = date( 'H:i', get_post_meta( $post->ID, '_product_expire', TRUE ) );
?>
	<p>
		<label>Expire Time:</label>
		<input type="text" name="expire_time" id="expire_time" value="<?php echo $value ?>" />
	</p>
	<hr />
	<h4>Saved Information:</h4>
<?php 
	$value = strtotime( current_time('mysql') );
	$value = date( 'H:i m/d/Y', $value );
?>
	<p>
		<label>Server time:</label>
		<input type="text" readonly disabled value="<?php echo $value ?>" />
	</p>
<?php
	if( !get_post_meta( $post->ID, '_product_expire', TRUE ) )
		$value = 'No expire time/date yet.';
	else
		$value = date( 'H:i m/d/Y', get_post_meta( $post->ID, '_product_expire', TRUE ) );
?>
	<p>
		<label>Expiration:</label>
		<input type="text" readonly disabled value="<?php echo $value ?>" />
	</p>
<?php
	if( product_functions::product_active( $post->ID ) == TRUE )
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

	/*
	@@ Función: box_save @@
	Permite guardar la información ingresada.
	*/
	function box_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

		if ( !wp_verify_nonce($_POST['justaquit_product'], plugin_basename(__FILE__)) )
			return;

		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;
		}

		$price_real = $_POST['price_real'];
		$price_max = $_POST['price_max'];
		$price_min = $_POST['price_min'];
		if( $price_min > $price_max ) :
			$aux = $price_max;
			$price_max = $price_min;
			$price_min = $aux;
		endif;
		if( $price_max > $price_real ):
			$aux = $price_real;
			$price_real = $price_max;
			$price_max = $aux;
		endif;

		$price_real = esc_attr( $price_real );
		$price_max  = esc_attr( $price_max );
		$price_min  = esc_attr( $price_min );

		$quantity  = $_POST['quantity'];
		$quantity  = esc_attr( $quantity );

		$expire_date = $_POST['expire_date'];
		$expire_date = preg_replace( '/\//', '.', $expire_date );
		$expire_time = $_POST['expire_time'];
		$expire_time = preg_replace( '/:/', '.', $expire_time );

		$expire = $expire_time . ' ' . $expire_date;
		$expire = parse_from_format( 'HH.ii mm.dd.yyyy', $expire );
		$expire = mktime( $expire['hour'], $expire['minute'], 0, $expire['month'], $expire['day'], $expire['year'] );

		add_post_meta($post_id, '_product_price_real', $price_real, true) or update_post_meta( $post_id, '_product_price_real', $price_real );
		add_post_meta($post_id, '_product_price_max', $price_max, true) or update_post_meta( $post_id, '_product_price_max', $price_max );
		add_post_meta($post_id, '_product_price_min', $price_min, true) or update_post_meta( $post_id, '_product_price_min', $price_min );
		add_post_meta($post_id, '_product_quantity', $quantity, true) or update_post_meta( $post_id, '_product_quantity', $quantity );
		add_post_meta($post_id, '_product_expire', $expire, true) or update_post_meta( $post_id, '_product_expire', $expire );
	}

	/*
	@@ Función: box_add @@
	Activa el recuadro al momento de editar el post.
	*/
	function box_add(){
		add_meta_box( 'product_box', 'Product Information', array('product_box', 'box_meta'), 'post', 'side', 'high' );
	}

	/*
	@@ Función: box_enqueue @@
	Llama los scripts o CSS necesarios para la ejecución del plugin.
	*/
	function box_enqueue(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-timepicker', plugins_url('js/timepicker.jquery.js', __FILE__) );
		wp_enqueue_script( 'jquery-admin', plugins_url('js/admin.jquery.js', __FILE__) );
		wp_register_style( 'box_custom_style', plugins_url('css/admin.jquery.css', __FILE__), false, '1.8.23' );
		wp_enqueue_style( 'box_custom_style' );
	}
} // End of class: product_box

// Call WordPress actions for product_box
add_action( 'admin_enqueue_scripts', array( 'product_box', 'box_enqueue' ) );
add_action( 'add_meta_boxes', array( 'product_box', 'box_add') );
add_action( 'save_post', array('product_box', 'box_save') );
// End of Call WordPress actions for product_box

class product_functions {
	/*
	@@ Función: product_active @@
	Arg:
		- ID: The product ID
	(Boolean) Devuelve "true" en caso de que el producto se encuentre activo.
	*/
	public function product_active( $ID ){
		// Post information
		$post = get_post($ID);

		// Variables
		$time_current = strtotime( current_time('mysql') );
		$time_expire  = get_post_meta( $post->ID, '_product_expire', TRUE );

		// Result
		if( $time_expire > $time_current )
			return true;
		else
			return false;
	}

	/*
	@@ Función: price_current @@
	Devuelve el precio actual del producto a través de una variable.
	*/
	public function price_current(){
		// Post information
		$ID   = get_the_ID();
		$post = get_post($ID);

		// If product is active do calculation
		if( product_functions::product_active( $post->ID ) ):
			// Variables
			$price_max      = get_post_meta( $post->ID, '_product_price_max', TRUE );
			$price_min      = get_post_meta( $post->ID, '_product_price_min', TRUE );
			$time_expire    = get_post_meta( $post->ID, '_product_expire', TRUE );
			$time_published = strtotime( $post->post_date );
			$time_current   = strtotime( current_time('mysql') );
			
			// Calculation
			$time_onair     = $time_current - $time_published;
			$time_total     = $time_expire - $time_published;
			$price_diff     = $price_max - $price_min;
			$price          = $price_min + ( $time_onair * $price_diff ) / $time_total;
			$price          = floor( $price * 1000 ) / 1000;
		// If product isn't active the price is equal the max price
		else :
			$price          = get_post_meta( $post->ID, '_product_price_max', TRUE );
		endif;

		// Result
		return $price;
	}

	/*
	@@ Función: price_post @@
	Arg:
		- ID: post ID
	Devuelve el precio actual del producto a través de una variable.
	*/
	public function price_post( $ID ){
		// Post information
		$post = get_post($ID);

		// If product is active do calculation
		if( product_functions::product_active( $post->ID ) ):
			// Variables
			$price_max      = get_post_meta( $post->ID, '_product_price_max', TRUE );
			$price_min      = get_post_meta( $post->ID, '_product_price_min', TRUE );
			$time_expire    = get_post_meta( $post->ID, '_product_expire', TRUE );
			$time_published = strtotime( $post->post_date );
			$time_current   = strtotime( current_time('mysql') );
			
			// Calculation
			$time_onair     = $time_current - $time_published;
			$time_total     = $time_expire - $time_published;
			$price_diff     = $price_max - $price_min;
			$price          = $price_min + ( $time_onair * $price_diff ) / $time_total;
			$price          = floor( $price * 1000 ) / 1000;
		// If product isn't active the price is equal the max price
		else :
			$price          = get_post_meta( $post->ID, '_product_price_max', TRUE );
		endif;

		// Result
		return $price;
	}

	function price_by_time( $ID, $time ){
		// Post information
		$post = get_post($ID);

		// If product is active do calculation
		if( product_functions::product_active( $post->ID ) ):
			// Variables
			$price_max      = get_post_meta( $post->ID, '_product_price_max', TRUE );
			$price_min      = get_post_meta( $post->ID, '_product_price_min', TRUE );
			$time_expire    = get_post_meta( $post->ID, '_product_expire', TRUE );
			$time_published = strtotime( $post->post_date );
			$time_current   = floor($time);
			
			// Calculation
			$time_onair     = $time_current - $time_published;
			$time_total     = $time_expire - $time_published;
			$price_diff     = $price_max - $price_min;
			$price          = $price_min + ( $time_onair * $price_diff ) / $time_total;
			$price          = floor( $price * 100 ) / 100;
		// If product isn't active the price is equal the max price
		else :
			$price          = get_post_meta( $post->ID, '_product_price_max', TRUE );
		endif;

		// Result
		return $price;
	}

	/*
	@@ Función: discount_current @@
	Devuelve el descuento actual del producto a través de una variable.
	*/
	public function discount_current(){
		// Post information
		$ID   = get_the_ID();
		$post = get_post($ID);

		// Variables
		$price_real    = get_post_meta( $post->ID, '_product_price_real', TRUE );
		$price_current = product_functions::price_current();
		
		// Calculation
		$discount      = 100 - ( ( 100 * $price_current ) / $price_real );
		$discount      = floor( $discount * 10 ) / 10;
				
		// Result
		return $discount;
	}

	/*
	@@ Función: discount_post @@
	Arg:
		- ID: ID del post.
	Devuelve el descuento actual del producto a través de una variable.
	*/
	public function discount_post( $ID ){
		// Post information
		$post = get_post($ID);
	
		// Variables
		$price_real     = get_post_meta( $post->ID, '_product_price_real', TRUE );
		$price_current = product_functions::price_post( $post->ID );
		
		// Calculation
		$discount      = 100 - ( ( 100 * $price_current ) / $price_real );
		$discount      = floor( $discount * 10 ) / 10;

		// Result
		return $discount;
	}

	/*
	@@ Función: time_current @@
	Devuelve el timepo restante actual del producto a través de una variable.
	*/
	public function time_current(){
		// Post information
		$ID   = get_the_ID();
		$post = get_post($ID);

		// If product is active do calculation
		if( product_functions::product_active( $post->ID ) ):
			// Variables
			$time_expire  = get_post_meta( $post->ID, '_product_expire', TRUE );
			$time_current = strtotime( current_time('mysql') );
			
			// Calculation
			$time         = $time_expire - $time_current;
			$time_hours   = $time / 3600;
			$time_hours   = floor( $time_hours );
			$time_mins    = ($time % 3600) / 60;
			$time_mins    = floor( $time_mins );
			$time_secs    = $time - ( ($time_hours * 3600) + ($time_mins * 60) );
			$output       = $time_hours.':'.$time_mins.':'.$time_secs;

		// If product isn't active the time is equal "Expired"
		else :
			$output         = 'Active';
		endif;

		// Result
		return $output;
	}

	/*
	@@ Función: show_inputs @@
	Devuelve el conjunto de inputs necesarios para el calculo vía JS.
	*/
	public function show_inputs($ID){
		// Post Information
		$post = get_post($ID);
		
		// Variables
		$price_real     = get_post_meta( $post->ID, '_product_price_real', TRUE );
		$price_max      = get_post_meta( $post->ID, '_product_price_max', TRUE );
		$price_min      = get_post_meta( $post->ID, '_product_price_min', TRUE );
		$time_expire    = get_post_meta( $post->ID, '_product_expire', TRUE );
		$time_published = strtotime($post->post_date);
		$time_current   = strtotime( current_time('mysql') );

		// Output
		$output         = "\n".'<input type="hidden" name="price_real" id="price_real" value="'.$price_real.'" />';
		$output        .= "\n".'<input type="hidden" name="price_max" id="price_max" value="'.$price_max.'" />';
		$output        .= "\n".'<input type="hidden" name="price_min" id="price_min" value="'.$price_min.'" />';
		$output        .= "\n".'<input type="hidden" name="time_published" id="time_published" value="'.$time_published.'" />';
		$output        .= "\n".'<input type="hidden" name="time_expire" id="time_expire" value="'.$time_expire.'" />';
		$output        .= "\n".'<input type="hidden" name="time_current" id="time_current" value="'.$time_current.'" />';
		// Result
		return $output;
	}

	/*
	@@ Función: get_categories @@
	Args:
		- (Int) count: Número de elementos requeridos
	*/
	public function get_categories( $count ){
		// Globalize $wpdb for SQL queries
		global $wpdb;
		// Vars
		$time_current = strtotime( current_time('mysql') );
		// Query
		$query = "SELECT * FROM $wpdb->terms WHERE term_id IN ( SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id IN ( SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ) ) AND term_id IN ( SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' AND parent = 0 ) ORDER BY term_id DESC LIMIT 0,%d";
		$categories = $wpdb->get_results( $wpdb->prepare( $query, $count ) );
		// Result
		if( !$categories )
			return false;
		else
			return $categories;
	}

	/*
	@@ Función: get_category_posts @@
	Argumentos:
		- (Int) cat_id: ID de la categoría.
		- (Int) count: Número de elementos requeridos.
	Devuelve posts activos de una categoría específica.
	*/
	public function get_category_posts( $cat_id, $count ){
		// Globalize $wpdb for SQL queries
		global $wpdb;
		// Vars
		$time_current = strtotime( current_time('mysql') );
		// Query
		$query = "SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND ID IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d ) ORDER BY post_date DESC LIMIT 0, %d";
		$posts = $wpdb->get_results( $wpdb->prepare( $query, $cat_id, $count ) );
		// Result
		if( !$posts )
			return false;
		else
			return $posts;
	}

	/*
	@@ Función: views_count @@
	Contador de la cantidad de vistas de un producto
	*/
	function views_count(){
		if( is_single() && !is_admin() ){
			$ID   = get_the_ID();
			$post = get_post($ID);

			$count = get_post_meta( $post->ID, '_product_views', TRUE );

			if( !$count )
				$count = 1;
			else {
				$count = intval( $count );
				$count++;				
			}
			add_post_meta($post->ID, '_product_views', $count, true) or update_post_meta( $post->ID, '_product_views', $count );
		}
	}

	/*
	@@ Función: buys_count @@
	Contador de la canridad de compras de un producto
	*/
	function buys_count($ID){
		$ID    = intval( $ID );
		$count = get_post_meta( $ID, '_product_buys', TRUE );

		if( !$count )
			$count = 1;
		else {
			$count = intval( $count );
			$count++;
		}

		add_post_meta($post->ID, '_product_buys', $count, true) or update_post_meta( $post->ID, '_product_buys', $count );
	}

	function details_script(){
		$ID = get_the_ID();
		if( product_functions::product_active( $ID ) && is_single() ) :
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-timer', plugins_url('js/timer.jquery.js',__FILE__) );
			wp_enqueue_script( 'jquery-details', plugins_url('js/details.jquery.js',__FILE__) );
		endif;
	}

	function get_order( $ID ){
		$ID = intval($ID);

		global $wpdb;

		$table = $wpdb->prefix.'orders';
		$query = "SELECT * FROM $table WHERE ID = %d";
		$order = $wpdb->get_row( $wpdb->prepare( $query, $ID ) );

		if( $order )
			return $order;
		else
			return false;
	}

	function get_transaction_by_order( $ID ){
		$ID = intval($ID);

		global $wpdb;
		$table       = $wpdb->prefix.'transactions';
		$query       = "SELECT * FROM $table WHERE order_id = %d";
		$transaction = $wpdb->get_row( $wpdb->prepare( $query, $ID ) );

		if( $transaction)
			return $transaction;
		else
			return false;
	}

	function get_coupon_by_order( $ID ){
		$ID = intval($ID);

		global $wpdb;
		$table  = $wpdb->prefix.'coupons';
		$query  = "SELECT * FROM $table WHERE order_id = %d";
		$coupon = $wpdb->get_row( $wpdb->prepare( $query, $ID ) );

		if( $coupon)
			return $coupon;
		else
			return false;
	}

	function get_coupon_by_code( $code ){
		global $wpdb;
		$table = $wpdb->prefix.'coupons';
		$query = "SELECT * FROM $table WHERE code = %s";
		$coupon = $wpdb->get_row( $wpdb->prepare( $query, $code ) );

		if( $coupon )
			return $coupon;
		else
			return false;
	}

	function get_order_status( $ID ){
		$ID = intval( $ID );

		global $wpdb;
		$table = $wpdb->prefix.'transactions';
		$query = "SELECT * FROM $table WHERE order_id = %d";
		$transaction = $wpdb->get_row( $wpdb->prepare($query, $ID) );
		$status = $transaction->status;

		if( $status == 3 )
			return true;
		else
			return false;
	}

	/*
	@@ Función: shortcode_details
	*/
	function shortcode_details(){
		// Get post information
		$ID = get_the_ID();
		$post = get_post($ID);
		$views = get_post_meta( $post->ID, '_product_views', TRUE );

		// If post is active execute
		$output  = "\n".'<form class="product_details" method="post" action="'.get_bloginfo('url').'/store/checkout" >';
		$output .= product_functions::show_inputs( $post->ID );
	  $output .= "\n\t".'<input type="hidden" name="price" id="price" value="'.product_functions::price_current().'" />';
	  $output .= "\n\t".'<input type="hidden" name="the_id" id="the_id" value="'.get_the_ID().'" />';
	  $output .= "\n\t".'<input type="hidden" name="url" id="url" value="'.get_permalink().'" />';
	  $output .= "\n\t".'<div class="top">';
	  $output .= "\n\t\t".'<div class="product_time">';
	  $output .= "\n\t\t\t".'<span id="time">'.product_functions::time_current().'</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="discount">';
	  $output .= "\n\t\t\t".'<span id="discount">'.product_functions::discount_current().'</span>% savings';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="price">';
	  $output .= "\n\t\t\t".'<span id="price">$'.product_functions::price_current().'</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<input type="submit" value="Buy Now!" />';
	  $output .= "\n\t".'</div>';
	  $output .= "\n\t".'<div class="bottom">';
	  $output .= "\n\t\t".'<div class="views">';
	  $output .= "\n\t\t\t".'<span id="views">'.$views.' Views</span>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="gift">';
	  $output .= kk_star_ratings( $post->ID );
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t\t".'<div class="share">';
	  $output .= "\n\t\t\t".'<div class="fb-like" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false" data-action="like" data-font="arial"></div>';
		$output .= "\n".'<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-hashtags="mydealisideal">Tweet</a>';
		$output .= "\n".'<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		$output .= "\n".'<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
<script type="IN/Share" data-counter="right"></script>';
		$output .= "\n".'<div class="g-plus" data-action="share" data-annotation="bubble"></div>';
	  $output .= "\n\t\t".'</div>';
	  $output .= "\n\t".'</div>';
	  $output .= "\n".'</form>';
	  $output .= "\n".'<div class="clearfix"></div>';

		// Result
		return $output;
	}
} // End of class: product_functions
add_action( 'wp_head', array('product_functions', 'views_count') );
add_action( 'wp_enqueue_scripts', array('product_functions', 'details_script') );
add_shortcode( 'details', array('product_functions', 'shortcode_details')  );

class product_slider {
	/*
	@@ Función: slider_enqueue @@
	Scripts necesarios para la ejecución del slider
	*/
	function slider_enqueue(){
		if( is_front_page() ){
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('jquery-timer', plugins_url('js/timer.jquery.js', __FILE__) );
			wp_enqueue_script('jquery-slider', plugins_url('js/slider.jquery.js', __FILE__) );
		}
	}

	function get_all_products( $count ){
		$args = array(
			'numberposts'     => $count,
			'offset'          => 0,
			'orderby'         => 'rand',
			'meta_key'        => '_product_expire',
			'post_type'       => 'post',
			'post_status'     => 'publish'
		);
		$posts = get_posts($args);

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_new_products( $count ){
		$args = array(
			'numberposts'     => $count,
			'offset'          => 0,
			'orderby'         => 'post_date',
			'meta_key'        => '_product_expire',
			'post_type'       => 'post',
			'post_status'     => 'publish'
		);
		$posts = get_posts($args);

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_ending_products( $count ){
		global $wpdb;
		$time_current = strtotime( current_time('mysql') );

		$query = "SELECT * FROM $wpdb->posts AS a	INNER JOIN $wpdb->postmeta AS b ON a.ID = b.post_id WHERE a.post_type = 'post' AND a.post_status = 'publish' AND b.meta_key = '_product_expire' AND b.meta_value > %d ORDER BY b.meta_value ASC LIMIT 0,%d";
		$posts = $wpdb->get_results( $wpdb->prepare( $query, $time_current, $count ) );

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_gone_products( $count ){
		global $wpdb;
		$time_current = strtotime( current_time('mysql') );

		$query = "SELECT * FROM $wpdb->posts AS a	INNER JOIN $wpdb->postmeta AS b ON a.ID = b.post_id WHERE a.post_type = 'post' AND a.post_status = 'publish' AND b.meta_key = '_product_expire' AND b.meta_value < %d ORDER BY b.meta_value ASC LIMIT 0,%d";
		$posts = $wpdb->get_results( $wpdb->prepare( $query, $time_current, $count ) );

		if( $posts )
			return $posts;
		else
			return false;
	}

	function get_views_products( $count ){
		$args = array(
			'numberposts'     => $count,
			'offset'          => 0,
			'orderby'         => 'meta_value_num',
			'order'           => 'DESC',
			'meta_key'        => '_product_views',
			'post_type'       => 'post',
			'post_status'     => 'publish'
		);
		$posts = get_posts($args);

		if( $posts )
			return $posts;
		else
			return false;
	}
} // End of class: product_slide

// Call WordPress actions for product_slider
add_action( 'wp_enqueue_scripts', array('product_slider', 'slider_enqueue')  );
// End of Call WordPress actions for product_slider


class product_transaction{
	function scripts(){
		if( is_page( 'Checkout' ) ){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-timer', plugins_url( 'js/timer.jquery.js', __FILE__ ) );
			wp_enqueue_script( 'jquery-checkout', plugins_url( 'js/checkout.jquery.js', __FILE__ ) );
      wp_register_style( 'style-checkout', plugins_url('css/checkout.css', __FILE__) );
			wp_enqueue_style( 'style-checkout' );
		}
	}

	/*
	@@ Función: shortcode_checkout @@
	LLamar las funciones necesarias.
	*/
	function shortcode_checkout(){
		$product_id  = $_POST['the_id'];
		$product_time_buy = $_POST['time_current'];
		$server_time_buy = strtotime( current_time('mysql') );
		$server_time_buy = $server_time_buy;
		$time_remain = 120 - ( $server_time_buy - $product_time_buy );

		if( (($time_remain <= 0 ) || ( !$product_id )) && product_functions::product_active( $product_id ) ) :
			echo '<div class="countdown">Sorry, your time has expire, you have to place again your order.</div>';
		else :
			$post = get_post($product_id);
			$total = 0;
?>
	<form action="<?php bloginfo('url') ?>/store/transaction" method="post">
		<input type="hidden" name="time_buy" id="time_buy" value="<?php echo $product_time_buy ?>" />
		<input type="hidden" name="time_server" id="time_server" value="<?php echo $server_time_buy ?>" />
		<table class="table-checkout">
		<tbody>
			<tr>
				<td colspan="6"><h4>PRODUCTS</h4></td>
			</tr>
			<tr class="product-item" id="product-<?php echo $post->ID ?>">
				<input type="hidden" name="the_id" value="<?php echo $post->ID ?>" />
				<td class="thumb">
					<?php echo get_the_post_thumbnail( $post->ID, array(100,100,TRUE) ); ?>
				</td>
				<td colspan="4">
					<strong><?php echo $post->post_title ?></strong>
					<br />
					<small>
						<a id="delete" href="#">Delete</a> | 
						<a id="view" href="<?php echo get_permalink($post->ID) ?>" target="_blank">View</a>
					</small>
				</td>
				<td class="price">
					$
					<span id="price"><?php 
						$total = $total + product_functions::price_post($post->ID);
						$total = floor($total*100)/100;
						echo floor(product_functions::price_post($post->ID)*100)/100;
					?></span>
				</td>
			</tr>
			<tr class="total">
				<td colspan="5"><strong>Total</strong></td>
				<td class="price"><strong>$<span id="total"><?php echo $total ?></span></strong></td>
			</tr>
			<tr>
				<td colspan="6">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="6"><h4>BILLING/CONTACT DETAILS</h4></td>
			</tr>
			<tr><!-- Email -->
				<td colspan="2"><label for="contact_email">Email *</td>
				<td><input type="email" name="contact_email" id="contact_email" required /></td>
			</tr>
			<tr><!-- First Name -->
				<td colspan="2"><label for="contact_first_name">First Name *</td>
				<td><input type="text" name="contact_first_name" id="contact_first_name" required /></td>
			</tr>
			<tr><!-- Last Name -->
				<td colspan="2"><label for="contact_last_name">Last Name *</td>
				<td><input type="text" name="contact_last_name" id="contact_last_name" required /></td>
			</tr>
			<tr><!-- Pay with PayPal -->
				<td colspan="5">
				</td>
				<td class="price">
					<a id="cancel" href="#">Cancel</a> 
					or
					<input type="submit" value="Buy Now">
				</td>
			</tr>
		</tbody>
		</table>
	</form>
<?php if( product_functions::product_active( $product_id ) ) : ?>
	<div class="countdown">
		Hurry up! You have <span id="countdown"><?php echo $time_remain ?></span> seconds left.
	</div>
<?php endif; ?>
<?php
		endif;
	}

	function shortcode_transaction(){
		$product_id         = $_POST['the_id'];
		$product_id         = floor( $product_id );
		$time_buy           = $_POST['time_buy'];
		$time_buy           = floor( $time_buy );
		$time_current       = strtotime( current_time('mysql') );
		$time_diff          = $time_current - $time_buy;
		$contact_email      = $_POST['contact_email'];
		$contact_first_name = $_POST['contact_first_name'];
		$contact_last_name  = $_POST['contact_last_name'];

		if( !$product_id ) :
			echo 'You haven\'t order a product yet, please choose a product.'."\n";
		else :
			if( $time_diff > 130 && product_functions::product_active( $product_id ) ) :
				echo 'Sorry, your time has expire, you have to place again your order.'."\n";
			else :
				product_functions::buys_count($product_id);
				$price = product_functions::price_by_time( $product_id, $time_buy );

				$order = 	array(
						'email'      => $contact_email,
						'first_name' => $contact_first_name,
						'last_name'  => $contact_last_name,
						'post_id'    => $product_id,
						'amount'     => $price,
						'date_time'  => $time_buy
					);
				$order_id = product_transaction::order_insert($order);
				if ( $order_id ) :
					$post = get_post( $order['post_id'] );

					$url  = 'https://www.paypal.com/cgi-bin/webscr?business=mydealisideal@gmail.com&cmd=_xclick&currency_code=USD';
					$url .= '&amount='.$order['amount'];
					$url .= '&item_name='.$post->post_title;
					$url .= '&item_number='.$order_id;
					$url .= '&shipping=0';
					$url .= '&notify_url='.home_url('/store/transaction/ipn/').'';
					$url .= '&return='.home_url('/sucessfull/');

					echo 'Please wait, you will be redirected to PayPal in a few seconds...';
					echo '
					<script type="text/javascript">
						window.location = "'.$url.'"
					</script>
					';
				else:
					
				endif;
			endif;
		endif;
	}

	function redirect($url) {
		header('Location: '.$url);
		exit();
	}

	function order_insert( $args ){
		global $wpdb;

		$table = $wpdb->prefix.'orders';
		$format = array( '%s', '%s', '%s', '%d', '%s', '%d' );
		$wpdb->insert( $table, $args, $format );
		$id = $wpdb->insert_id;

		if( $id )
			return $id;			
		else
			return false;
	}

	function transaction_insert( $args ){
		global $wpdb;

		$table = $wpdb->prefix.'transactions';
		$format = array( '%d', '%s', '%s', '%d' );
		$wpdb->insert( $table, $args, $format );
		$id = $wpdb->insert_id;

		if( $id )
			return true;
		else
			return false;
	}

	function shortcode_transaction_ipn(){
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exits = true;
		}
		foreach ($myPost as $key => $value) {        
			if($get_magic_quotes_exits == true && get_magic_quotes_gpc() == 1) { 
				$value = urlencode(stripslashes($value)); 
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.paypal.com'));

		$res = curl_exec($ch);
		curl_close($ch);
 
		// assign posted variables to local variables
		$item_name        = $_POST['item_name'];
		$item_number      = $_POST['item_number'];
		$payment_status   = $_POST['payment_status'];
		$payment_amount   = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id           = $_POST['txn_id'];
		$receiver_email   = $_POST['receiver_email'];
		$payer_email      = $_POST['payer_email'];

		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			if( $payment_status == 'Completed' ) :
				// check that txn_id has not been previously processed
				global $wpdb;
				$table = $wpdb->prefix.'transactions';
				$query = "SELECT * FROM $table WHERE txn_id = %s";
				$check = $wpdb->get_results( $wpdb->prepare( $query, $txn_id ) );
				if( !$check ) :
					// check that receiver_email is your Primary PayPal email
					if( $receiver_email == 'mydealisideal@gmail.com' ) :
						// check that payment_amount/payment_currency are correct
						$order = product_functions::get_order( $item_number );
						if( $order->amount == $payment_amount ) :
							// Add transaction
							$transaction = array(
									'order_id' => $item_number,
									'amount'   => $payment_amount,
									'txn_id'   => $txn_id,
									'status'   => 3
								);
							$transaction_id = product_transaction::transaction_insert( $transaction );

							$coupon = array(
									'order_id'    => $item_number,
									'coupon_date' => strtotime( current_time('mysql') ),
									'code'        => product_coupon::coupon_code(6),
									'status'      => 0,
									'usage_date'  => NULL
								);
							$coupon_id = product_coupon::coupon_create( $coupon );
							$product = get_post( $order->post_id );

							// Get order information
							$mail_to      = $order->email;
							$mail_subject = 'Your coupon from '.get_bloginfo();
							$mail_message = 'Your Order ID: '.$coupon['order_id']."\n";
							$mail_message .= 'Product Name: '.$product->post_title."\n";
							$mail_message .= 'Product URL: '.get_permalink($product->ID)."\n";
							$mail_message .= 'Coupon code: '.$coupon['code']."\n\n\n";
							$mail_message .= 'Thank you for purchase a promotion at '.get_bloginfo()."\n";
							$mail_message .= 'Problems? '.get_bloginfo('admin_email')."\n";
							$headers[]    = 'From: '.get_bloginfo().' <'.get_bloginfo('admin_email').'>';
							$headers[]    = 'Cc: '.get_bloginfo('admin_email');
							wp_mail( $mail_to, $mail_subject, $mail_message, $headers );

						endif;
					endif;
				endif;
			endif;
		} else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			echo 'nada que ver';
		}
	}
}
add_shortcode( 'checkout', array('product_transaction', 'shortcode_checkout') );
add_shortcode( 'transaction', array('product_transaction', 'shortcode_transaction') );
add_shortcode( 'transaction_ipn', array('product_transaction', 'shortcode_transaction_ipn') );
add_action( 'wp_enqueue_scripts', array('product_transaction', 'scripts') );

class product_coupon{
	function coupon_create( $args ){
		global $wpdb;

		$table = $wpdb->prefix.'coupons';
		$format = array( '%d', '%d', '%s', '%d', '%d' );
		$wpdb->insert( $table, $args, $format );
		$id = $wddb->insert_id;

		if( $id )
			return $id;
		else
			return false;
	}

	function coupon_gen_code( $length=6 ){
		$key = '';
		list($usec, $sec) = explode(' ', microtime());
		mt_srand((float) $sec + ((float) $usec * 100000));
		$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

		for($i=0; $i<$length; $i++) {
			$key .= $inputs{mt_rand(0,61)};
		}

		return $key;
	}

	function coupon_code( $length=6 ){
		global $wpdb;

		do {
			$code = product_transaction::coupon_gen_code($length);
			$table = $wpdb->prefix.'coupons';
			$query = "SELECT * FROM $table WHERE code = %s";
			$check = $wpdb->get_results( $wpdb->prepare( $query, $code ) );
		} while ( $check );

		return $code;
	}

	function set_active( $ID ){
		$ID = intval( $ID );
		$ID = array( 'ID' => $ID );

		global $wpdb;

		$args   = array(
				'status'     => 1,
				'usage_date' => strtotime( current_time('mysql') )
			);
		$format = array( '%d', '%d' );
		$table  = $wpdb->prefix.'coupons';
		$wpdb->update( $table, $args, $ID, $format, array( '%d' ) );
	}

	function shortcode_coupons(){
		$action = $_GET['action'];

		if( $action == 'use_coupon' ) :
			$code   = $_GET['code'];
			$coupon = product_functions::get_coupon_by_code( $code );
			// Verify if coupon exists
			if( $coupon ):
				$order = product_functions::get_order($coupon->order_id);
				$product  = get_post( $order->post_id );
				switch( $coupon->status ){
					case 0:
						$status = 'Valid Coupon';
						break;
					case 1:
						$status = 'Already used coupon';
						break;
					default:
						$status = 'Error, contact support@mydealisideal.com';
				}
?>
<h3>Coupon Information</h3>
<h5>Status</h5>
	<?php echo $status ?>
<h5>Client Information</h5>
	First Name: <?php echo $order->first_name ?><br />
	Last Name: <?php echo $order->last_name ?><br />
	Email: <?php echo $order->email ?><br />
<h5>Product Information</h5>
	Product Name: <?php echo $product->post_title ?><br/>
	Product URL: <a href="<?php echo get_permalink($product->ID) ?>" target="_blank"><?php echo get_permalink($product->ID) ?></a><br />
	Product Price: $<?php echo $order->amount ?><br />
	Buy Date: <?php echo date('H:i m-d-Y', $order->date_time) ?><br />
<h5>Status</h5>
	<?php echo $status ?>
<?php
				product_coupon::set_active( $coupon->ID );
			else :
?>
<h4>Error</h4>
You are trying to use an invalid coupon.
<?php
			endif;
		else :
?>
	<form method="get" action="<?php $_SERVER['PHP_SELF'] ?>">
		<label for="code">Coupon Code: </label>
		<input type="text" name="code" required />
		<input type="hidden" name="action" value="use_coupon" />
		<input type="submit" value="Use Coupon" />
	</form>
<?php
		endif;
	}
}
add_shortcode( 'coupons', array('product_coupon', 'shortcode_coupons') );

class product_admin {
	function page_main(){
?>
	<h3>Page Main</h3>
<?php
	}

	function orders(){
		$order = product_functions::get_order( $_GET['view'] );
		if( $order ) :
?>
<div class="wrap">
	<h2>View Order <?php echo $order->ID ?></h2>
	<h3>Client Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">First Name:</th>
			<td><input type="text" disabled value="<?php echo $order->first_name ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Last Name:</th>
			<td><input type="text" disabled value="<?php echo $order->last_name ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Email:</th>
			<td><input type="text" disabled value="<?php echo $order->email ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Status:</th>
			<td>
<?php
	$status = product_functions::get_order_status( $order->ID );
	if( $status )
		echo '<input type="text" disabled value="Paid" />';
	else
		echo '<input type="text" disabled value="Unpaid" />';
?>
			</td>
		</tr>
	</tbody>
	</table>

	<h3>Product Information</h3>
<?php
	$product = get_post( $order->post_id );
?>	
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Product ID:</th>
			<td><input type="text" disabled value="<?php echo $product->ID ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Name:</th>
			<td><input type="text" disabled value="<?php echo $product->post_title ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">URL:</th>
			<td><a href="<?php echo get_permalink($product->ID) ?>" target="_blank"><?php echo get_permalink($product->ID) ?></a></td>
		</tr>
		<tr valign="top">
			<th scope="row">Price:</th>
			<td><input type="text" disabled value="<?php echo $order->amount ?>" /></td>
		</tr>
	</tbody>
	</table>
<?php
	if( $status ) :
		$transaction = product_functions::get_transaction_by_order( $order->ID );
?>
	<h3>Transaction Information</h3>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Transaction ID:</th>
			<td><input type="text" disabled value="<?php echo $transaction->ID ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">PayPal Txn ID:</th>
			<td><input type="text" disabled value="<?php echo $transaction->txn_id ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Date:</th>
			<td><input type="text" disabled value="<?php echo date('H:i m/d/Y', $order->date_time) ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Amount:</th>
			<td><input type="text" disabled value="<?php echo $transaction->amount ?>" /></td>
		</tr>
	</tbody>
	</table>

	<h3>Coupon Information</h3>
<?php
	$coupon = product_functions::get_coupon_by_order( $order->ID );
?>
	<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">Coupon ID:</th>
			<td><input type="text" disabled value="<?php echo $coupon->ID ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Coupon Code:</th>
			<td><input type="text" disabled value="<?php echo $coupon->code ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Status:</th>
<?php
	if( $coupon->status == 1 ) :
?>
			<td><input type="text" disabled value="Already used." /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Usage Date:</th>
			<td><input type="text" disabled value="<?php echo date( 'H:i m/d/Y', $coupon->usage_date ) ?>" /></td>
<?php
	else :
?>
			<td><input type="text" disabled value="Already used." /></td>
<?php
	endif;
?>
		</tr>
	</tbody>
	</table>
<?php
	endif;
?>
</div>
<?php
		else:
?>
<div class="wrap">
	<h2>Orders</h2>

	<table class="wp-list-table widefat fixed orders" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="id" class="manage-column column-id">
					<span>Order ID</span>
				</th>
				<th scope="col" id="author" class="manage-column column-author">
					<span>First Name</span>
				</th>
				<th scope="col" id="author" class="manage-column column-author">
					<span>Last Name</span>
				</th>
				<th scope="col" id="email" class="manage-column column-email">
					<span>Email</span>
				</th>
				<th scope="col" id="date" class="manage-column column-date">
					<span>Date</span>
				</th>
				<th scope="col" id="Status" class="manage-column column-status">
					<span>Status</span>
				</th>
				<th scope="col" id="options" class="manage-column column-options">
					<span>Options</span>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="id" class="manage-column column-id">
					<span>Order ID</span>
				</th>
				<th scope="col" id="author" class="manage-column column-author">
					<span>First Name</span>
				</th>
				<th scope="col" id="author" class="manage-column column-author">
					<span>Last Name</span>
				</th>
				<th scope="col" id="email" class="manage-column column-email">
					<span>Email</span>
				</th>
				<th scope="col" id="date" class="manage-column column-date">
					<span>Date</span>
				</th>
				<th scope="col" id="Status" class="manage-column column-status">
					<span>Status</span>
				</th>
				<th scope="col" id="options" class="manage-column column-options">
					<span>Options</span>
				</th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	global $wpdb;
	$table = $wpdb->prefix.'orders';
	$query = "SELECT * FROM $table ORDER BY date_time DESC";
	$orders = $wpdb->get_results( $query );
	foreach( $orders as $order ):
?>
			<tr>
				<td class="id column-id"><?php echo $order->ID ?></td>
				<td class="author column-author"><?php echo $order->first_name ?></td>
				<td class="author column-author"><?php echo $order->last_name ?></td>
				<td class="email column-email"><?php echo $order->email ?></td>
				<td class="date column-date"><?php echo date('H:i m/d/Y', $order->date_time) ?></td>
				<td class="status column-status">
<?php
$status = product_functions::get_order_status( $order->ID );
if( $status )
	echo 'Pay';
else
	echo 'Unpay';
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
		endif;
	}

	function transactions(){
?>
<div class="wrap">
	<h2>Transactions</h2>

	<table class="wp-list-table widefat fixed transactions" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="id" class="manage-column column-id">
					<span>ID</span>
				</th>
				<th scope="col" id="order" class="manage-column column-order">
					<span>Order ID</span>
				</th>
				<th scope="col" id="amount" class="manage-column column-amount">
					<span>Amount</span>
				</th>
				<th scope="col" id="txnid" class="manage-column column-txnid">
					<span>PayPal Txn ID</span>
				</th>
				<th scope="col" id="date" class="manage-column column-date">
					<span>Date</span>
				</th>
				<th scope="col" id="Status" class="manage-column column-status">
					<span>Status</span>
				</th>
				<th scope="col" id="options" class="manage-column column-options">
					<span>Options</span>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="id" class="manage-column column-id">
					<span>ID</span>
				</th>
				<th scope="col" id="order" class="manage-column column-order">
					<span>Order ID</span>
				</th>
				<th scope="col" id="amount" class="manage-column column-amount">
					<span>Amount</span>
				</th>
				<th scope="col" id="txnid" class="manage-column column-txnid">
					<span>PayPal Txn ID</span>
				</th>
				<th scope="col" id="date" class="manage-column column-date">
					<span>Date</span>
				</th>
				<th scope="col" id="status" class="manage-column column-status">
					<span>Status</span>
				</th>
				<th scope="col" id="options" class="manage-column column-options">
					<span>Options</span>
				</th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	global $wpdb;
	$table = $wpdb->prefix.'transactions';
	$query = "SELECT * FROM $table ORDER BY ID DESC";
	$transactions = $wpdb->get_results( $query );
	foreach( $transactions as $transaction ):
		$order = product_functions::get_order( $transaction->order_id );
?>
			<tr>
				<td class="id column-id"><?php echo $transaction->ID ?></td>
				<td class="order column-order"><?php echo $transaction->order_id ?></td>
				<td class="amount column-amount">$<?php echo $transaction->amount ?></td>
				<td class="txnid column-txnid"><?php echo $transaction->txn_id ?></td>
				<td class="date column-date"><?php echo date('H:i m/d/Y', $order->date_time) ?></td>
				<td class="status column-status">
<?php
if( $transaction->status == 3 )
	echo 'Pay';
else
	echo 'Unpay';
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

	function coupons(){
?>
<div class="wrap">
	<h2>Coupons</h2>

	<table class="wp-list-table widefat fixed coupons" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="id" class="manage-column column-id">
					<span>ID</span>
				</th>
				<th scope="col" id="order" class="manage-column column-order">
					<span>Order ID</span>
				</th>
				<th scope="col" id="code" class="manage-column column-code">
					<span>Code</span>
				</th>
				<th scope="col" id="date" class="manage-column column-date">
					<span>Date</span>
				</th>
				<th scope="col" id="status" class="manage-column column-status">
					<span>Status</span>
				</th>
				<th scope="col" id="usage" class="manage-column column-usage">
					<span>Usage Date</span>
				</th>
				<th scope="col" id="options" class="manage-column column-options">
					<span>Options</span>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="id" class="manage-column column-id">
					<span>ID</span>
				</th>
				<th scope="col" id="order" class="manage-column column-order">
					<span>Order ID</span>
				</th>
				<th scope="col" id="code" class="manage-column column-code">
					<span>Code</span>
				</th>
				<th scope="col" id="date" class="manage-column column-date">
					<span>Date</span>
				</th>
				<th scope="col" id="status" class="manage-column column-status">
					<span>Status</span>
				</th>
				<th scope="col" id="usage" class="manage-column column-usage">
					<span>Usage Date</span>
				</th>
				<th scope="col" id="options" class="manage-column column-options">
					<span>Options</span>
				</th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	global $wpdb;
	$table = $wpdb->prefix.'coupons';
	$query = "SELECT * FROM $table ORDER BY coupon_date DESC";
	$coupons = $wpdb->get_results( $query );
	foreach( $coupons as $coupon ):
		$order = product_functions::get_order( $coupon->order_id );
?>
			<tr>
				<td class="id column-id"><?php echo $coupon->ID ?></td>
				<td class="order column-order"><?php echo $coupon->order_id ?></td>
				<td class="code column-code"><?php echo $coupon->code ?></td>
				<td class="date column-date"><?php echo date('H:i m/d/Y', $coupon->coupon_date) ?></td>
				<td class="status column-status">
<?php
if( $coupon->status == 0 )
	echo 'Valid';
else
	echo 'Used';
?>
				</td>
				<td class="txnid column-txnid">
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

	function init(){
		add_menu_page( 'Products', 'Products', 'administrator', 'product', array('product_admin', 'page_main'), '', 59 );
		add_submenu_page( 'product', 'Orders', 'Orders', 'administrator', 'product_orders', array( 'product_admin', 'orders' ) );
		add_submenu_page( 'product', 'Transactions', 'Transactions', 'administrator', 'product_transactions', array( 'product_admin', 'transactions' ) );
		add_submenu_page( 'product', 'Coupons', 'Coupons', 'administrator', 'product_coupons', array( 'product_admin', 'coupons' ) );
	}
}
add_action( 'admin_menu', array('product_admin', 'init') );



/*
@@ Función: parse_from_format @@
Permite hacer un parse de la fecha y hora ingresada antes de
realizar el mktime()
*/
function parse_from_format($format, $date) {
  $dMask = array(
    'H'=>'hour',
    'i'=>'minute',
    's'=>'second',
    'y'=>'year',
    'm'=>'month',
    'd'=>'day'
  );
  $format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY);  
  $date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY);  
  foreach ($date as $k => $v) {
    if ($dMask[$format[$k]]) $dt[$dMask[$format[$k]]] .= $v;
  }
  return $dt;
}
?>
