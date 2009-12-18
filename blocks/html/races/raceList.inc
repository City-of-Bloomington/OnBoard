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
				echo "<a class=\"add button\" href=\"".BASE_URL."/races/addRace.php\">Add</a>";
			}
		?>
		Races
	</h1>
	<ul><?php
			foreach ($this->raceList as $race) {
				$editButton = '';
				if (userHasRole(array('Administrator','Clerk'))) {
					$url = new URL(BASE_URL.'/races/updateRace.php');
					$url->race_id = $race->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}
				echo "<li>$editButton $race</li>";
			}
		?>
	</ul>
</div>
