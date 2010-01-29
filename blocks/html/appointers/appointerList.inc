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
				$addURL = new URL(BASE_URL.'/appointers/addAppointer.php');
				echo "
				<button type=\"button\" class=\"add\" onclick=\"document.location.href='$addURL';\">
				Add
				</button>
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
					$editURL = new URL(BASE_URL.'/appointers/updateAppointer.php');
					$editURL->appointer_id = $appointer->getId();
					$editButton = "
					<button type=\"button\" class=\"edit\" onclick=\"document.location.href='$editURL';\">
						Edit
					</button>
					";
				}
		?>
	</ul>
</div>
