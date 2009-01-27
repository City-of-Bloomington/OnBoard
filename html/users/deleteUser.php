<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET id
 */
verifyUser('Administrator');

$user = new User($_GET['id']);
$user->delete();

header('Location: '.BASE_URL.'/users');
