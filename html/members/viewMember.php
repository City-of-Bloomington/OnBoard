<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET member_id
 */
$member = new Member($_GET['member_id']);

$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$template = new Template('default',$format);
$template->title = $member->getFullname();

$template->blocks[] = new Block('members/personalInfo.inc',array('member'=>$member));

echo $template->render();
