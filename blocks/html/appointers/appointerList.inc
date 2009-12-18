<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param AppointerList $this->appointerList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole(array('Administrator','Clerk'))) {
				echo "
				<a class=\"add button\" href=\"".BASE_URL."/appointers/addAppointer.php\">
					Add
				</a>
				";
			}
		?>
		Appointers
	</h1>
	<p>Seats on boards &amp; committees are appointed.  This is the list of
		appointers known to the system.  When creating a seat, an entry from
		this list must be chosen.</p>
	<ul><?php
			foreach ($this->appointerList as $appointer) {
				$editButton = '';
				if (userHasRole(array('Administrator','Clerk'))) {
					$url = new URL(BASE_URL.'/appointers/updateAppointer.php');
					$url->appointer_id = $appointer->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}
				echo "<li>$editButton $appointer</li>";
			}
		?>
	</ul>
</div>
