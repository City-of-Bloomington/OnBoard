<?php
/**
 * @copyright Copyright (C) 2007-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser('Administrator');

if (isset($_POST['race']))
{
	$race = new Race();
	foreach($_POST['race'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$race->$set($value);
	}

	try
	{
		$race->save();
		Header('Location: '.BASE_URL.'/races');
		exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('races/addRaceForm.inc');
echo $template->render();