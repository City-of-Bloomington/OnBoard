<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser('Administrator');

$committee = new Committee($_REQUEST['id']);
if (isset($_POST['committee']))
{
	foreach($_POST['committee'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$committee->$set($value);
	}

	try
	{
		$committee->save();
		Header('Location: home.php');
		exit();
	}
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('committees/updateCommitteeForm.inc',array('committee'=>$committee));
echo $template->render();