<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class shortcode_checkout{
	public function __construct(){
		add_action('wp_enqueue_scripts', array($this, 'scripts'));
		add_shortcode('checkout', array($this, 'shortcode'));
	}

	public function shortcode(){
		if( isset($_POST['the_id']) )
			$ID  = $_POST['the_id'];
		else
			return;
		if( isset($_POST['time_current']) )
			$product_time_buy = $_POST['time_current'];
		else
			return;

		$server_time_buy = strtotime( current_time('mysql') );

		$time_remain = 120 - ( $server_time_buy - $product_time_buy );

		if( $time_remain <= 0 && is_active($ID) ):
			echo '<div class="countdown">Sorry, your time has expire, you have to place again your order.</div>';
			return;
		else :
			$post = get_post($ID);
			$total = 0;
?>
	<form action="<?php bloginfo('url') ?>/store/transaction" method="post">
		<?php wp_nonce_field('checkout', 'deals_checkout') ?>
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
						$total = $total + get_price($post->ID);
						$total = floor($total*100)/100;
						echo get_price($post->ID);
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
<?php if(is_active($ID)) : ?>
	<div class="countdown">
		Hurry up! You have <span id="countdown"><?php echo $time_remain ?></span> seconds left.
	</div>
<?php endif; ?>
<?php
		endif;
	}

	public function scripts(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-timer', plugin_dir_url(__FILE__).'../../javascript/timer.jquery.js');
		wp_enqueue_script( 'jquery-checkout', plugin_dir_url(__FILE__).'../../javascript/checkout.jquery.js');
	}

	public function stylesheets(){
		wp_register_style( 'style-checkout', plugins_url('css/checkout.css', __FILE__) );
		wp_enqueue_style( 'style-checkout' );
	}
}
?>