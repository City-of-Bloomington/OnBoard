<div class="sidebar">
	<div class="titleBar">Profile</div>
	<ul>
		<?php if (isset($_SESSION['USER'])) { $id = $_SESSION['USER']->getId(); } ?>
		<li><a href="<?php echo BASE_URL; ?>/profile/updateProfileForm.php?id=<?php echo $id; ?>">Edit Profile</a></li>
	</ul>
</div>