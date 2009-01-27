<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$tagList = new TagList();
$tagList->find();

$template = new Template();
$template->title = 'Tags';
$template->blocks[] = new Block('tags/breadcrumbs.inc');
$template->blocks[] = new Block('tags/tagList.inc',array('tagList'=>$tagList));
echo $template->render();
