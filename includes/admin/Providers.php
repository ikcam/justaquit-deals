<?php
if(!defined('JUSTAQUIT_DEALS')){
	echo 'Hello world.';
	die();
}

Class Providers extends Deals{
	public function __construct(){
		add_action('admin_menu', array($this, 'add'));
	}

public function page(){
		$settings = get_option('justaquit_deals');
		if( !isset($_GET['action']) ):
?>
<div class="wrap">
	<h2>Providers</h2>

	<table class="wp-list-table widefat fixed providers" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="address" class="manage-column column-address"><span>Address</span></th>
				<th scope="col" id="phone" class="manage-column column-phone"><span>Phone</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>Site URL</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>FB URL</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="id" class="manage-column column-id"><span>ID</span></th>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="address" class="manage-column column-address"><span>Address</span></th>
				<th scope="col" id="phone" class="manage-column column-phone"><span>Phone</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>Site URL</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>FB URL</span></th>
				<th scope="col" id="options" class="manage-column column-options"><span>Options</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	$providers = get_providers();
	foreach( $providers as $provider ):
?>
			<tr>
				<td class="id column-id"><?php echo $provider->ID ?></td>
				<td class="name column-name"><?php echo $provider->name ?></td>
				<td class="email column-email">$<?php echo $provider->email ?></td>
				<td class="address column-address"><?php echo $provider->address ?></td>
				<td class="phone column-phone"><?php echo $provider->phone ?></td>
				<td class="status column-url"><?php echo $provider->url_site ?></td>
				<td class="status column-url"><?php echo $provider->url_fb ?></td>
				<td class="options column-options">Not yet</td>
			</tr>
<?php
	endforeach;
?>
		</tbody>
	</table>
</div>
<?php
		else:
			echo 'Action';
		endif;
	}

	public function add(){
		add_submenu_page('deals', 'Providers', 'Providers', 'administrator', 'deals_providers', array($this, 'page'));
	}
}

$init = new Providers();
?>