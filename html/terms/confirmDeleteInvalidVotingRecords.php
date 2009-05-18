<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SESSION pendingTerm
 * @param GET confirm
 * @param GET return_url
 */
if (isset($_GET['confirm'])) {
	try {
		$_SESSION['pendingTerm']->save();
		$url = $_GET['return_url'];
	}
	catch (Exception $e) {
		$url = new URL(BASE_URL.'/terms/updateTerm.php');
		$url->term_id = $_SESSION['pendingTerm']->getId();
		$url->return_url = $_GET['return_url'];
	}
	unset($_SESSION['pendingTerm']);
	header("Location: $url");
	exit();
}

$template = new Template();
$template->title = 'Edit Term';
$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$_SESSION['pendingTerm']->getSeat()->getCommittee()));
$template->blocks[] = new BlocK('seats/seatInfo.inc',array('seat'=>$_SESSION['pendingTerm']->getSeat()));
$template->blocks[] = new Block('terms/confirmDeleteInvalidVotingRecords.inc',
								array('term'=>$_SESSION['pendingTerm'],
									  'return_url'=>$_GET['return_url']));
echo $template->render();
