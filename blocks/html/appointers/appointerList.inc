<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			if (userHasRole('Administrator'))
			{
				echo "<a class=\"add button\" href=\"".BASE_URL."/appointers/addAppointer.php\">Add</a>";
			}
		?>
		Appointers
	</h1>
	<p>Seats on boards &amp; committees are appointed.  This is the list of appointers known to the system.
		When creating a seat, an entry from this list must be chosen.</p>
	<ul><?php
			foreach($this->appointerList as $appointer)
			{
				$name = View::escape($appointer->getName());

				$editButton = userHasRole('Administrator') ? "<a class=\"edit button\" href=\"".BASE_URL."/appointers/updateAppointer.php?appointer_id={$appointer->getId()}\">Edit</a>" : '';
				echo "<li>$editButton $name</li>";
			}
		?>
	</ul>
</div>