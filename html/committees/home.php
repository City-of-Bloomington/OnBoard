<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$template = new Template('default',$format);

$template->blocks[] = new Block('committees/breadcrumbs.inc');

$committees = new CommitteeList();
$committees->find();
$list = new Block('committees/committeeList.inc');
$list->committeeList = $committees;
$template->blocks[] = $list;

echo $template->render();
