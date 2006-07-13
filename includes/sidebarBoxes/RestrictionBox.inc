<div class="sidebar">
	<div class="titleBar">Restrictions</div>
	<ul>
		<?php if (isset($_SESSION['USER'])) { $id = $_SESSION['USER']->getId(); } ?>
			<li><a href="<?php echo BASE_URL; ?>/restrictions/addRestrictionForm.php">Add Restrictions</a></li>
			<li><a href="<?php echo BASE_URL; ?>/restrictions/deleteRestrictionForm.php">Delete Restrictions</a></li>
	</ul>
</div>