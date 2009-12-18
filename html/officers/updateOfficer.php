<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST officer_id
 */
verifyUser(array('Administrator','Clerk'));

$officer = new Officer($_REQUEST['officer_id']);
if (isset($_POST['officer'])) {
	foreach ($_POST['officer'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$officer->$set($value);
	}

	try {
		$officer->save();
		header('Location: '.$officer->getCommittee()->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Edit Office';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$officer->getCommittee()));
$template->blocks[] = new Block('officers/updateOfficerForm.inc',array('officer'=>$officer));
echo $template->render();
