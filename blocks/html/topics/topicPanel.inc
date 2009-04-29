<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET sort
 * @param TopicList $this->topicList
 */
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

// We only want to display topics for a single year, chosen by the user
// Lets create a new list, only including a single years worth of topics
$years = $this->topicList->getYears();
$years = count($years) ? $years : array(date('Y'));
$displayYear = (isset($_GET['year']) && ctype_digit($_GET['year']))
			? $_GET['year']
			: $years[0];
$search = array('year'=>$displayYear);
$parameters = $this->topicList->getParameters();
if (array_key_exists(':committee_id',$parameters)) {
	$search['committee_id'] = $parameters[':committee_id'];
	$committee = new Committee($parameters[':committee_id']);
}
if (array_key_exists(':tag_id',$parameters)) {
	$search['tag_id'] = $parameters[':tag_id'];
	$tag = new Tag($parameters[':tag_id']);
}
$topics = new TopicList();
$topics->find($search,$sort);


// Display the tag cloud
if (isset($committee)) {
	$url = $committee->getURL();
	$url->tab = 'topics';
}
else {
	$url = new URL(BASE_URL.'/topics');
}
$tagCloud = new Block('tags/tagCloud.inc',
					  array('topicList'=>$topics,'base_url'=>$url));
echo $tagCloud->render();


// Display the years list and the sorting buttons
$userCanEdit = userHasRole(array('Administrator','Clerk'));
if (count($topics) || $userCanEdit) {
	$title = isset($tag) ? View::escape(ucfirst($tag->getName())) : '';

	$addTopicButton = '';
	if (isset($committee) && $userCanEdit) {
		$url = new URL(BASE_URL.'/topics/addTopic.php');
		$url->committee_id = $committee->getId();
		$addTopicButton = "<a class=\"add button\" href=\"$url\">Add</a>";
	}
	echo "
	<div class=\"interfaceBox legislation\">
		<h1>$addTopicButton $title Legislation</h1>
		<div class=\"years\">
	";
		$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		foreach ($years as $year) {
			$url->year = $year;
			$class = $year==$displayYear ? 'class="current"' : '';
			echo "<a href=\"$url\" $class>$year</a> ";
		}
	echo "</div>";

	$numberSortURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	if ($sort=='number') {
		$numberSortURL->sort = 'number desc';
		$numberDirection = 'down';
	}
	else {
		$numberSortURL->sort = 'number';
		$numberDirection = 'up';
	}

	$dateSortURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	if ($sort=='date') {
		$dateSortURL->sort = 'date desc';
		$dateDirection = 'down';
	}
	else {
		$dateSortURL->sort = 'date';
		$dateDirection = 'up';
	}
	echo "
		<div>
			<label>Sort:</label>
			<a href=\"$numberSortURL\" class=\"$numberDirection\">Number</a>
			<a href=\"$dateSortURL\" class=\"$dateDirection\">Date</a>
		</div>
	";


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


	// Display the topics
	$topicBlock = new Block('topics/topicList.inc',array('topicList'=>$topicList));
	echo $topicBlock->render();


	// Display the page navigation links
	if (isset($pages)) {
		$pageNavigation = new Block('pageNavigation.inc');
		$pageNavigation->page = $page;
		$pageNavigation->pages = $pages;
		$pageNavigation->url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

		echo $pageNavigation->render();
	}
}
