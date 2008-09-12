<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$committeeList = new CommitteeList();
$committeeList->find();

$template = new Template();
$template->blocks[] = new Block('committees/committeeList.inc',array('committeeList'=>$committeeList));
echo $template->render();