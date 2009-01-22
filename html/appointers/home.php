<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
verifyUser(array('Administrator','Clerk'));

$appointerList = new AppointerList();
$appointerList->find();

$template = new Template();
$template->blocks[] = new Block('appointers/appointerList.inc',array('appointerList'=>$appointerList));
echo $template->render();