<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$requirementList = new RequirementList();
$requirementList->find();

$template = new Template();
$template->blocks[] = new Block('requirements/requirementList.inc',array('requirementList'=>$requirementList));
echo $template->render();