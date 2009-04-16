<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param RequirementList $this->requirementList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole(array('Administrator','Clerk'))) {
				echo "
				<a class=\"add button\" href=\"".BASE_URL."/requirements/addRequirement.php\">
					Add
				</a>
				";
			}
		?>
		Requirements
	</h1>
	<ul><?php
			foreach ($this->requirementList as $requirement)
			{
				$text = View::escape($requirement);
				$editButton = '';
				$deleteButton = '';
				if (userHasRole(array('Administrator','Clerk')))
				{
					$url = new URL(BASE_URL.'/requirements/updateRequirement.php');
					$url->requirement_id = $requirement->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";

					$url = new URL(BASE_URL.'/requirements/deleteRequirement.php');
					$url->requirement_id = $requirement->getId();
					$deleteButton = "
					<button type=\"button\" class=\"delete\" onclick=\"FRAMEWORK.deleteConfirmation('$url');\">
						Delete
					</button>
					";
				}
				echo "
				<li>$editButton
					$deleteButton
					$text
				</li>
				";
			}
		?>
	</ul>
</div>
