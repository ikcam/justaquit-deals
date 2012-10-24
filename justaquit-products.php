<?php
class product_functions {
	/*
	@@ Función: get_categories @@
	Args:
		- (Int) count: Número de elementos requeridos
	*/
	public function get_categories( $count=10 ){
		// Globalize $wpdb for SQL queries
		global $wpdb;
		// Vars
		$time_current = strtotime( current_time('mysql') );
		// Query
		$query = "SELECT * FROM $wpdb->terms WHERE term_id IN ( SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id IN ( SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ) ) AND term_id IN ( SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' AND parent = 0 ) ORDER BY term_id DESC LIMIT 0,%d";
		$categories = $wpdb->get_results( $wpdb->prepare( $query, $count ) );
		// Result
		if( !$categories )
			return false;andy
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

	
} // End of class: product_functions
add_action( 'wp_head', array('product_functions', 'views_count') );

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

class product_admin {
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
<?php
		endif;
	}

}
?>
