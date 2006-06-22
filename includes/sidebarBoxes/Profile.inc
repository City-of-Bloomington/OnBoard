<div class="sidebar">
	<div class="titleBar">Profile</div>
	<ul>
		<?php if (isset($_SESSION['USER'])) { $id = $_SESSION['USER']->getId(); } ?>
		<li><a href="<?php echo BASE_URL; ?>/users/updateUserForm.php?id=<?php echo $id; ?>">Edit My Profile</a></li>
	</ul>
</div>