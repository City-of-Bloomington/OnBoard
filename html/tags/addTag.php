<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser(array('Administrator','Clerk'));

if (isset($_POST['tag']))
{
	$tag = new Tag();
	foreach($_POST['tag'] as $field=>$value)
	{
		$set = 'set'.ucfirst($field);
		$tag->$set($value);
	}

	try
	{
		$tag->save();
		Header('Location: '.BASE_URL.'/tags');
		exit();
	}
	catch(Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('tags/addTagForm.inc');
echo $template->render();
