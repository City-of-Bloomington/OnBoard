<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET member_id
 */
$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$template = new Template('default',$format);

$member = new Member($_GET['member_id']);
$template->blocks[] = new Block('members/personalInfo.inc',array('member'=>$member));

echo $template->render();
