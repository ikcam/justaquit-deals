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

		if( isset($_GET['action']) && $_GET['action'] == 'add' ):
?>
<div class="wrap">
	<h2>Add Provider</h2>
	<form method="post" action="?page=deals_providers">
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="name">Name</label></th>
				<td><input type="text" id="name" name="name" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="shortname">Shortname</label></th>
				<td><input type="text" id="shortname" name="shortname" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="password">Password</label></th>
				<td><input type="text" id="password" name="password" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="email">E-mail</label></th>
				<td><input type="email" id="email" name="email" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="address">Address</label></th>
				<td><textarea id="address" name="address"></textarea><span class="description">You can use HTML here</span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="phone">Phone</label></th>
				<td><input type="text" id="phone" name="phone" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="url_site">Site URL</label></th>
				<td><input type="url" id="url_site" name="url_site" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="url_fb">FB URL</label></th>
				<td><input type="url" id="url_fb" name="url_fb" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="location_lat">Location Latitude</label></th>
				<td><input type="text" id="location_lat" name="location_lat" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="location_long">Location Longitude</label></th>
				<td><input type="text" id="location_long" name="location_long" /></td>
			</tr>
		</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Add New') ?>"></p>
	</form>
</div>
<?php
		elseif( isset($_GET['action']) && isset($_GET['ID']) && $_GET['action'] == 'edit' ):
			if( isset($_POST['submit']) ):
				$provider = new Provider( $_POST['name'], $_POST['shortname'], $_POST['password'], $_POST['email'], $_POST['address'], $_POST['phone'], $_POST['url_site'], $_POST['url_fb'], $_POST['location_lat'], $_POST['location_long'] );

				if( $provider->update_provider($_GET['ID']) ):
?>
	<div id="message" class="updated">
		<p>Provider updated succesfuly.</p>
	</div>
<?php
				else:
?>
	<div id="message" class="error">
		<p>Error updating the current provider.</p>
	</div>
<?php
				endif;
			endif;

			$provider = get_provider($_GET['ID']);
?>
<div class="wrap">
	<h2>Edit Provider</h2>
	<form method="post" action="?page=deals_providers&amp;action=edit&amp;ID=<?php echo $_GET['ID'] ?>">
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="name">Name</label></th>
				<td><input type="text" id="name" name="name" value="<?php echo $provider->name ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="shortname">Shortname</label></th>
				<td><input type="text" id="shortname" name="shortname" value="<?php echo $provider->shortname ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="password">Password</label></th>
				<td><input type="text" id="password" name="password" value="<?php echo $provider->password ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="email">E-mail</label></th>
				<td><input type="email" id="email" name="email" value="<?php echo $provider->email ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="address">Address</label></th>
				<td><textarea id="address" name="address"><?php echo $provider->address ?></textarea><span class="description">You can use HTML here</span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="phone">Phone</label></th>
				<td><input type="text" id="phone" name="phone" value="<?php echo $provider->phone ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="url_site">Site URL</label></th>
				<td><input type="url" id="url_site" name="url_site" value="<?php echo $provider->url_site ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="url_fb">FB URL</label></th>
				<td><input type="url" id="url_fb" name="url_fb" value="<?php echo $provider->url_fb ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="location_lat">Location Latitude</label></th>
				<td><input type="text" id="location_lat" name="location_lat" value="<?php echo $provider->location_lat ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="location_long">Location Longitude</label></th>
				<td><input type="text" id="location_long" name="location_long" value="<?php echo $provider->location_long ?>" /></td>
			</tr>
		</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Edit') ?>"></p>
	</form>
</div>
<?php
		else:
?>
<div class="wrap">
<?php
		if( isset($_POST['submit']) ):
			$provider = new Provider( $_POST['name'], $_POST['shortname'], $_POST['password'], $_POST['email'], $_POST['address'], $_POST['phone'], $_POST['url_site'], $_POST['url_fb'], $_POST['location_lat'], $_POST['location_long'] );

			$provider->add_provider();
?>
	<div id="message" class="updated">
		<p>Provider added succesfuly. <a href="?page=deals_providers&amp;action=edit&amp;ID=<?php echo $provider->ID ?>">Edit</a></p>
	</div>
<?php
		elseif ( isset($_GET['action']) && isset($_GET['ID']) && $_GET['action'] == 'delete' ):
			if( Provider::delete_provider($_GET['ID']) ):
?>
	<div id="message" class="updated">
		<p>The provider has been deleted succesfuly.</p>
	</div>
<?php
			else:
?>
	<div id="message" class="error">
		<p>An error roccured while we try to delete the current provider.</p>
	</div>
<?php
			endif;
		endif;
?>
	<h2>Providers<a href="?page=deals_providers&amp;action=add" class="add-new-h2">Add New</a></h2>

	<table class="wp-list-table widefat fixed providers" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="address" class="manage-column column-address"><span>Address</span></th>
				<th scope="col" id="phone" class="manage-column column-phone"><span>Phone</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>Site URL</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>FB URL</span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="name" class="manage-column column-name"><span>Name</span></th>
				<th scope="col" id="email" class="manage-column column-email"><span>Email</span></th>
				<th scope="col" id="address" class="manage-column column-address"><span>Address</span></th>
				<th scope="col" id="phone" class="manage-column column-phone"><span>Phone</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>Site URL</span></th>
				<th scope="col" id="url" class="manage-column column-url"><span>FB URL</span></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
<?php
	$providers = get_providers();
	foreach( $providers as $provider ):
?>
			<tr>
				<td class="name column-name">
					<strong><?php echo $provider->name ?></strong>
					<div class="row-actions">
						<span class="edit"><a href="?page=deals_providers&amp;action=edit&amp;ID=<?php echo $provider->ID ?>" title="Edit">Edit</a> | </span>
						<span class="trash"><a class="submitdelete" title="Editar" href="?page=deals_providers&amp;action=delete&amp;ID=<?php echo $provider->ID ?>" onclick="if ( confirm( 'You are about to delete the provider: \'<?php echo $provider->name ?>\'\n \'Cancel\' to return, \'Accept\' to erase.' ) ) { return true;}return false;">Delete</a></span>
					</div>
				</td>
				<td class="email column-email"><?php echo $provider->email ?></td>
				<td class="address column-address"><?php echo $provider->address ?></td>
				<td class="phone column-phone"><?php echo $provider->phone ?></td>
				<td class="status column-url"><a href="<?php echo $provider->url_site ?>" target="_blank"><?php echo $provider->url_site ?></a></td>
				<td class="status column-url"><a href="<?php echo $provider->url_fb ?>" target="_blank"><?php echo $provider->url_fb ?></a></td>
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

	public function add(){
		add_submenu_page('deals', 'Providers', 'Providers', 'administrator', 'deals_providers', array($this, 'page'));
	}
}

$init = new Providers();
?>