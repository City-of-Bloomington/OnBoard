<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

$termList = new TermList();
$termList->find();

$template = new Template();
$template->blocks[] = new Block('terms/termList.inc',array('termList'=>$termList));
echo $template->render();