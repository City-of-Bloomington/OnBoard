<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
verifyUser(array('Administrator','Clerk'));

$appointerList = new AppointerList();
$appointerList->find();

$template = new Template();
$template->title = 'Appointers';
$template->blocks[] = new Block('appointers/appointerList.inc',
								array('appointerList'=>$appointerList));
echo $template->render();
