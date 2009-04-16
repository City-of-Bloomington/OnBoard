<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST tag_id
 */
verifyUser(array('Administrator','Clerk'));

$tag = new Tag($_REQUEST['tag_id']);

if (isset($_POST['tag'])) {
	foreach ($_POST['tag'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$tag->$set($value);
	}

	try {
		$tag->save();
		header('Location: '.BASE_URL.'/tags');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Update Tag';
$template->blocks[] = new Block('tags/updateTagForm.inc',array('tag'=>$tag));
echo $template->render();
