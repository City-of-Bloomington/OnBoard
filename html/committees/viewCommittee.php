<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET committee_id
 */
$committee = new Committee($_GET['committee_id']);

$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$template = new Template('default',$format);
$template->title = $committee->getName();

if ($template->outputFormat == 'html') {
	$template->blocks[] = new Block('committees/breadcrumbs.inc',array('committee'=>$committee));
}
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$committee));


// Don't bother showing the tabs if there are no topics for this committee
if ($committee->hasTopics()) {
	$tabs = array('members'=>'Members','topics'=>'Legislation','votes'=>'Votes');
	$current_tab = isset($_GET['tab']) && array_key_exists($_GET['tab'],$tabs) ? $_GET['tab'] : 'members';
	$template->blocks[] = new Block('tabs.inc',array('tabs'=>$tabs,'current_tab'=>$current_tab));
}
else {
	$current_tab = 'members';
}

// Add all the blocks for each tab
// One case for each possible tab
switch ($current_tab) {
	case 'members':
		$block = (isset($_GET['members']) && $_GET['members']=='past')
				? 'pastMembers.inc'
				: 'currentTerms.inc';
		$template->blocks[] = new Block("committees/$block",array('committee'=>$committee));
		break;

	case 'topics':
		// Only allow the valid sort values
		$sort = null;
		if (isset($_GET['sort'])) {
			switch ($_GET['sort']) {
				case 'date':
				case 'date desc':
				case 'number':
				case 'number desc':
					$sort = $_GET['sort'];
					break;
				default:
					$sort = null;
			}
		}

		$topics = $committee->getTopics(null,$sort);
		$people = array();
		foreach($committee->getCurrentTerms() as $term) {
			$people[] = $term->getPerson();
		}
		$template->blocks[] = new Block('topics/tagCloud.inc',array('topicList'=>$topics));

		// Paginate the topics if there's alot of them
		if (count($topics) > 15) {
			$pages = new Paginator($topics,15);
			$page = (isset($_GET['page']) && $_GET['page'])
					? (int)$_GET['page']
					: 0;
			if (!$pages->offsetExists($page)) {
				$page = 0;
			}
			$topicList = new LimitIterator($topics,$pages[$page],$pages->getPageSize());
		}
		else {
			$topicList = $topics;
		}
		$template->blocks[] = new Block('topics/topicList.inc',
										array('topicList'=>$topicList,'committee'=>$committee));

		if (isset($pages)) {
			$pageNavigation = new Block('pageNavigation.inc');
			$pageNavigation->page = $page;
			$pageNavigation->pages = $pages;
			$pageNavigation->url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

			$template->blocks[] = $pageNavigation;
		}
		break;

	case 'votes':
		$topics = $committee->getTopics();
		$people = array();
		foreach($committee->getCurrentTerms() as $term) {
			$people[] = $term->getPerson();
		}

		$votingComparison = new Block('votingRecords/votingRecordComparison.inc');
		$votingComparison->topicList = $topics;
		$votingComparison->people = $people;
		$template->blocks[] = $votingComparison;
		break;
}

echo $template->render();
