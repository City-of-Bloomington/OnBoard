<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET term_id
 */

verifyUser(array('Administrator','Clerk'));

$term = new Term($_REQUEST['term_id']);
if (isset($_POST['term'])) {
	foreach ($_POST['term'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$term->$set($value);
	}

	try {
		$term->save();
		header('Location: '.$term->getSeat()->getCommittee()->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Term';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$term->getSeat()->getCommittee()));
$template->blocks[] = new BlocK('seats/seatInfo.inc',array('seat'=>$term->getSeat()));
$template->blocks[] = new Block('terms/updateTermForm.inc',array('term'=>$term));
echo $template->render();
