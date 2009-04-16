<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET requirement_id
 */
verifyUser(array('Administrator','Clerk'));

$requirement = new Requirement($_GET['requirement_id']);
$requirement->delete();
header('Location: '.BASE_URL.'/requirements');
