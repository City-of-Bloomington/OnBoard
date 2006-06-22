<?php
/*
	$_GET variables:	id
*/

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");

?>
<div id="mainContent">
	<?php
		include(GLOBAL_INCLUDES."/errorMessages.inc");
    
		$user = new User($_GET['id']); 
		
	?>
	<h1><?php echo $user->getFirstname() . " " . $user->getLastname(); ?>'s Profile</h1>
		<fieldset><legend>Profile Info</legend>
			<input name="id" type="hidden" value="<?php echo $user->getId(); ?>" />
			<table>
				<tr><th>Field Name</th><th>Personal Information</th></tr>
				<tr><td><label>First Name</label></td>
						<td><?php echo $user->getFirstname(); ?></td>
				</tr>
				<tr><td><label>Last Name</label></td>
						<td><?php echo $user->getLastname(); ?></td>
				</tr>
				<tr><td><label>Email Address</label></td>
						<td><?php echo $user->getEmail(); ?></td>
				</tr>
				
				<tr><td><label>Home Phone Number</label></td>
						<td><?php echo $user->getHomePhone(); ?></td>
				</tr>
				
				<tr><td><label>Work Phone Number</label></td>
						<td><?php echo $user->getWorkPhone(); ?></td>
				</tr>
				
				<tr><td><?php echo $user->getFirstname() . " " . $user->getLastname(); ?>'s Address</td></tr>
				<tr><td><label>Street</label></td>
						<td><?php echo $user->getStreet(); ?></td>
				</tr>
				
				<tr><td><label>City, State</label></td>
						<td><?php echo $user->getCity(); ?></td>
				</tr>
				
				<tr><td><label>Zip Code</label></td>
						<td><?php echo $user->getZipCode(); ?></td>
				</tr>
				<?php 
					$seatList = new SeatList();
					$seatList->find();
					$count = 0;
					foreach($seatList as $seat) 
					{
						if ($seat->getUser() == $user) {
							$label = "Committees";
							$count += 1;
							if ($count > 1) { $label = ""; }
							echo "<tr><td><label>$label</label></td><td>{$seat->getCommission()->getName()}</td>"; 
						}
					} 
					?>
				</tr>
				<tr><td><label>About <?php echo $user->getFirstname() . " " . $user->getLastname(); ?></label></td>
						<td><?php echo $user->getAbout(); ?></td>
				</tr>
			</table>
		</fieldset>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>