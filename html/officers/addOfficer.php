<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST committee_id
 * @param REQUEST person_id
 */

verifyUser(array('Administrator','Clerk'));
$committee = new Committee($_REQUEST['committee_id']);
$person = new Person($_REQUEST['person_id']);

if (isset($_POST['officer'])) {
	$officer = new Officer();
	$officer->setCommittee($committee);
	$officer->setPerson($person);

	foreach ($_POST['officer'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$officer->$set($value);
	}

	try {
		$officer->save();
		header('Location: '.$committee->getURL());
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add Office';
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$committee));
$template->blocks[] = new Block('officers/addOfficerForm.inc',
								array('committee'=>$committee,'person'=>$person));
echo $template->render();
