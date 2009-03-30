<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$committees = new CommitteeList();
$committees->find();

$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$template = new Template('default',$format);
$template->title = 'Boards & Commissions';

$template->blocks[] = new Block('committees/breadcrumbs.inc');

$template->blocks[] = new Block('committees/committeeList.inc',array('committeeList'=>$committees));

echo $template->render();
