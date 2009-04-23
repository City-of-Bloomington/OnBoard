<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET term_id
 * @param GET return_url
 */
verifyUser(array('Administrator','Clerk'));

$term = new Term($_GET['term_id']);
if ($term->isSafeToDelete()) {
	$term->delete();
}

header("Location: $_GET[return_url]");
