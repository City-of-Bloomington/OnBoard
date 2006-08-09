<div class="menuBar">
	<?php if (isset($_SESSION['USER'])) { echo "<a href=\"".BASE_URL."/logout.php\">Logout</a>"; }
	      else {echo "<a href=\"".BASE_URL."\">Home</a><a href=\"".BASE_URL."/applications/applicationForm.php\">Apply for Board or Commission</a>"; } ?>
</div>
