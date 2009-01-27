<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser(array('Administrator','Clerk'));

if (isset($_POST['committee']))
{
	$committee = new Committee();
	foreach ($_POST['committee'] as $field=>$value)
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
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->title = 'Add a new committee';
$template->blocks[] = new Block('committees/addCommitteeForm.inc');
echo $template->render();
