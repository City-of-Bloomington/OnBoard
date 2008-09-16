<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param GET requirement_id
 */
verifyUser('Administrator');

$requirement = new Requirement($_REQUEST['requirement_id']);
if (isset($_POST['requirement']))
{
	foreach($_POST['requirement'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$requirement->$set($value);
	}

	try
	{
		$requirement->save();
		Header('Location: home.php');
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('requirements/updateRequirementForm.inc',array('requirement'=>$requirement));
echo $template->render();