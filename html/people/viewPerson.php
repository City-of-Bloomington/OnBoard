<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
$person = new Person($_GET['person_id']);

$template = new Template();
$template->title = $person->getFullname();
$template->blocks[] = new Block('people/personInfo.inc',array('person'=>$person));

$terms = $person->getTerms();
if (count($terms)) {
	$tabs = array('info','votes');
	$current_tab = (isset($_GET['tab']) && in_array($_GET['tab'],$tabs)) ? $_GET['tab'] : 'info';
	$template->blocks[] = new Block('tabs.inc',array('tabs'=>$tabs,'current_tab'=>$current_tab));
	$template->blocks[] = new Block("people/tabs/$current_tab.inc",array('person'=>$person));
}

echo $template->render();
