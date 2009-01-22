<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET tag_id
 */
verifyUser(array('Administrator','Clerk'));

$tag = new Tag($_REQUEST['tag_id']);

if (isset($_POST['tag']))
{
	foreach ($_POST['tag'] as $field=>$value)
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
	catch (Exception $e) { $_SESSION['errorMessages'][] = $e; }
}

$template = new Template();
$template->blocks[] = new Block('tags/updateTagForm.inc',array('tag'=>$tag));
echo $template->render();
