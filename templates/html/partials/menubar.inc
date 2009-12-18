<div class="menuBar">
	<ul><li><a href="<?php echo BASE_URL; ?>">Home</a></li>
		<?php
			if (isset($_SESSION['USER'])) {
				echo "
				<li><a href=\"".BASE_URL."/login/logout.php\">Logout</a></li>
				<li><a href=\"".BASE_URL."/tags\">Tags</a></li>
				<li><a href=\"".BASE_URL."/people\">People</a></li>
				<li><a href=\"".BASE_URL."/appointers\">Appointers</a></li>
				<li><a href=\"".BASE_URL."/committees\">Committees</a></li>
				<li><a href=\"".BASE_URL."/requirements\">Requirements</a></li>
				<li><a href=\"".BASE_URL."/topicTypes\">Legislation Types</a></li>
				<li><a href=\"".BASE_URL."/voteTypes\">Vote Types</a></li>
				<li><a href=\"".BASE_URL."/races\">Races</a></li>
				<li><a href=\"".BASE_URL."/tags\">Tags</a></li>
				";
			}
			else {
				echo "<li><a href=\"".BASE_URL."/login\">Login</a></li>";
			}

			if (userHasRole('Administrator')) {
				echo "
				<li><a href=\"".BASE_URL."/users\">User Accounts</a></li>
				";
			}
		?>
	</ul>
</div>
