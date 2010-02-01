<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param RaceList $this->raceList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole(array('Administrator','Clerk'))) {
				echo "
				<button type=\"button\" class=\"add\" onclick=\"document.location.href='".BASE_URL."/races/addRace.php';\">
				Add
				</button>";
			}
		?>
		Races
	</h1>
	<ul>
	<?php
			foreach ($this->raceList as $race) {
				$editButton = '';
				if (userHasRole(array('Administrator','Clerk'))) {
					$url = new URL(BASE_URL.'/races/updateRace.php');
					$url->race_id = $race->getId();
					$editButton = "
					<button type=\"button\" class=\"edit\" onclick=\"document.location.href='$url';\">
					Edit
					</button>
					";
				}
				echo "<li>$editButton $race</li>";
			}
		?>
	</ul>
</div>
