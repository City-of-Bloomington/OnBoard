<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser(array('Administrator','Clerk'));

$requirement = new Requirement($_GET['requirement_id']);
$requirement->delete();
Header('Location: '.BASE_URL.'/requirements');
