<div class="menuBar">
	<ul><li><a href="<?php echo BASE_URL; ?>">Home</a></li>
	<?php
		if (isset($_SESSION['USER'])) { echo "<li><a href=\"".BASE_URL."/logout.php\">Logout</a></li>"; }
		else { echo "<li><a href=\"".BASE_URL."/applications/applicationForm.php\">Apply for Board or Commission</a></li>"; }
	?>
	</ul>
</div>